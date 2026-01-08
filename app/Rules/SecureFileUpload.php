<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SecureFileUpload implements ValidationRule
{
    /**
     * Allowed MIME types mapped to their extensions.
     */
    protected array $allowedMimeTypes = [
        // Images
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],

        // Archives
        'application/zip' => ['zip'],
        'application/x-zip-compressed' => ['zip'],
        'application/x-rar-compressed' => ['rar'],
        'application/vnd.rar' => ['rar'],
        'application/x-7z-compressed' => ['7z'],
        'application/x-tar' => ['tar'],
        'application/gzip' => ['gz'],
        'application/x-gzip' => ['gz'],

        // Documents
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],

        // Design files
        'image/vnd.adobe.photoshop' => ['psd'],
        'application/x-photoshop' => ['psd'],
        'application/postscript' => ['ai', 'eps'],
        'application/illustrator' => ['ai'],
    ];

    /**
     * Maximum file size in bytes (default 100MB).
     */
    protected int $maxSize;

    /**
     * Custom allowed MIME types (if specified).
     */
    protected ?array $customMimeTypes;

    /**
     * Create a new rule instance.
     *
     * @param int $maxSizeMB Maximum file size in megabytes
     * @param array|null $allowedMimeTypes Custom allowed MIME types
     */
    public function __construct(int $maxSizeMB = 100, ?array $allowedMimeTypes = null)
    {
        $this->maxSize = $maxSizeMB * 1024 * 1024;
        $this->customMimeTypes = $allowedMimeTypes;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid uploaded file.');
            return;
        }

        // Check if file was uploaded successfully
        if (!$value->isValid()) {
            $fail('The :attribute failed to upload properly.');
            return;
        }

        // Check file size
        if ($value->getSize() > $this->maxSize) {
            $maxMB = $this->maxSize / 1024 / 1024;
            $fail("The :attribute must not be larger than {$maxMB}MB.");
            return;
        }

        // Get the real MIME type using finfo (more secure than client-provided)
        $realPath = $value->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            $fail('The :attribute could not be verified.');
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMimeType = finfo_file($finfo, $realPath);
        finfo_close($finfo);

        // Get allowed MIME types
        $allowedMimes = $this->customMimeTypes ?? array_keys($this->allowedMimeTypes);

        // Verify MIME type
        if (!in_array($detectedMimeType, $allowedMimes)) {
            Log::warning('SecureFileUpload: Rejected file with invalid MIME type', [
                'attribute' => $attribute,
                'detected_mime' => $detectedMimeType,
                'client_mime' => $value->getMimeType(),
                'original_name' => $value->getClientOriginalName(),
                'extension' => $value->getClientOriginalExtension(),
            ]);

            $fail('The :attribute has an invalid or potentially dangerous file type.');
            return;
        }

        // Additional check: Verify extension matches MIME type (prevent extension spoofing)
        $extension = strtolower($value->getClientOriginalExtension());
        $validExtensions = $this->getValidExtensionsForMime($detectedMimeType);

        if (!empty($validExtensions) && !in_array($extension, $validExtensions)) {
            Log::warning('SecureFileUpload: Extension mismatch detected', [
                'attribute' => $attribute,
                'detected_mime' => $detectedMimeType,
                'extension' => $extension,
                'valid_extensions' => $validExtensions,
                'original_name' => $value->getClientOriginalName(),
            ]);

            $fail('The :attribute extension does not match its actual file type.');
            return;
        }

        // Check for dangerous content patterns in certain file types
        if ($this->containsDangerousContent($realPath, $detectedMimeType)) {
            Log::warning('SecureFileUpload: Potentially dangerous content detected', [
                'attribute' => $attribute,
                'detected_mime' => $detectedMimeType,
                'original_name' => $value->getClientOriginalName(),
            ]);

            $fail('The :attribute contains potentially dangerous content.');
            return;
        }
    }

    /**
     * Get valid extensions for a MIME type.
     */
    protected function getValidExtensionsForMime(string $mimeType): array
    {
        return $this->allowedMimeTypes[$mimeType] ?? [];
    }

    /**
     * Check for dangerous content patterns.
     */
    protected function containsDangerousContent(string $filePath, string $mimeType): bool
    {
        // Only scan text-based or document files for dangerous patterns
        $scanMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!in_array($mimeType, $scanMimeTypes)) {
            return false;
        }

        // Read first 8KB of the file to check for suspicious patterns
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $content = fread($handle, 8192);
        fclose($handle);

        // Dangerous patterns that might indicate embedded malware
        $dangerousPatterns = [
            '/\<script[^>]*\>/i',           // Script tags
            '/javascript:/i',                // JavaScript protocol
            '/vbscript:/i',                  // VBScript protocol
            '/on\w+\s*=/i',                  // Event handlers
            '/\<\?php/i',                    // PHP tags
            '/\<\%(.*)\%\>/i',               // ASP tags
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a rule for product files.
     */
    public static function productFile(): self
    {
        return new self(100, [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip',
            'application/x-gzip',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/vnd.adobe.photoshop',
            'application/x-photoshop',
            'application/postscript',
            'application/illustrator',
        ]);
    }

    /**
     * Create a rule for archive files only.
     */
    public static function archiveOnly(): self
    {
        return new self(100, [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/x-7z-compressed',
        ]);
    }

    /**
     * Create a rule for document files only.
     */
    public static function documentOnly(int $maxSizeMB = 20): self
    {
        return new self($maxSizeMB, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Create a rule for image files only.
     */
    public static function imageOnly(int $maxSizeMB = 10): self
    {
        return new self($maxSizeMB, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
    }

    /**
     * Create a rule for attachments (images + documents + archives).
     */
    public static function attachment(int $maxSizeMB = 5): self
    {
        return new self($maxSizeMB, [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // Archives
            'application/zip',
            'application/x-zip-compressed',
        ]);
    }

    /**
     * Create a rule for identity verification documents.
     */
    public static function identityDocument(int $maxSizeMB = 10): self
    {
        return new self($maxSizeMB, [
            'image/jpeg',
            'image/png',
            'application/pdf',
        ]);
    }
}
