<?php

namespace App\Providers;

use App\Policies\ClinicalRecordPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // PatientPolicy and AppointmentPolicy are found automatically by
        // Laravel's naming convention. The five clinical record models all
        // share one policy class, which convention cannot guess, so they are
        // mapped here. Adding a record type? Add its model to this loop.
        foreach (config('healthtrack.records') as $definition) {
            Gate::policy($definition['model'], ClinicalRecordPolicy::class);
        }
    }
}
