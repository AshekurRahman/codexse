<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->is_active) {
                $message = 'You are already subscribed to our newsletter.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }
                return back()->with('info', $message);
            }

            $existing->update([
                'is_active' => true,
                'unsubscribed_at' => null,
            ]);

            $message = 'Welcome back! You have been re-subscribed to our newsletter.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);
        }

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'user_id' => auth()->id(),
            'confirmed_at' => now(),
            'is_active' => true,
        ]);

        $message = 'Thank you for subscribing to our newsletter!';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
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
        }

        return redirect()->route('home')->with('success', 'Your subscription has been confirmed!');
    }
}
