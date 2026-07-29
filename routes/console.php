<?php

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console commands
|--------------------------------------------------------------------------
|
| Small one-off commands live here. Anything substantial belongs in its own
| class under app/Console/Commands.
|
*/

Artisan::command('healthtrack:about', function (): void {
    $centre = config('healthtrack.centre');

    $this->info('HealthTrack -- barangay patient information management system.');
    $this->line("Centre:   {$centre['name']}");
    $this->line("Location: {$centre['barangay']}, {$centre['municipality']}, {$centre['province']}");
    $this->newLine();
    $this->line('Record types configured in config/healthtrack.php:');

    foreach (config('healthtrack.records') as $key => $definition) {
        $this->line(sprintf('  %-18s %s', $key, $definition['label']));
    }
})->purpose('Show how this HealthTrack installation is configured');
