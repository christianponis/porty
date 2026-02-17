<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('berth_id')->constrained('berths')->cascadeOnDelete();
            $table->unsignedBigInteger('guest_id');
            $table->unsignedTinyInteger('rating_ormeggio');
            $table->unsignedTinyInteger('rating_servizi');
            $table->unsignedTinyInteger('rating_posizione');
            $table->unsignedTinyInteger('rating_qualita_prezzo');
            $table->unsignedTinyInteger('rating_accoglienza');
            $table->decimal('average_rating', 3, 2);
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->unique(['booking_id']);
            $table->index(['berth_id', 'created_at']);
            $table->index(['guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
