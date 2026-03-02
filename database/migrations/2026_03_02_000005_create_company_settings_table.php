<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // Meter & image settings
            $table->boolean('is_meter_number_required')->default(false);
            $table->boolean('is_meter_image_required')->default(false);

            // Code settings
            $table->boolean('is_code_changeable')->default(true);
            $table->boolean('is_code_generator')->default(false);
            $table->char('code', 4)->nullable()->comment('4-digit OTP');

            // OTP login
            $table->boolean('login_with_otp')->default(false);

            // Vehicle limits
            $table->enum('vehicle_limit_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->decimal('vehicle_balance_min', 10, 2)->nullable();
            $table->boolean('vehicles_can_use_fuel_balance')->default(false);

            // Wallet settings
            $table->enum('type_of_wallet', ['branch', 'vehicle'])->default('vehicle');
            $table->boolean('auto_balance')->default(false);

            // Fuel pull limits
            $table->decimal('fuel_pull_limit', 10, 2)->nullable();
            $table->unsignedInteger('fuel_pull_limit_days')->nullable();

            // Wash
            $table->unsignedInteger('wash_count')->nullable();

            // Terms
            $table->text('vehicle_receiving_terms')->nullable();

            // Assignments
            $table->boolean('allow_multiple_assignments')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
