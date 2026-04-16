<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->string('fee_type');
            $table->unsignedTinyInteger('month')->nullable(); // 1-12
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedBigInteger('amount');           // original amount
            $table->unsignedBigInteger('fine_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('net_amount');       // amount - discount + fine
            $table->enum('status', ['unpaid', 'paid', 'partial', 'waived'])->default('unpaid');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'student_id', 'status']);
            $table->index(['school_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_invoices');
    }
};