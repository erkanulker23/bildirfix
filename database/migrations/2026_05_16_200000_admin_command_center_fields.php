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
            $table->boolean('mail_use_custom_smtp')->default(false)->after('google_client_secret');
            $table->string('mail_from_address')->nullable()->after('mail_use_custom_smtp');
            $table->string('mail_from_name')->nullable()->after('mail_from_address');
            $table->string('mail_host')->nullable()->after('mail_from_name');
            $table->unsignedSmallInteger('mail_port')->nullable()->after('mail_host');
            $table->string('mail_encryption', 16)->nullable()->after('mail_port');
            $table->string('mail_username')->nullable()->after('mail_encryption');
            $table->text('mail_password')->nullable()->after('mail_username');
        });

        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->string('moderation_status', 32)->default('approved')->after('published_at');
            $table->timestamp('moderated_at')->nullable()->after('moderation_status');
            $table->foreignId('moderated_by_user_id')->nullable()->after('moderated_at')->constrained('users')->nullOnDelete();
            $table->text('moderation_note')->nullable()->after('moderated_by_user_id');
            $table->index(['moderation_status', 'is_published']);
        });

        Schema::table('institutions', function (Blueprint $table): void {
            $table->string('website')->nullable()->after('verified');
            $table->string('public_email')->nullable()->after('website');
            $table->string('phone')->nullable()->after('public_email');
            $table->string('address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table): void {
            $table->dropColumn(['website', 'public_email', 'phone', 'address']);
        });

        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropIndex(['moderation_status', 'is_published']);
            $table->dropConstrainedForeignId('moderated_by_user_id');
            $table->dropColumn(['moderation_status', 'moderated_at', 'moderation_note']);
        });

        Schema::table('platform_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'mail_use_custom_smtp',
                'mail_from_address',
                'mail_from_name',
                'mail_host',
                'mail_port',
                'mail_encryption',
                'mail_username',
                'mail_password',
            ]);
        });
    }
};
