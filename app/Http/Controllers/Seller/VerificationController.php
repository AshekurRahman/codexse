<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerVerification;
use App\Rules\SecureFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Show verification status page.
     */
    public function index()
    {
        $seller = auth()->user()->seller;

        if (!$seller) {
            return redirect()->route('seller.apply')
                ->with('error', 'You need to be a seller to access verification.');
        }

        $verifications = $seller->verifications()->latest()->get();
        $latestVerification = $seller->latestVerification;

        return view('seller.verification.index', compact('seller', 'verifications', 'latestVerification'));
    }

    /**
     * Show the verification request form.
     */
    public function create(Request $request)
    {
        $seller = auth()->user()->seller;

        if (!$seller) {
            return redirect()->route('seller.apply')
                ->with('error', 'You need to be a seller to request verification.');
        }

        if (!$seller->canRequestVerification()) {
            return redirect()->route('seller.verification.index')
                ->with('error', 'You already have a pending verification request.');
        }

        $type = $request->get('type', 'identity');

        return view('seller.verification.create', compact('seller', 'type'));
    }

    /**
     * Submit a verification request.
     */
    public function store(Request $request)
    {
        $seller = auth()->user()->seller;

        if (!$seller) {
            return redirect()->route('seller.apply')
                ->with('error', 'You need to be a seller to request verification.');
        }

        if (!$seller->canRequestVerification()) {
            return redirect()->route('seller.verification.index')
                ->with('error', 'You already have a pending verification request.');
        }

        $validated = $request->validate([
            'verification_type' => 'required|in:identity,business,address',
            'document_type' => 'required|in:passport,national_id,drivers_license,business_license,utility_bill,bank_statement',
            'document_number' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'country' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'document_front' => ['required', SecureFileUpload::identityDocument(5)],
            'document_back' => ['nullable', SecureFileUpload::identityDocument(5)],
            'selfie_with_document' => ['required_if:verification_type,identity', SecureFileUpload::identityDocument(5)],
        ]);

        // Upload documents
        $documentFront = $request->file('document_front')->store('verifications/' . $seller->id, 'public');
        $documentBack = $request->hasFile('document_back')
            ? $request->file('document_back')->store('verifications/' . $seller->id, 'public')
            : null;
        $selfie = $request->hasFile('selfie_with_document')
            ? $request->file('selfie_with_document')->store('verifications/' . $seller->id, 'public')
            : null;

        // Create verification request
        SellerVerification::create([
            'seller_id' => $seller->id,
            'verification_type' => $validated['verification_type'],
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'full_name' => $validated['full_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'country' => $validated['country'],
            'address' => $validated['address'],
            'document_front' => $documentFront,
            'document_back' => $documentBack,
            'selfie_with_document' => $selfie,
            'status' => 'pending',
        ]);

        // Update seller status
        $seller->update(['verification_status' => 'pending']);

        return redirect()->route('seller.verification.index')
            ->with('success', 'Verification request submitted successfully. We will review it within 24-48 hours.');
    }

    /**
     * Show a specific verification request.
     */
    public function show(SellerVerification $verification)
    {
        $seller = auth()->user()->seller;

        if (!$seller || $verification->seller_id !== $seller->id) {
            abort(403);
        }

        return view('seller.verification.show', compact('seller', 'verification'));
    }
}
