<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berth_id')->constrained('berths')->cascadeOnDelete();
            $table->unsignedBigInteger('owner_id');
            $table->string('status', 20)->default('draft');
            $table->decimal('total_score', 5, 2)->nullable();
            $table->unsignedTinyInteger('anchor_count')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['berth_id', 'status']);
            $table->index(['owner_id']);
            $table->unique(['berth_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_assessments');
    }
};
