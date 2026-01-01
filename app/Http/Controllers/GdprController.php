<?php

namespace App\Http\Controllers;

use App\Models\GdprConsentLog;
use App\Models\GdprDataRequest;
use App\Models\GdprProcessingLog;
use App\Services\GdprService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GdprController extends Controller
{
    public function __construct(
        protected GdprService $gdprService
    ) {}

    /**
     * Show GDPR privacy center.
     */
    public function index()
    {
        $user = Auth::user();

        $requests = GdprDataRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $consentHistory = GdprConsentLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activityLogs = GdprProcessingLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.gdpr.index', compact('user', 'requests', 'consentHistory', 'activityLogs'));
    }

    /**
     * Show data export request form.
     */
    public function exportForm()
    {
        $user = Auth::user();
        $pendingExport = GdprDataRequest::where('user_id', $user->id)
            ->where('type', GdprDataRequest::TYPE_EXPORT)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        $completedExport = GdprDataRequest::where('user_id', $user->id)
            ->where('type', GdprDataRequest::TYPE_EXPORT)
            ->where('status', GdprDataRequest::STATUS_COMPLETED)
            ->where('export_expires_at', '>', now())
            ->orderBy('completed_at', 'desc')
            ->first();

        return view('pages.gdpr.export', compact('user', 'pendingExport', 'completedExport'));
    }

    /**
     * Submit data export request.
     */
    public function submitExport(Request $request)
    {
        $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'string|in:' . implode(',', array_keys(GdprDataRequest::DATA_CATEGORIES)),
        ]);

        try {
            $gdprRequest = $this->gdprService->submitRequest(
                Auth::user(),
                GdprDataRequest::TYPE_EXPORT,
                null,
                $request->categories ?: null
            );

            return redirect()->route('gdpr.requests')
                ->with('success', 'Your data export request has been submitted. Reference: ' . $gdprRequest->request_number);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show data deletion request form.
     */
    public function deleteForm()
    {
        $user = Auth::user();
        $pendingDeletion = GdprDataRequest::where('user_id', $user->id)
            ->where('type', GdprDataRequest::TYPE_DELETION)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        return view('pages.gdpr.delete', compact('user', 'pendingDeletion'));
    }

    /**
     * Submit data deletion request.
     */
    public function submitDelete(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
            'password' => 'required|current_password',
            'confirm_deletion' => 'required|accepted',
        ]);

        try {
            $gdprRequest = $this->gdprService->submitRequest(
                Auth::user(),
                GdprDataRequest::TYPE_DELETION,
                $request->reason
            );

            return redirect()->route('gdpr.requests')
                ->with('success', 'Your account deletion request has been submitted. Reference: ' . $gdprRequest->request_number);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show all GDPR requests.
     */
    public function requests()
    {
        $requests = GdprDataRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.gdpr.requests', compact('requests'));
    }

    /**
     * Show single request details.
     */
    public function showRequest(GdprDataRequest $request)
    {
        // Ensure user owns this request
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.gdpr.show-request', compact('request'));
    }

    /**
     * Cancel a pending request.
     */
    public function cancelRequest(GdprDataRequest $request)
    {
        // Ensure user owns this request
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$request->canBeCancelled()) {
            return back()->with('error', 'This request cannot be cancelled.');
        }

        $request->cancel();

        return back()->with('success', 'Your request has been cancelled.');
    }

    /**
     * Download exported data.
     */
    public function download(GdprDataRequest $request)
    {
        // Ensure user owns this request
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$request->is_export_available) {
            return back()->with('error', 'Export file is not available or has expired.');
        }

        // Log the download
        GdprProcessingLog::log(
            $request->user_id,
            GdprProcessingLog::ACTIVITY_DATA_ACCESS,
            GdprProcessingLog::CATEGORY_PERSONAL,
            'User downloaded their data export',
            Auth::id(),
            ['request_id' => $request->id]
        );

        return Storage::download(
            $request->export_file_path,
            'my-data-export-' . now()->format('Y-m-d') . '.zip'
        );
    }

    /**
     * Show consent preferences.
     */
    public function consentPreferences()
    {
        $user = Auth::user();

        return view('pages.gdpr.consent', compact('user'));
    }

    /**
     * Update consent preferences.
     */
    public function updateConsent(Request $request)
    {
        $request->validate([
            'marketing_consent' => 'nullable|boolean',
            'analytics_consent' => 'nullable|boolean',
            'third_party_consent' => 'nullable|boolean',
        ]);

        $this->gdprService->updateConsent(Auth::user(), [
            'marketing' => $request->boolean('marketing_consent'),
            'analytics' => $request->boolean('analytics_consent'),
            'third_party' => $request->boolean('third_party_consent'),
        ]);

        return back()->with('success', 'Your consent preferences have been updated.');
    }

    /**
     * Show processing logs.
     */
    public function activityLog()
    {
        $logs = GdprProcessingLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.gdpr.activity-log', compact('logs'));
    }
}
