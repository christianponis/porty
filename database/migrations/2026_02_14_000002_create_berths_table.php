<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->foreignId('port_id')->constrained('ports')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('length_m', 5, 2);
            $table->decimal('width_m', 5, 2);
            $table->decimal('max_draft_m', 4, 2)->nullable();
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('price_per_week', 10, 2)->nullable();
            $table->decimal('price_per_month', 10, 2)->nullable();
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->string('status', 20)->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['port_id', 'status']);
            $table->index(['owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berths');
    }
};
