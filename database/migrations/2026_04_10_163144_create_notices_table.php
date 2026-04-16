<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('target_role', [
                'all', 'school-admin', 'teacher', 'student', 'parent'
            ])->default('all');
            $table->foreignId('target_class_id')
                  ->nullable()
                  ->constrained('classes')
                  ->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'target_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};