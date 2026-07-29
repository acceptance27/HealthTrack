<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

            // The midwife who scheduled it. Keep the appointment if the
            // account is later removed, so history is not lost.
            $table->foreignId('midwife_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending');
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('scheduled_at');
            $table->index(['patient_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
