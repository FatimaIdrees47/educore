<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date')->nullable();
            $table->unsignedSmallInteger('full_marks')->default(100);
            $table->unsignedSmallInteger('passing_marks')->default(40);
            $table->timestamps();

            $table->unique(['exam_id', 'subject_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};