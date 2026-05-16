<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Türkiye coğrafi bölgeleri — her plaka tek bölgeye atanır (yaygın okul / TÜİK-yakın sınıflandırma).
 */
final class TurkishRegions
{
    /** @var list<string> */
    public const ORDER = [
        'marmara',
        'ege',
        'akdeniz',
        'icanadolu',
        'karadeniz',
        'dogu',
        'guneydogu',
        'diger',
    ];

    /** @var array<int, string> plaka => bölge anahtarı */
    private const PLATE_TO_REGION = [
        1 => 'akdeniz',
        2 => 'guneydogu',
        3 => 'ege',
        4 => 'dogu',
        5 => 'karadeniz',
        6 => 'icanadolu',
        7 => 'akdeniz',
        8 => 'karadeniz',
        9 => 'ege',
        10 => 'marmara',
        11 => 'marmara',
        12 => 'dogu',
        13 => 'dogu',
        14 => 'karadeniz',
        15 => 'akdeniz',
        16 => 'marmara',
        17 => 'marmara',
        18 => 'icanadolu',
        19 => 'karadeniz',
        20 => 'ege',
        21 => 'guneydogu',
        22 => 'marmara',
        23 => 'dogu',
        24 => 'dogu',
        25 => 'dogu',
        26 => 'icanadolu',
        27 => 'guneydogu',
        28 => 'karadeniz',
        29 => 'karadeniz',
        30 => 'dogu',
        31 => 'akdeniz',
        32 => 'akdeniz',
        33 => 'akdeniz',
        34 => 'marmara',
        35 => 'ege',
        36 => 'dogu',
        37 => 'karadeniz',
        38 => 'icanadolu',
        39 => 'marmara',
        40 => 'icanadolu',
        41 => 'marmara',
        42 => 'icanadolu',
        43 => 'ege',
        44 => 'dogu',
        45 => 'ege',
        46 => 'akdeniz',
        47 => 'guneydogu',
        48 => 'ege',
        49 => 'dogu',
        50 => 'icanadolu',
        51 => 'icanadolu',
        52 => 'karadeniz',
        53 => 'karadeniz',
        54 => 'marmara',
        55 => 'karadeniz',
        56 => 'guneydogu',
        57 => 'karadeniz',
        58 => 'icanadolu',
        59 => 'marmara',
        60 => 'karadeniz',
        61 => 'karadeniz',
        62 => 'dogu',
        63 => 'guneydogu',
        64 => 'ege',
        65 => 'dogu',
        66 => 'icanadolu',
        67 => 'karadeniz',
        68 => 'icanadolu',
        69 => 'karadeniz',
        70 => 'icanadolu',
        71 => 'icanadolu',
        72 => 'guneydogu',
        73 => 'guneydogu',
        74 => 'karadeniz',
        75 => 'dogu',
        76 => 'dogu',
        77 => 'marmara',
        78 => 'karadeniz',
        79 => 'guneydogu',
        80 => 'akdeniz',
        81 => 'marmara',
    ];

    public static function keyForPlate(?int $plate): string
    {
        if ($plate === null || $plate < 1 || $plate > 81) {
            return 'diger';
        }

        return self::PLATE_TO_REGION[$plate] ?? 'diger';
    }

    public static function label(string $key): string
    {
        return match ($key) {
            'marmara' => __('Marmara Bölgesi'),
            'ege' => __('Ege Bölgesi'),
            'akdeniz' => __('Akdeniz Bölgesi'),
            'icanadolu' => __('İç Anadolu Bölgesi'),
            'karadeniz' => __('Karadeniz Bölgesi'),
            'dogu' => __('Doğu Anadolu Bölgesi'),
            'guneydogu' => __('Güneydoğu Anadolu Bölgesi'),
            default => __('Diğer'),
        };
    }
}
