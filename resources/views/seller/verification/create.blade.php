<x-layouts.app title="Request Verification">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.verification.index') }}" class="inline-flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Verification Status
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Request Verification</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Submit your documents for identity verification.</p>
            </div>

            <form action="{{ route('seller.verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Verification Type -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Verification Type</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="verification_type" value="identity" class="peer sr-only" {{ $type === 'identity' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-surface-200 dark:border-surface-700 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-colors">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-medium text-surface-900 dark:text-white">Identity</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Verify your personal identity with government ID.</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="verification_type" value="business" class="peer sr-only" {{ $type === 'business' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-surface-200 dark:border-surface-700 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-colors">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="font-medium text-surface-900 dark:text-white">Business</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Verify your business registration.</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="verification_type" value="address" class="peer sr-only" {{ $type === 'address' ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-surface-200 dark:border-surface-700 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-colors">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="font-medium text-surface-900 dark:text-white">Address</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Verify your residential address.</p>
                            </div>
                        </label>
                    </div>
                    @error('verification_type')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Type -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Document Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Document Type *</label>
                            <select name="document_type" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Select document type</option>
                                <option value="passport">Passport</option>
                                <option value="national_id">National ID Card</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="business_license">Business License</option>
                                <option value="utility_bill">Utility Bill</option>
                                <option value="bank_statement">Bank Statement</option>
                            </select>
                            @error('document_type')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Document Number</label>
                            <input type="text" name="document_number" value="{{ old('document_number') }}" placeholder="e.g., A12345678" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">
                            @error('document_number')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Full Legal Name *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">
                            @error('full_name')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Country *</label>
                            <input type="text" name="country" value="{{ old('country') }}" required placeholder="e.g., United States" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">
                            @error('country')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Address</label>
                            <textarea name="address" rows="2" placeholder="Full address" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 focus:border-primary-500 focus:ring-primary-500">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Upload -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Upload Documents</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Document Front *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-surface-600">
                                        <label class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
                                            <span>Upload front of document</span>
                                            <input type="file" name="document_front" accept="image/*" required class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-surface-500">PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                            @error('document_front')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Document Back (if applicable)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-surface-600">
                                        <label class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
                                            <span>Upload back of document</span>
                                            <input type="file" name="document_back" accept="image/*" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-surface-500">PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                            @error('document_back')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Selfie with Document *</label>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-2">Take a photo of yourself holding your ID document next to your face.</p>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div class="flex text-sm text-surface-600">
                                        <label class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
                                            <span>Upload selfie with document</span>
                                            <input type="file" name="selfie_with_document" accept="image/*" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-surface-500">PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                            @error('selfie_with_document')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('seller.verification.index') }}" class="px-4 py-2 text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Submit Verification Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
