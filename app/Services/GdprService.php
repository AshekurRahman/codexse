<?php

namespace App\Services;

use App\Models\GdprConsentLog;
use App\Models\GdprDataRequest;
use App\Models\GdprProcessingLog;
use App\Models\User;
use App\Notifications\GdprRequestCompleted;
use App\Notifications\GdprRequestSubmitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GdprService
{
    /**
     * Submit a new GDPR data request.
     */
    public function submitRequest(User $user, string $type, ?string $reason = null, ?array $categories = null): GdprDataRequest
    {
        // Check for existing pending request of same type
        $existingRequest = GdprDataRequest::where('user_id', $user->id)
            ->where('type', $type)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($existingRequest) {
            throw new \Exception("You already have a pending {$type} request.");
        }

        $request = GdprDataRequest::create([
            'user_id' => $user->id,
            'type' => $type,
            'reason' => $reason,
            'data_categories' => $categories ?? array_keys(GdprDataRequest::DATA_CATEGORIES),
            'identity_verified' => true, // Assume verified since user is logged in
        ]);

        // Log the request
        GdprProcessingLog::log(
            $user->id,
            GdprProcessingLog::ACTIVITY_REQUEST_SUBMITTED,
            GdprProcessingLog::CATEGORY_GDPR,
            "GDPR {$request->type_name} request submitted",
            $user->id,
            ['request_id' => $request->id, 'type' => $type]
        );

        // Notify user
        $user->notify(new GdprRequestSubmitted($request));

        return $request;
    }

    /**
     * Process a data export request.
     */
    public function processExportRequest(GdprDataRequest $request): string
    {
        $request->markAsProcessing();

        try {
            $user = $request->user;
            $exportData = $this->collectUserData($user, $request->data_categories ?? []);

            // Create export file
            $exportPath = $this->createExportArchive($user, $exportData);

            $request->complete($exportPath);

            // Log the export
            GdprProcessingLog::log(
                $user->id,
                GdprProcessingLog::ACTIVITY_DATA_EXPORT,
                GdprProcessingLog::CATEGORY_PERSONAL,
                "User data exported successfully",
                auth()->id(),
                ['request_id' => $request->id, 'categories' => $request->data_categories]
            );

            // Notify user
            $user->notify(new GdprRequestCompleted($request));

            return $exportPath;
        } catch (\Exception $e) {
            Log::error('GDPR export failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process a data deletion request.
     */
    public function processDeletionRequest(GdprDataRequest $request): void
    {
        $request->markAsProcessing();

        try {
            $user = $request->user;

            DB::transaction(function () use ($user, $request) {
                // Log before deletion
                GdprProcessingLog::log(
                    $user->id,
                    GdprProcessingLog::ACTIVITY_DATA_DELETION,
                    GdprProcessingLog::CATEGORY_PERSONAL,
                    "User account and data deletion initiated",
                    auth()->id(),
                    ['request_id' => $request->id, 'user_email' => $user->email]
                );

                // Anonymize user data instead of hard delete (for legal compliance)
                $this->anonymizeUserData($user);

                // Mark request as complete
                $request->complete();
            });

            // Note: User notification is skipped since account is anonymized

        } catch (\Exception $e) {
            Log::error('GDPR deletion failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Collect all user data for export.
     */
    public function collectUserData(User $user, array $categories = []): array
    {
        $data = [];

        // Personal Information
        if (empty($categories) || in_array('personal', $categories)) {
            $data['personal_information'] = [
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'website' => $user->website,
                'avatar' => $user->avatar_url,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at' => $user->created_at->toIso8601String(),
            ];
        }

        // Account Details
        if (empty($categories) || in_array('account', $categories)) {
            $data['account_details'] = [
                'is_seller' => $user->isSeller(),
                'is_admin' => $user->is_admin,
                'two_factor_enabled' => $user->hasTwoFactorEnabled(),
                'referral_code' => $user->referral_code,
                'referral_balance' => $user->referral_balance,
            ];

            if ($user->seller) {
                $data['seller_profile'] = [
                    'store_name' => $user->seller->store_name,
                    'store_slug' => $user->seller->slug,
                    'description' => $user->seller->description,
                    'status' => $user->seller->status,
                    'total_sales' => $user->seller->total_sales,
                    'rating' => $user->seller->rating,
                    'created_at' => $user->seller->created_at->toIso8601String(),
                ];
            }
        }

        // Order History
        if (empty($categories) || in_array('orders', $categories)) {
            $data['orders'] = $user->orders()->with('items.product')->get()->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name ?? 'Deleted Product',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'license_type' => $item->license_type,
                        ];
                    })->toArray(),
                    'created_at' => $order->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Financial Transactions
        if (empty($categories) || in_array('transactions', $categories)) {
            $data['wallet'] = [
                'balance' => $user->wallet?->balance ?? 0,
                'currency' => $user->wallet?->currency ?? 'USD',
            ];

            $data['wallet_transactions'] = $user->walletTransactions()->get()->map(function ($tx) {
                return [
                    'type' => $tx->type,
                    'amount' => $tx->amount,
                    'balance_after' => $tx->balance_after,
                    'description' => $tx->description,
                    'status' => $tx->status,
                    'created_at' => $tx->created_at->toIso8601String(),
                ];
            })->toArray();

            $data['referral_rewards'] = $user->referralRewards()->get()->map(function ($reward) {
                return [
                    'type' => $reward->type,
                    'amount' => $reward->amount,
                    'description' => $reward->description,
                    'status' => $reward->status,
                    'created_at' => $reward->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Communications
        if (empty($categories) || in_array('communications', $categories)) {
            $data['support_tickets'] = $user->supportTickets()->with('replies')->get()->map(function ($ticket) {
                return [
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'messages' => $ticket->replies->map(function ($reply) {
                        return [
                            'message' => $reply->message,
                            'is_from_user' => $reply->is_from_user,
                            'created_at' => $reply->created_at->toIso8601String(),
                        ];
                    })->toArray(),
                    'created_at' => $ticket->created_at->toIso8601String(),
                ];
            })->toArray();

            $data['conversations'] = $user->conversations()->with('messages')->get()->map(function ($conv) {
                return [
                    'with_seller' => $conv->seller?->store_name ?? 'Unknown',
                    'product' => $conv->product?->name ?? 'Unknown',
                    'messages' => $conv->messages->map(function ($msg) {
                        return [
                            'message' => $msg->message,
                            'is_from_buyer' => $msg->is_from_buyer,
                            'created_at' => $msg->created_at->toIso8601String(),
                        ];
                    })->toArray(),
                    'created_at' => $conv->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Reviews
        if (empty($categories) || in_array('reviews', $categories)) {
            $data['reviews'] = $user->reviews()->with('product')->get()->map(function ($review) {
                return [
                    'product_name' => $review->product->name ?? 'Deleted Product',
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Preferences
        if (empty($categories) || in_array('preferences', $categories)) {
            $data['preferences'] = [
                'marketing_consent' => $user->marketing_consent ?? false,
                'analytics_consent' => $user->analytics_consent ?? true,
                'third_party_consent' => $user->third_party_consent ?? false,
                'wishlist_public' => $user->wishlist_public,
            ];

            $data['wishlists'] = $user->wishlists()->with('product')->get()->map(function ($item) {
                return [
                    'product_name' => $item->product->name ?? 'Deleted Product',
                    'added_at' => $item->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Activity Logs
        if (empty($categories) || in_array('activity', $categories)) {
            $data['gdpr_processing_logs'] = GdprProcessingLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($log) {
                    return [
                        'activity' => $log->activity_type_name,
                        'category' => $log->data_category_name,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at->toIso8601String(),
                    ];
                })->toArray();

            $data['consent_history'] = GdprConsentLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($consent) {
                    return [
                        'type' => $consent->type_name,
                        'granted' => $consent->granted,
                        'granted_at' => $consent->granted_at?->toIso8601String(),
                        'revoked_at' => $consent->revoked_at?->toIso8601String(),
                    ];
                })->toArray();
        }

        // Downloads
        if (empty($categories) || in_array('downloads', $categories)) {
            $data['downloads'] = $user->downloads()->with('product')->get()->map(function ($download) {
                return [
                    'product_name' => $download->product->name ?? 'Deleted Product',
                    'downloaded_at' => $download->created_at->toIso8601String(),
                    'ip_address' => $download->ip_address,
                ];
            })->toArray();
        }

        // Subscriptions
        if (empty($categories) || in_array('subscriptions', $categories)) {
            $data['subscriptions'] = $user->subscriptions()->with('plan')->get()->map(function ($sub) {
                return [
                    'plan_name' => $sub->plan->name ?? 'Unknown Plan',
                    'status' => $sub->status,
                    'current_period_start' => $sub->current_period_start?->toIso8601String(),
                    'current_period_end' => $sub->current_period_end?->toIso8601String(),
                    'created_at' => $sub->created_at->toIso8601String(),
                ];
            })->toArray();
        }

        // Add metadata
        $data['export_metadata'] = [
            'exported_at' => now()->toIso8601String(),
            'data_categories' => $categories ?: array_keys(GdprDataRequest::DATA_CATEGORIES),
            'format_version' => '1.0',
        ];

        return $data;
    }

    /**
     * Create a ZIP archive of the user's data.
     */
    protected function createExportArchive(User $user, array $data): string
    {
        $filename = 'gdpr-export-' . $user->id . '-' . now()->format('Y-m-d-His') . '.zip';
        $directory = 'gdpr-exports';
        $zipPath = storage_path('app/' . $directory . '/' . $filename);

        // Ensure directory exists
        Storage::makeDirectory($directory);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create export archive');
        }

        // Add JSON data
        $zip->addFromString('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Add a readable HTML summary
        $zip->addFromString('summary.html', $this->generateHtmlSummary($user, $data));

        // Add README
        $zip->addFromString('README.txt', $this->generateReadme($user));

        $zip->close();

        return $directory . '/' . $filename;
    }

    /**
     * Generate HTML summary of the data.
     */
    protected function generateHtmlSummary(User $user, array $data): string
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        $html .= '<title>Your Data Export - ' . config('app.name') . '</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;max-width:800px;margin:0 auto;padding:20px;} ';
        $html .= 'h1,h2,h3{color:#333;} table{width:100%;border-collapse:collapse;margin:20px 0;} ';
        $html .= 'th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f5f5f5;}</style>';
        $html .= '</head><body>';

        $html .= '<h1>Your Data Export</h1>';
        $html .= '<p>Exported on: ' . now()->format('F j, Y \a\t g:i A') . '</p>';

        // Personal Information
        if (isset($data['personal_information'])) {
            $html .= '<h2>Personal Information</h2><table>';
            foreach ($data['personal_information'] as $key => $value) {
                $html .= '<tr><th>' . ucwords(str_replace('_', ' ', $key)) . '</th><td>' . htmlspecialchars($value ?? 'N/A') . '</td></tr>';
            }
            $html .= '</table>';
        }

        // Orders Summary
        if (isset($data['orders']) && count($data['orders']) > 0) {
            $html .= '<h2>Order History (' . count($data['orders']) . ' orders)</h2><table>';
            $html .= '<tr><th>Order #</th><th>Date</th><th>Status</th><th>Total</th></tr>';
            foreach (array_slice($data['orders'], 0, 20) as $order) {
                $html .= '<tr><td>' . $order['order_number'] . '</td>';
                $html .= '<td>' . date('M j, Y', strtotime($order['created_at'])) . '</td>';
                $html .= '<td>' . ucfirst($order['status']) . '</td>';
                $html .= '<td>$' . number_format($order['total'], 2) . '</td></tr>';
            }
            $html .= '</table>';
            if (count($data['orders']) > 20) {
                $html .= '<p><em>Showing first 20 orders. See data.json for complete list.</em></p>';
            }
        }

        $html .= '<h2>Complete Data</h2>';
        $html .= '<p>For complete data in machine-readable format, see the <code>data.json</code> file included in this archive.</p>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate README file content.
     */
    protected function generateReadme(User $user): string
    {
        return "DATA EXPORT - " . config('app.name') . "\n" .
            "=".str_repeat('=', strlen(config('app.name')) + 13) . "\n\n" .
            "This archive contains your personal data as requested under GDPR Article 15 (Right of Access).\n\n" .
            "Contents:\n" .
            "- data.json: Complete data in JSON format\n" .
            "- summary.html: Human-readable summary (open in browser)\n" .
            "- README.txt: This file\n\n" .
            "Export Details:\n" .
            "- User: " . $user->email . "\n" .
            "- Exported: " . now()->format('Y-m-d H:i:s') . " UTC\n" .
            "- Valid for: 7 days\n\n" .
            "If you have questions about this data, please contact our support team.\n\n" .
            "Your Rights Under GDPR:\n" .
            "- Right to Rectification (Article 16)\n" .
            "- Right to Erasure (Article 17)\n" .
            "- Right to Restriction of Processing (Article 18)\n" .
            "- Right to Data Portability (Article 20)\n";
    }

    /**
     * Anonymize user data (soft delete approach).
     */
    protected function anonymizeUserData(User $user): void
    {
        $anonymizedId = 'deleted_' . $user->id . '_' . time();

        // Anonymize user record
        $user->update([
            'name' => 'Deleted User',
            'email' => $anonymizedId . '@deleted.local',
            'password' => bcrypt(\Str::random(32)),
            'avatar' => null,
            'bio' => null,
            'website' => null,
            'google_id' => null,
            'facebook_id' => null,
            'github_id' => null,
            'twitter_id' => null,
            'social_avatar' => null,
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'referral_code' => $anonymizedId,
            'wishlist_share_token' => null,
            'gdpr_deletion_requested_at' => now(),
        ]);

        // Anonymize reviews
        $user->reviews()->update([
            'title' => 'Review removed',
            'comment' => 'This review has been removed at the user\'s request.',
        ]);

        // Delete conversations/messages
        $user->conversations()->delete();

        // Delete AI chat sessions
        $user->aiChatSessions()->delete();

        // Delete wishlists
        $user->wishlists()->delete();

        // Delete seller follows
        $user->followedSellers()->delete();

        // Keep order history (legal requirement) but anonymize where possible
        // Orders are kept for accounting/tax purposes

        // Delete support tickets content but keep structure
        foreach ($user->supportTickets as $ticket) {
            $ticket->replies()->update(['message' => 'Message removed upon user request.']);
        }

        // Log the anonymization
        Log::channel('gdpr')->info('User data anonymized', [
            'original_user_id' => $user->id,
            'anonymized_email' => $user->email,
        ]);
    }

    /**
     * Update consent preferences for a user.
     */
    public function updateConsent(User $user, array $consents): void
    {
        foreach ($consents as $type => $granted) {
            // Record consent change
            GdprConsentLog::recordConsent($user, $type, (bool) $granted);

            // Update user preferences
            if ($type === 'marketing') {
                $user->marketing_consent = (bool) $granted;
            } elseif ($type === 'analytics') {
                $user->analytics_consent = (bool) $granted;
            } elseif ($type === 'third_party') {
                $user->third_party_consent = (bool) $granted;
            }
        }

        $user->save();

        // Log the update
        GdprProcessingLog::log(
            $user->id,
            GdprProcessingLog::ACTIVITY_DATA_UPDATE,
            GdprProcessingLog::CATEGORY_PREFERENCES,
            "User consent preferences updated",
            $user->id,
            ['consents' => $consents]
        );
    }

    /**
     * Clean up expired export files.
     */
    public function cleanupExpiredExports(): int
    {
        $expired = GdprDataRequest::where('type', GdprDataRequest::TYPE_EXPORT)
            ->where('status', GdprDataRequest::STATUS_COMPLETED)
            ->whereNotNull('export_file_path')
            ->where('export_expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $request) {
            $request->deleteExportFile();
            $count++;
        }

        return $count;
    }
}
