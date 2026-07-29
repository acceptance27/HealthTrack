<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',

    // Where Fortify sends people after login. The /dashboard route reads the
    // user's role and forwards them to the right home page.
    'home' => '/dashboard',

    'prefix' => '',
    'domain' => null,
    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    // Our own Blade files are registered in FortifyServiceProvider.
    'views' => true,

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Features::registration() is deliberately ABSENT.
    |
    | This is a clinical system: nobody should be able to create their own
    | account. An earlier version left registration on with a role dropdown on
    | the public form, which let an anonymous visitor sign up as a midwife and
    | read every patient record. Staff accounts come from the seeder; patient
    | accounts are created by staff inside the app.
    |
    */

    'features' => [
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]),
    ],

];
