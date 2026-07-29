<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The five clinical record tables.
 *
 * They live in one migration because they share an identical skeleton --
 * patient, author, one date -- and only differ in their content columns.
 * Keeping them together makes that symmetry visible.
 *
 * If you add a column here, add it to the matching model's $fillable and to
 * the "records" array in config/healthtrack.php, or it will not appear in
 * any form. See DOCS/03-adding-a-record-type.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table): void {
            $this->skeleton($table);
            $table->string('diagnosis');
            $table->text('description')->nullable();
            $this->finish($table, 'diagnosed_at');
        });

        Schema::create('lab_values', function (Blueprint $table): void {
            $this->skeleton($table);
            $table->string('test_name');
            $table->string('value');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $this->finish($table, 'tested_at');
        });

        Schema::create('doctor_notes', function (Blueprint $table): void {
            $this->skeleton($table);
            $table->string('title');
            $table->text('note');
            $this->finish($table, 'noted_at');
        });

        Schema::create('medical_histories', function (Blueprint $table): void {
            $this->skeleton($table);
            $table->string('condition');
            $table->text('details')->nullable();
            $this->finish($table, 'recorded_at');
        });

        Schema::create('medication_allergies', function (Blueprint $table): void {
            $this->skeleton($table);
            $table->string('allergen');
            $table->text('reaction')->nullable();
            $table->string('severity')->nullable();
            $this->finish($table, 'recorded_at');
        });
    }

    /** Columns every clinical record shares, added before the content columns. */
    private function skeleton(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

        // The staff member who entered the record. Nulled rather than
        // cascaded so deleting a user never destroys clinical history.
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    }

    /** The date column, timestamps, and the index, added after the content columns. */
    private function finish(Blueprint $table, string $dateColumn): void
    {
        $table->date($dateColumn);
        $table->timestamps();
        $table->index(['patient_id', $dateColumn]);
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_allergies');
        Schema::dropIfExists('medical_histories');
        Schema::dropIfExists('doctor_notes');
        Schema::dropIfExists('lab_values');
        Schema::dropIfExists('diagnoses');
    }
};
