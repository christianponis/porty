<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berths', function (Blueprint $table) {
            $table->string('rating_level', 10)->nullable()->after('nodi_value_per_day');
            $table->unsignedTinyInteger('grey_anchor_count')->nullable()->after('rating_level');
            $table->unsignedTinyInteger('blue_anchor_count')->nullable()->after('grey_anchor_count');
            $table->unsignedTinyInteger('gold_anchor_count')->nullable()->after('blue_anchor_count');
            $table->unsignedInteger('review_count')->default(0)->after('gold_anchor_count');
            $table->decimal('review_average', 3, 2)->nullable()->after('review_count');

            $table->index(['rating_level', 'blue_anchor_count']);
            $table->index(['review_average']);
        });
    }

    public function down(): void
    {
        Schema::table('berths', function (Blueprint $table) {
            $table->dropIndex(['rating_level', 'blue_anchor_count']);
            $table->dropIndex(['review_average']);
            $table->dropColumn(['rating_level', 'grey_anchor_count', 'blue_anchor_count', 'gold_anchor_count', 'review_count', 'review_average']);
        });
    }
};
