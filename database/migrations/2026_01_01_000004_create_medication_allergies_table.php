<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('allergen');
            $table->text('reaction')->nullable();
            $table->string('severity')->nullable();
            $table->date('recorded_at');
            $table->timestamps();

            $table->index(['barangay_id', 'patient_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_allergies');
    }
};
