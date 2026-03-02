<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend the default users table with company-related fields.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('mobile', 20)->unique()->nullable()->after('email');
            $table->string('iqama')->nullable()->after('mobile');
            $table->string('image')->nullable()->after('iqama');
            $table->boolean('active')->default(true)->after('image');
            $table->foreignId('user_type_id')->nullable()->constrained('user_types')->nullOnDelete()->after('active');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->after('user_type_id');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('company_id');
            $table->dropConstrainedForeignId('user_type_id');
            $table->dropColumn(['username', 'mobile', 'iqama', 'image', 'active']);
        });
    }
};
