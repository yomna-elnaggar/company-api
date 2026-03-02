<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('tax_id', 15)->unique();
            $table->string('commercial_record')->nullable();
            $table->string('national_id')->nullable();
            $table->string('commerce_letter')->nullable();
            $table->string('contact_id')->nullable();
            $table->string('electronic_contract_website')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('sale_person_id')->nullable()->constrained('sale_people')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
