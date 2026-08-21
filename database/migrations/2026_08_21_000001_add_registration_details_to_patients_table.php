<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->string('civil_status', 30)->nullable()->after('birthdate');
            $table->string('blood_type', 10)->nullable()->after('civil_status');
            $table->string('occupation')->nullable()->after('blood_type');
            $table->string('barangay_id_number', 50)->nullable()->after('occupation');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->dropColumn([
                'civil_status',
                'blood_type',
                'occupation',
                'barangay_id_number',
            ]);
        });
    }
};