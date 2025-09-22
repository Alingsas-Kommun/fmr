<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Slugs
    |--------------------------------------------------------------------------
    |
    | Define route slugs for different languages. The key is the locale and
    | the value is an array of slugs for that language.
    |
    */

    'slugs' => [
        'en' => [
            'assignments' => 'assignments',
            'decision-authorities' => 'decision-authorities',
            'style-guide' => 'style-guide',
            'search' => 'search',
        ],
        'sv' => [
            'assignments' => 'uppdrag',
            'decision-authorities' => 'beslutsinstanser',
            'style-guide' => 'stilguide',
            'search' => 'sok',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | The default language to use when no locale is specified.
    |
    */
    'default_locale' => 'sv',
];
