<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default types
        DB::table('user_types')->insert([
            ['name' => 'Admin',   'slug' => 'admin',   'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Company', 'slug' => 'company', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Driver',  'slug' => 'driver',  'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
