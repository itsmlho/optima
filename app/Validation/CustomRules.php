<?php

namespace App\Validation;

class CustomRules
{
    public function strong_password(string $str, ?string $fields = null, array $data = []): bool
    {
        // Cek 1 huruf kecil, 1 huruf besar, 1 angka, 1 simbol, minimal 8 karakter
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $str);
    }
}
