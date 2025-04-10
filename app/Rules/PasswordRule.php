<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{
    public function passes($attribute, $value)
    {
        // يجب أن تكون كلمة المرور 8 أحرف على الأقل
        if (strlen($value) < 8) {
            return false;
        }

        // يجب أن تحتوي على حرف كبير واحد على الأقل
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // يجب أن تحتوي على حرف صغير واحد على الأقل
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // يجب أن تحتوي على رقم واحد على الأقل
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل، وتتضمن حرفاً كبيراً، وحرفاً صغيراً، ورقماً.';
    }
}
