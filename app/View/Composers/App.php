<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

use function App\Core\setting;
use function App\Core\hasSetting;
use function App\Core\getImageElement;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        return [
            'siteName' => $this->siteName(),
            'logotype' => $this->logotype(),
            'logotypeDarkmode' => $this->logotypeDarkmode(),
            'setting' => function ($name, $default = null) {
                return setting($name, $default);
            },
            'hasSetting' => function ($name) {
                return hasSetting($name);
            },
        ];
    }

    /**
     * Retrieve the site name.
     * 
     * @return string
     */
    public function siteName(): string
    {
        return get_bloginfo('name', 'display');
    }

    /**
     * Retrieve the logotype.
     * 
     * @return string
     */
    public function logotype()
    {
        return getImageElement(setting('logotype_default'), 'full', 'w-auto h-12 dark:hidden');
    }

    /**
     * Retrieve the logotype for dark mode.
     * 
     * @return string
     */
    public function logotypeDarkmode()
    {
        return getImageElement(setting('logotype_darkmode'), 'full', 'w-auto h-12 hidden dark:block');
    }
}
