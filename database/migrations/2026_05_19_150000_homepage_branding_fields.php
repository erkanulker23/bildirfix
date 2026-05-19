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
            $table->string('homepage_seo_title')->nullable()->after('custom_body_html');
            $table->string('homepage_seo_description', 520)->nullable()->after('homepage_seo_title');
            $table->string('site_logo_path')->nullable()->after('homepage_seo_description');
            $table->string('favicon_path')->nullable()->after('site_logo_path');
            $table->string('homepage_og_image_path')->nullable()->after('favicon_path');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'homepage_seo_title',
                'homepage_seo_description',
                'site_logo_path',
                'favicon_path',
                'homepage_og_image_path',
            ]);
        });
    }
};
