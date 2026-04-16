<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('school_name');
            $table->string('school_email')->nullable();
            $table->string('school_phone')->nullable();
            $table->string('school_address')->nullable();
            $table->string('school_website')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('grading_scale')->nullable();
            $table->string('currency', 10)->default('PKR');
            $table->string('timezone')->default('Asia/Karachi');
            $table->boolean('allow_parent_messages')->default(true);
            $table->boolean('show_positions')->default(true);
            $table->timestamps();

            $table->unique('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};