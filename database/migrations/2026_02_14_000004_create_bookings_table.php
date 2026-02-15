<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berth_id')->constrained('berths')->cascadeOnDelete();
            $table->unsignedBigInteger('guest_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_days');
            $table->decimal('total_price', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->text('guest_notes')->nullable();
            $table->text('owner_notes')->nullable();
            $table->string('cancelled_by', 10)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['berth_id', 'status']);
            $table->index(['guest_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
