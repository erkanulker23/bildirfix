<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('blog_category_id')->nullable()->after('author_user_id')->constrained('blog_categories')->nullOnDelete();
        });

        $now = now();
        $categories = [
            ['name' => 'Kent yaşamı', 'slug' => 'kent-yasami', 'sort_order' => 10],
            ['name' => 'Rehber', 'slug' => 'rehber', 'sort_order' => 20],
            ['name' => 'Duyurular', 'slug' => 'duyurular', 'sort_order' => 30],
            ['name' => 'Haberler', 'slug' => 'haberler', 'sort_order' => 40],
        ];

        foreach ($categories as $cat) {
            DB::table('blog_categories')->insert([
                ...$cat,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blog_category_id');
        });

        Schema::dropIfExists('blog_categories');
    }
};
