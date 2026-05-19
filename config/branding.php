<?php

declare(strict_types=1);

return [
    'default_homepage_title' => env('BRANDING_DEFAULT_HOME_TITLE'),

    'default_homepage_description' => env('BRANDING_DEFAULT_HOME_DESCRIPTION'),

    /** Varsayılan favicon (public/ altında). */
    'default_favicon' => '/favicon.svg',

    /** Logo yokken header’da metin + ikon kullanılır. */
    'default_logo' => null,
];
