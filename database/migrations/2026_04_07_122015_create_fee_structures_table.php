<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('fee_type'); // Tuition, Transport, Lab, Library
            $table->unsignedBigInteger('amount'); // in paisas
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly', 'one-time'])
                  ->default('monthly');
            $table->unsignedTinyInteger('due_day')->default(10); // day of month
            $table->timestamps();

            $table->index(['school_id', 'class_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};