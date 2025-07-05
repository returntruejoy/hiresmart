<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location_preference')->nullable()->after('role');
            $table->unsignedInteger('salary_expectation_min')->nullable()->after('location_preference');
            $table->unsignedInteger('salary_expectation_max')->nullable()->after('salary_expectation_min');
            $table->text('bio')->nullable()->after('salary_expectation_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['location_preference', 'salary_expectation_min', 'salary_expectation_max', 'bio']);
        });
    }
};
