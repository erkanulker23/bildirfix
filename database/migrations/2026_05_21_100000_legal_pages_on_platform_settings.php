<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table): void {
            $table->longText('legal_privacy_html')->nullable()->after('homepage_og_image_path');
            $table->longText('legal_kvkk_html')->nullable()->after('legal_privacy_html');
            $table->longText('legal_terms_html')->nullable()->after('legal_kvkk_html');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table): void {
            $table->dropColumn(['legal_privacy_html', 'legal_kvkk_html', 'legal_terms_html']);
        });
    }
};
