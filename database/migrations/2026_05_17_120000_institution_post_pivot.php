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
        Schema::create('institution_post', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'institution_id']);
        });

        foreach (DB::table('posts')->whereNotNull('institution_id')->cursor() as $row) {
            DB::table('institution_post')->insertOrIgnore([
                'post_id' => $row->id,
                'institution_id' => $row->institution_id,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_post');
    }
};
