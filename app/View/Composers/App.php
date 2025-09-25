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

    public function with(): array
    {
        return [
            'siteName' => $this->siteName(),
            'logotype' => $this->logotype(),
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
     */
    public function siteName(): string
    {
        return get_bloginfo('name', 'display');
    }

    public function logotype()
    {
        return getImageElement(setting('logotype_default'), 'full', 'w-auto h-12 dark:invert dark:hue-rotate-180');
    }
}
