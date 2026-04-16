<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id')->nullable();
            $table->text('qualifications')->nullable();
            $table->date('joining_date')->nullable();
            // Money stored as integer (paisas) — never floats for currency
            $table->unsignedBigInteger('salary')->default(0);
            $table->unsignedTinyInteger('leave_balance')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'employee_id']);
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};