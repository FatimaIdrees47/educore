<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('relationship', [
                'father', 'mother', 'guardian', 'other'
            ])->default('guardian');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // A parent can only be linked to a student once
            $table->unique(['student_id', 'parent_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};