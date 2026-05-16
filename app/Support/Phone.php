<?php

namespace App\Support;

class Phone
{
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (str_starts_with($digits, '90')) {
            return '+'.$digits;
        }

        if (strlen($digits) >= 10) {
            return '+90'.$digits;
        }

        return '+'.$digits;
    }
}
