<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('healthtrack:about', function (): void {
    $this->info('HealthTrack barangay patient information and medical history system.');
});
