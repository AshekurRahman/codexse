<?php

namespace App\Rules;

use App\Models\PasswordHistory;
use App\Models\Setting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurePassword implements ValidationRule
{
    protected ?int $userId = null;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $errors = [];

        // Minimum length
        $minLength = (int) Setting::get('password_min_length', 12);
        if (strlen($value) < $minLength) {
            $errors[] = "The password must be at least {$minLength} characters.";
        }

        // Maximum length (prevent DoS)
        if (strlen($value) > 128) {
            $errors[] = 'The password must not exceed 128 characters.';
        }

        // Mixed case requirement
        if (Setting::get('password_require_mixed_case', true)) {
            if (!preg_match('/[a-z]/', $value) || !preg_match('/[A-Z]/', $value)) {
                $errors[] = 'The password must contain both uppercase and lowercase letters.';
            }
        }

        // Numbers requirement
        if (Setting::get('password_require_numbers', true)) {
            if (!preg_match('/[0-9]/', $value)) {
                $errors[] = 'The password must contain at least one number.';
            }
        }

        // Symbols requirement
        if (Setting::get('password_require_symbols', true)) {
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $value)) {
                $errors[] = 'The password must contain at least one special character.';
            }
        }

        // Check for common passwords
        if ($this->isCommonPassword($value)) {
            $errors[] = 'This password is too common. Please choose a more unique password.';
        }

        // Check for sequential characters
        if ($this->hasSequentialCharacters($value)) {
            $errors[] = 'The password contains too many sequential characters.';
        }

        // Check for repeated characters
        if ($this->hasRepeatedCharacters($value)) {
            $errors[] = 'The password contains too many repeated characters.';
        }

        // Check password history (prevent reuse)
        if ($this->userId) {
            $preventReuse = (int) Setting::get('password_prevent_reuse', 5);
            if ($preventReuse > 0 && PasswordHistory::wasUsedBefore($this->userId, $value, $preventReuse)) {
                $errors[] = "You cannot reuse any of your last {$preventReuse} passwords.";
            }
        }

        // Report first error
        if (!empty($errors)) {
            $fail($errors[0]);
        }
    }

    /**
     * Check if password is in common passwords list.
     */
    protected function isCommonPassword(string $password): bool
    {
        $common = [
            'password', 'password1', 'password123', '123456', '12345678', '123456789',
            'qwerty', 'abc123', 'monkey', 'master', 'dragon', 'letmein', 'login',
            'welcome', 'admin', 'administrator', 'passw0rd', 'p@ssword', 'p@ssw0rd',
            'iloveyou', 'princess', 'sunshine', 'football', 'baseball', 'soccer',
            'starwars', 'trustno1', 'batman', 'superman', 'michael', 'jennifer',
            'shadow', 'ashley', 'bailey', 'whatever', 'freedom', 'mustang',
            'qwerty123', 'password!', '1234567890', 'qwertyuiop', 'asdfghjkl',
        ];

        return in_array(strtolower($password), $common);
    }

    /**
     * Check for sequential characters (abc, 123, etc.).
     */
    protected function hasSequentialCharacters(string $password, int $threshold = 4): bool
    {
        $sequences = [
            'abcdefghijklmnopqrstuvwxyz',
            'zyxwvutsrqponmlkjihgfedcba',
            '0123456789',
            '9876543210',
            'qwertyuiop',
            'asdfghjkl',
            'zxcvbnm',
        ];

        $lowerPassword = strtolower($password);

        foreach ($sequences as $sequence) {
            for ($i = 0; $i <= strlen($sequence) - $threshold; $i++) {
                $chunk = substr($sequence, $i, $threshold);
                if (str_contains($lowerPassword, $chunk)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check for repeated characters (aaaa, 1111, etc.).
     */
    protected function hasRepeatedCharacters(string $password, int $threshold = 3): bool
    {
        return preg_match('/(.)\1{' . ($threshold - 1) . ',}/', $password) === 1;
    }
}
