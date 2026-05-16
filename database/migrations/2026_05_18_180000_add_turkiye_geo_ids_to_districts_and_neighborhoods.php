<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->unsignedBigInteger('turkiye_id')->nullable()->after('city_id');
            $table->unique('turkiye_id');
        });

        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->unsignedBigInteger('turkiye_id')->nullable()->after('district_id');
            $table->unique(['district_id', 'turkiye_id']);
        });
    }

    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropUnique(['turkiye_id']);
            $table->dropColumn('turkiye_id');
        });

        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->dropUnique(['district_id', 'turkiye_id']);
            $table->dropColumn('turkiye_id');
        });
    }
};
