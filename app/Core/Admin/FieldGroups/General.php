<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\OptionsFieldGroup;

class General extends OptionsFieldGroup
{
    protected static $id = 'general';

    protected function getTitle()
    {
        return __('General', 'fmr');
    }

    protected function getFields()
    {
        return [
            [
                'id' => 'general_settings',
                'label' => __('General', 'fmr'),
                'tab' => 'general',
                'tab_label' => __('General', 'fmr'),
                'fields' => [
                    [
                        'id' => 'site_title',
                        'label' => __('Site Title', 'fmr'),
                        'type' => 'text',
                        'cols' => 6,
                        'default' => 'Alingsås',
                        'description' => __('Main title of the website', 'fmr'),
                        'optional' => true,
                    ],
                    [
                        'id' => 'site_description',
                        'label' => __('Site Description', 'fmr'),
                        'type' => 'text',
                        'cols' => 6,
                        'default' => 'Alingsås',
                        'description' => __('Description of the website', 'fmr'),
                        'optional' => true,
                    ],
                ],
            ],
            [
                'id' => 'branding_settings',
                'label' => __('Branding', 'fmr'),
                'tab' => 'general',
                'tab_label' => __('General', 'fmr'),
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'logotype_default',
                                'label' => __('Logotype (Default)', 'fmr'),
                                'type' => 'image',
                                'cols' => 6,
                                'description' => __('Logo for light mode', 'fmr'),
                            ],
                            [
                                'id' => 'logotype_darkmode',
                                'label' => __('Logotype (Dark Mode)', 'fmr'),
                                'type' => 'image',
                                'cols' => 6,
                                'description' => __('Logo for dark mode', 'fmr'),
                                'optional' => true,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'primary_color',
                                'label' => __('Primary Color', 'fmr'),
                                'type' => 'color',
                                'cols' => 4,
                                'default' => '#236151',
                                'css_var' => '--wp-admin-color-primary',
                            ],
                            [
                                'id' => 'secondary_color',
                                'label' => __('Secondary Color', 'fmr'),
                                'type' => 'color',
                                'cols' => 4,
                                'default' => '#bd2b30',
                                'css_var' => '--wp-admin-color-secondary',
                            ],
                            [
                                'id' => 'tertiary_color',
                                'label' => __('Tertiary Color', 'fmr'),
                                'type' => 'color',
                                'cols' => 4,
                                'default' => '#fab526',
                                'css_var' => '--wp-admin-color-tertiary',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'display_settings',
                'label' => __('Display Settings', 'fmr'),
                'tab' => 'general',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'show_breadcrumbs',
                                'label' => __('Show Breadcrumbs', 'fmr'),
                                'type' => 'checkbox',
                                'cols' => 4,
                                'on_label' => __('Yes', 'fmr'),
                                'off_label' => __('No', 'fmr'),
                                'description' => __('Display breadcrumb navigation', 'fmr'),
                            ],
                            [
                                'id' => 'show_search',
                                'label' => __('Show Search', 'fmr'),
                                'type' => 'checkbox',
                                'cols' => 4,
                                'on_label' => __('Yes', 'fmr'),
                                'off_label' => __('No', 'fmr'),
                                'description' => __('Display search functionality', 'fmr'),
                            ],
                            [
                                'id' => 'show_advanced_search',
                                'label' => __('Show Advanced Search', 'fmr'),
                                'type' => 'checkbox',
                                'cols' => 4,
                                'on_label' => __('Yes', 'fmr'),
                                'off_label' => __('No', 'fmr'),
                                'description' => __('Display advanced search link', 'fmr'),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'api_settings',
                'label' => __('API Settings', 'fmr'),
                'tab' => 'api',
                'tab_label' => __('API', 'fmr'),
                'fields' => [
                    [
                        'id' => 'enable_api',
                        'label' => __('Enable API', 'fmr'),
                        'type' => 'checkbox',
                        'cols' => 6,
                        'on_label' => __('Yes', 'fmr'),
                        'off_label' => __('No', 'fmr'),
                        'description' => __('Enable API access for external integrations', 'fmr'),
                    ],
                    [
                        'id' => 'api_key',
                        'label' => __('API Key', 'fmr'),
                        'type' => 'key_generation',
                        'cols' => 6,
                        'description' => __('API key for external access', 'fmr'),
                        'optional' => true,
                    ],
                ],
            ],
        ];
    }
}
