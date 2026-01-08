<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->is_active && $existing->confirmed_at) {
                $message = 'You are already subscribed to our newsletter.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }
                return back()->with('info', $message);
            }

            // Re-subscribe - need to confirm again
            $existing->update([
                'is_active' => false, // Will become active after confirmation
                'unsubscribed_at' => null,
                'confirmed_at' => null, // Require re-confirmation
            ]);

            // Send confirmation email
            $this->sendConfirmationEmail($existing);

            $message = 'Please check your email to confirm your subscription.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);
        }

        // Create new subscriber with pending confirmation (GDPR double opt-in)
        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'user_id' => auth()->id(),
            'confirmed_at' => null, // Not confirmed yet - requires email verification
            'is_active' => false, // Will become active after confirmation
        ]);

        // Send confirmation email
        $this->sendConfirmationEmail($subscriber);

        $message = 'Please check your email to confirm your subscription.';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
    }

    /**
     * Send double opt-in confirmation email
     */
    protected function sendConfirmationEmail(NewsletterSubscriber $subscriber): void
    {
        try {
            $confirmUrl = route('newsletter.confirm', $subscriber->token);

            Mail::send('emails.newsletter.confirm', [
                'confirmUrl' => $confirmUrl,
                'email' => $subscriber->email,
            ], function ($message) use ($subscriber) {
                $message->to($subscriber->email)
                    ->subject('Confirm your newsletter subscription');
            });

            Log::info('Newsletter confirmation email sent', ['email' => $subscriber->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send newsletter confirmation email: ' . $e->getMessage());
        }
    }

    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->unsubscribe();

        return view('pages.newsletter.unsubscribed');
    }

    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        if (!$subscriber->confirmed_at) {
            $subscriber->confirm();
            Log::info('Newsletter subscription confirmed', ['email' => $subscriber->email]);
        }

        return redirect()->route('home')->with('success', 'Your subscription has been confirmed! Welcome to our newsletter.');
    }
}
