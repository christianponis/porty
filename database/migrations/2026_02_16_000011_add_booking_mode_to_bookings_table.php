<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_mode', 30)->default('rental')->after('status');
            $table->decimal('nodi_amount', 12, 2)->nullable()->after('booking_mode');
            $table->decimal('eur_compensation', 10, 2)->nullable()->after('nodi_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_mode', 'nodi_amount', 'eur_compensation']);
        });
    }
};
