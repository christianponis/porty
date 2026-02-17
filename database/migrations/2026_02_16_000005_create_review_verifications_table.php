<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->string('question_key', 50);
            $table->boolean('answer');
            $table->timestamps();

            $table->unique(['review_id', 'question_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_verifications');
    }
};
