<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\FieldGroup;

class BoardDetails extends FieldGroup
{
    protected static $post_types = ['board'];
    protected static $id = 'board_details';

    protected function getTitle()
    {
        return __('Board Details', 'fmr');
    }

    protected function getTabs()
    {
        return [
            'general' => [
                'label' => __('General', 'fmr'),
            ],
            'contact' => [
                'label' => __('Contact Details', 'fmr'),
            ],
        ];
    }

    protected function getFields()
    {
        return [
            [
                'id' => 'basic_info',
                'label' => __('Basic Information', 'fmr'),
                'tab' => 'general',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'board_category',
                                'label' => __('Category', 'fmr'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Select type', 'fmr'),
                                    'company' => __('Company', 'fmr'),
                                    'council' => __('Council', 'fmr'),
                                    'committee' => __('Committee', 'fmr'),
                                    'foundation' => __('Foundation', 'fmr'),
                                    'other' => __('Other', 'fmr'),
                                ],
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