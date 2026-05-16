<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_user_id')->nullable()->comment('Bağlı kurum hesabı')->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index(['city_id', 'verified']);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_url')->nullable();
            $table->json('media')->nullable()->comment('Çoklu medya/meta');
            // Kalıcı şikâyet feed’i; story içerikleri `stories` tablosunda
            $table->string('type', 32)->default('complaint');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->default('open');
            $table->unsignedInteger('support_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('reports_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->timestamps();

            $table->index(['city_id', 'district_id', 'status', 'created_at']);
            if (DB::getDriverName() === 'mysql') {
                $table->fullText(['title', 'description']);
            }
        });

        Schema::create('post_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('media_url')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['city_id', 'district_id', 'expires_at', 'created_at']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
        });

        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supports');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('post_status_logs');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('categories');
    }
};
