<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidId implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value) || (int)$value != $value || $value < 0) {
            $fail("ID không phải là số nguyên dương");
        }
    }
}
