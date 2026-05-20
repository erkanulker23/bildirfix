<?php

return [
    'user_email_domain' => env('EXTERNAL_IMPORT_EMAIL_DOMAIN', 'import.simdibildir.local'),
    'http_timeout' => (int) env('EXTERNAL_IMPORT_HTTP_TIMEOUT', 25),
    'max_detail_fetches_per_run' => (int) env('EXTERNAL_IMPORT_MAX_DETAILS', 80),
    'user_agent' => 'Mozilla/5.0 (compatible; SimdibildirImporter/1.0)',
];
