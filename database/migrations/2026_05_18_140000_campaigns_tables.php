<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('excerpt', 480)->nullable();
                $table->text('description');
                $table->string('hero_image_url')->nullable();
                $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedInteger('goal_supporters')->nullable();
                $table->unsignedInteger('supporter_count')->default(0);
                $table->string('moderation_status', 32)->default('pending');
                $table->timestamp('moderated_at')->nullable();
                $table->foreignId('moderated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('moderation_note')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();

                $table->index(['moderation_status', 'created_at']);
                $table->index(['city_id', 'moderation_status']);
            });
        }

        if (! Schema::hasTable('campaign_supporters')) {
            Schema::create('campaign_supporters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['campaign_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_supporters');
        Schema::dropIfExists('campaigns');
    }
};
