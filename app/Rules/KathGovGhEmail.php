<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KathGovGhEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = is_string($value) ? strtolower(trim($value)) : '';

        if ($email === '' || ! str_contains($email, '@') || ! str_ends_with($email, 'kath.gov.gh')) {
            $fail('Only hospital email addresses ending with kath.gov.gh can be used.');
        }
    }
}
