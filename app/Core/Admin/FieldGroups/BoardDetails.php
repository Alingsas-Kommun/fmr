<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\FieldGroup;

class BoardDetails extends FieldGroup
{
    /**
     * The post types for the board details
     *
     * @var array
     */
    protected static $post_types = ['board'];
    
    /**
     * The ID for the board details
     *
     * @var string
     */
    protected static $id = 'board_details';

    /**
     * Get the title for the board details
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Board Details', 'fmr');
    }

    /**
     * Get the fields for the board details
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            [
                'id' => 'basic_info',
                'label' => __('Basic Information', 'fmr'),
                'tab' => 'general',
                'tab_label' => __('General', 'fmr'),
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'board_category',
                                'label' => __('Category', 'fmr'),
                                'type' => 'taxonomy_relation',
                                'taxonomy' => 'type',
                                'cols' => 12,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'board_shortening',
                                'label' => __('Shortening', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 12,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'contact_details',
                'label' => __('Contact Details', 'fmr'),
                'tab' => 'contact',
                'tab_label' => __('Contact Details', 'fmr'),
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'board_visiting_address',
                                'label' => __('Visiting address', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 12,
                            ],
                            [
                                'id' => 'board_address',
                                'label' => __('Address', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 12,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'board_zip',
                                'label' => __('ZIP Code', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 4,
                            ],
                            [
                                'id' => 'board_city',
                                'label' => __('City', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 8,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'board_website',
                                'label' => __('Website', 'fmr'),
                                'type' => 'url',
                                'optional' => true,
                                'cols' => 12,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'board_email',
                                'label' => __('E-mail', 'fmr'),
                                'type' => 'email',
                                'optional' => true,
                                'visibility' => [
                                    'default' => true,
                                ],
                                'cols' => 6,
                            ],
                            [
                                'id' => 'board_phone',
                                'label' => __('Phone', 'fmr'),
                                'type' => 'tel',
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                                'cols' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}