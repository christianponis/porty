<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berths', function (Blueprint $table) {
            $table->boolean('sharing_enabled')->default(false)->after('is_active');
            $table->json('booking_modes')->nullable()->after('sharing_enabled');
            $table->decimal('nodi_value_per_day', 10, 2)->nullable()->after('booking_modes');
        });
    }

    public function down(): void
    {
        Schema::table('berths', function (Blueprint $table) {
            $table->dropColumn(['sharing_enabled', 'booking_modes', 'nodi_value_per_day']);
        });
    }
};
