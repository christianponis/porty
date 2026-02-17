<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('self_assessment_id')->constrained('self_assessments')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->unsignedSmallInteger('answer_value');
            $table->string('photo_path', 500)->nullable();
            $table->timestamps();

            $table->unique(['self_assessment_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_assessment_answers');
    }
};
