<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test setup
|--------------------------------------------------------------------------
|
| Every Feature test gets a fresh in-memory SQLite database (see phpunit.xml).
| Run the suite with:  php artisan test
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
