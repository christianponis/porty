<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berth_id')->constrained('berths')->cascadeOnDelete();
            $table->unsignedBigInteger('inspector_id')->nullable();
            $table->string('status', 20)->default('pending');
            $table->decimal('total_score', 5, 2)->nullable();
            $table->unsignedTinyInteger('anchor_count')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->text('notes')->nullable();
            $table->date('inspection_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['berth_id', 'status']);
            $table->index(['valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
