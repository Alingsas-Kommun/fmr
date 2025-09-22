<?php

use Roots\Acorn\Application;
use App\Providers\ThemeServiceProvider;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'fmr'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| System Requirements Check
|--------------------------------------------------------------------------
|
| Check for minimum PHP and WordPress version requirements before
| initializing the application.
|
*/

if (version_compare('8.2', phpversion(), '>=') === -1) {
    wp_die(__('You must be using PHP 8.2 or greater.', 'fmr'));
}

if (version_compare('6.8.0', get_bloginfo('version'), '>=') === -1) {
    wp_die(__('You must be using WordPress 6.8.0 or greater.', 'fmr'));
}

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        ThemeServiceProvider::class,
    ])
    ->withRouting(
        // Configure routing with named parameters
        web: base_path('routes/web.php'),    // Laravel-style web routes
        api: base_path('routes/api.php'),    // API routes
        wordpress: true,                     // Enable WordPress request handling
    )
    ->boot();
