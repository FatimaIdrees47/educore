<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('schools', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('schools', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('schools', 'principal_name')) {
                $table->string('principal_name')->nullable()->after('address');
            }
            if (!Schema::hasColumn('schools', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])
                      ->default('active')->after('principal_name');
            }
            if (!Schema::hasColumn('schools', 'subdomain')) {
                $table->string('subdomain')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('schools', 'max_students')) {
                $table->integer('max_students')->default(500)->after('subdomain');
            }
            if (!Schema::hasColumn('schools', 'max_teachers')) {
                $table->integer('max_teachers')->default(50)->after('max_students');
            }
            if (!Schema::hasColumn('schools', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('max_teachers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'principal_name',
                'status', 'subdomain', 'max_students',
                'max_teachers', 'subscription_expires_at',
            ]);
        });
    }
};