<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('moderation_status', 32)->default('approved')->after('status');
            $table->timestamp('moderated_at')->nullable()->after('moderation_status');
            $table->foreignId('moderated_by_user_id')->nullable()->after('moderated_at')->constrained('users')->nullOnDelete();
            $table->text('moderation_note')->nullable()->after('moderated_by_user_id');
            $table->index(['moderation_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['moderated_by_user_id']);
            $table->dropIndex(['moderation_status', 'created_at']);
            $table->dropColumn(['moderation_status', 'moderated_at', 'moderated_by_user_id', 'moderation_note']);
        });
    }
};
