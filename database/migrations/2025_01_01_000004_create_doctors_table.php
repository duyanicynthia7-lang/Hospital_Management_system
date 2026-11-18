<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('specialization');
            $table->string('license_number', 50)->nullable()->unique();
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('schedule')->nullable(); // e.g. "Mon-Fri 08:00-16:00"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Now that doctors exists, link departments.head_doctor_id -> doctors.id
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('head_doctor_id')->references('id')->on('doctors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_doctor_id']);
        });

        Schema::dropIfExists('doctors');
    }
};
