<?php

declare(strict_types=1);

return [
    /*
    | Google AdSense — .env ile etkinleştirin.
    | Yayına almadan önce AdSense hesabınızın onaylandığından ve politikalara uyduğunuzdan emin olun.
    */
    'enabled' => filter_var(env('ADSENSE_ENABLED', false), FILTER_VALIDATE_BOOL),

    /** ca-pub-XXXXXXXXXXXXXXXX */
    'client' => env('ADSENSE_CLIENT', ''),

    /** Opsiyonel: tüm slotlar için varsayılan (boşsa yalnızca slot bazlı kullanın) */
    'default_slot' => env('ADSENSE_DEFAULT_SLOT', ''),

    'slots' => [
        'feed_sidebar' => env('ADSENSE_SLOT_FEED_SIDEBAR', ''),
        'feed_inline' => env('ADSENSE_SLOT_FEED_INLINE', ''),
        'city_top' => env('ADSENSE_SLOT_CITY_TOP', ''),
        'home_mid' => env('ADSENSE_SLOT_HOME_MID', ''),
    ],
];
