<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('campaign_topics')) {
            Schema::create('campaign_topics', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('group_key', 32)->index();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('campaigns') && ! Schema::hasColumn('campaigns', 'campaign_topic_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreignId('campaign_topic_id')
                    ->nullable()
                    ->after('city_id')
                    ->constrained('campaign_topics')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_topic_id');
        });

        Schema::dropIfExists('campaign_topics');
    }
};
