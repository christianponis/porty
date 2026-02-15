<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berth_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berth_id')->constrained('berths')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_available')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['berth_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berth_availabilities');
    }
};
