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
            'parties' => 'parties',
            'persons' => 'persons',
            'assignments' => 'assignments',
            'boards' => 'boards',
            'style-guide' => 'style-guide',
        ],
        'sv' => [
            'parties' => 'partier',
            'persons' => 'personer',
            'assignments' => 'uppdrag',
            'boards' => 'beslutsinstanser',
            'style-guide' => 'stilguide',
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
