<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('diagnosis');
            $table->text('description')->nullable();
            $table->date('diagnosed_at');
            $table->timestamps();

            $table->index(['barangay_id', 'patient_id', 'diagnosed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
