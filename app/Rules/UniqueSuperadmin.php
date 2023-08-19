<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Validation\Rule;

class UniqueSuperadmin implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
         // Check if a user with the superadmin role exists
         return !User::where('role', 'superadmin')->exists();
    }
    
    public function message()
    {
        return 'Only one user with the superadmin role is allowed.';
    }
}
