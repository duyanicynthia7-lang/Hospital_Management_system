<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('ward_id')->constrained('wards')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->dateTime('admitted_at');
            $table->dateTime('discharged_at')->nullable();
            $table->text('diagnosis');
            $table->text('notes')->nullable();
            $table->enum('status', ['admitted', 'discharged'])->default('admitted');
            $table->timestamps();

            $table->index(['status', 'ward_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
