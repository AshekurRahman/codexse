<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the About page.
     */
    public function about(): View
    {
        return view('pages.about');
    }

    /**
     * Display the Contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        ContactMessage::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Thank you for your message! We will get back to you within 24-48 hours.');
    }

    /**
     * Display the Help Center page.
     */
    public function help(): View
    {
        return view('pages.help');
    }

    /**
     * Display the License page.
     */
    public function license(): View
    {
        return view('pages.legal.license');
    }

    /**
     * Display the Refund Policy page.
     */
    public function refund(): View
    {
        return view('pages.legal.refund');
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy(): View
    {
        return view('pages.legal.privacy');
    }

    /**
     * Display the Terms of Service page.
     */
    public function terms(): View
    {
        return view('pages.legal.terms');
    }

    /**
     * Display the Cookie Policy page.
     */
    public function cookies(): View
    {
        return view('pages.legal.cookies');
    }
}
