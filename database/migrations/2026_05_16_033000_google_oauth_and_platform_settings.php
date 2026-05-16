<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->change();
            $table->string('password')->nullable()->change();
        });

        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('google_oauth_enabled')->default(false);
            $table->text('google_client_id')->nullable();
            $table->text('google_client_secret')->nullable();
            $table->timestamps();
        });

        DB::table('platform_settings')->insert([
            'google_oauth_enabled' => false,
            'google_client_id' => null,
            'google_client_secret' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['google_id']);
            $table->dropColumn('google_id');

            try {
                $table->string('phone')->nullable(false)->change();
            } catch (\Throwable) {
                // downgrade may require manual fix if oauth users lack phone
            }

            try {
                $table->string('password')->nullable(false)->change();
            } catch (\Throwable) {
                //
            }
        });
    }
};
