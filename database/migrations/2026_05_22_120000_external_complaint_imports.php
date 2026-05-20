<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_import_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 32)->comment('sikayetvar | sikayetvar_institution');
            $table->string('source_url');
            $table->string('source_slug')->nullable();
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('enabled')->default(true);
            $table->boolean('auto_sync')->default(false);
            $table->unsignedSmallInteger('max_pages')->default(50);
            $table->boolean('fetch_media')->default(true);
            $table->string('default_moderation', 32)->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->unsignedInteger('last_imported_count')->default(0);
            $table->text('last_sync_error')->nullable();
            $table->timestamps();

            $table->index(['type', 'enabled']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('external_source', 32)->nullable()->after('google_id');
            $table->string('external_import_key', 191)->nullable()->after('external_source');

            $table->unique(['external_source', 'external_import_key'], 'users_external_import_unique');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('external_source', 32)->nullable()->after('type');
            $table->string('external_id', 64)->nullable()->after('external_source');
            $table->string('source_url', 512)->nullable()->after('external_id');
            $table->foreignId('external_import_source_id')->nullable()->after('source_url')
                ->constrained('external_import_sources')->nullOnDelete();
            $table->timestamp('imported_at')->nullable()->after('external_import_source_id');

            $table->unique(['external_source', 'external_id'], 'posts_external_unique');
            $table->index('external_import_source_id');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['external_import_source_id']);
            $table->dropUnique('posts_external_unique');
            $table->dropIndex(['external_import_source_id']);
            $table->dropColumn([
                'external_source',
                'external_id',
                'source_url',
                'external_import_source_id',
                'imported_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_external_import_unique');
            $table->dropColumn(['external_source', 'external_import_key']);
        });

        Schema::dropIfExists('external_import_sources');
    }
};
