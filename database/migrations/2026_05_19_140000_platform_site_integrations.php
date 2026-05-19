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
            $table->string('google_site_verification', 128)->nullable()->after('mail_password');
            $table->string('google_analytics_measurement_id', 32)->nullable()->after('google_site_verification');
            $table->string('yandex_verification', 128)->nullable()->after('google_analytics_measurement_id');
            $table->string('bing_site_verification', 128)->nullable()->after('yandex_verification');
            $table->string('indexnow_key', 64)->nullable()->unique()->after('bing_site_verification');
            $table->text('custom_head_css')->nullable()->after('indexnow_key');
            $table->text('custom_head_html')->nullable()->after('custom_head_css');
            $table->text('custom_body_html')->nullable()->after('custom_head_html');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'google_site_verification',
                'google_analytics_measurement_id',
                'yandex_verification',
                'bing_site_verification',
                'indexnow_key',
                'custom_head_css',
                'custom_head_html',
                'custom_body_html',
            ]);
        });
    }
};
