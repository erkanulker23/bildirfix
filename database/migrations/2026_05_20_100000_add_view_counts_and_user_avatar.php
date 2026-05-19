<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('name');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->after('follow_count');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->after('supporter_count');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};
