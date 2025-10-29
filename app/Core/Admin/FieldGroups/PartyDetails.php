<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\FieldGroup;

class PartyDetails extends FieldGroup
{
    protected static $post_types = ['party'];
    protected static $id = 'party_details';
    protected static $priority = 'high';

    protected function getTitle()
    {
        return __('Party Details', 'fmr');
    }

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
                                'id' => 'party_description',
                                'label' => __('Description', 'fmr'),
                                'type' => 'textarea',
                                'optional' => true,
                                'cols' => 12,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'party_shortening',
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
                'tab_label' => __('Contact Details', 'fmr'),
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'party_address',
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
                                'id' => 'party_zip',
                                'label' => __('ZIP Code', 'fmr'),
                                'type' => 'text',
                                'optional' => true,
                                'cols' => 4,
                            ],
                            [
                                'id' => 'party_city',
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
                                'id' => 'party_website',
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
                                'id' => 'party_email',
                                'label' => __('E-mail', 'fmr'),
                                'type' => 'email',
                                'optional' => true,
                                'visibility' => [
                                    'default' => true,
                                ],
                                'cols' => 6,
                            ],
                            [
                                'id' => 'party_phone',
                                'label' => __('Phone', 'fmr'),
                                'type' => 'number',
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