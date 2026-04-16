<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')
                  ->nullable() // nullable because super admin has no school
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('photo_path')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('photo_path');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn([
                'school_id', 'phone', 'photo_path',
                'is_active', 'last_login_at', 'deleted_at'
            ]);
        });
    }
};