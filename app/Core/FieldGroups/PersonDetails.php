<?php

namespace App\Core\FieldGroups;

use App\Core\Abstracts\FieldGroup;

class PersonDetails extends FieldGroup
{
    protected static $post_type = 'person';
    protected static $id = 'person_details';

    protected function getTitle()
    {
        return __('Person Details', 'fmr');
    }

    protected function getTabs()
    {
        return [
            'basic' => [
                'label' => __('Basic Information', 'fmr'),
            ],
            'addresses' => [
                'label' => __('Addresses', 'fmr'),
            ],
        ];
    }

    protected function getFields()
    {
        return [
            [
                'id' => 'personal_info',
                'label' => __('Personal Information', 'fmr'),
                'tab' => 'basic',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'person_firstname',
                                'label' => __('Firstname', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                            ],
                            [
                                'id' => 'person_lastname',
                                'label' => __('Lastname', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_birth_date',
                                'label' => __('Date of birth', 'fmr'),
                                'type' => 'date',
                                'cols' => 6,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_ssn',
                                'label' => __('Social security number', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'politics_info',
                'label' => __('Politics Information', 'fmr'),
                'tab' => 'basic',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'person_party',
                                'label' => __('Member of party', 'fmr'),
                                'type' => 'select',
                                'cols' => 6,
                                'options' => [
                                    '' => __('Select party', 'fmr'),
                                    'party1' => __('Party 1', 'fmr'),
                                    'party2' => __('Party 2', 'fmr'),
                                ],
                            ],
                            [
                                'id' => 'person_kilometers',
                                'label' => __('Total kilometers', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_active',
                                'label' => __('Active', 'fmr'),
                                'type' => 'checkbox',
                                'cols' => 4,
                                'on_label' => __('Yes', 'fmr'),
                                'off_label' => __('No', 'fmr'),
                            ],
                            [
                                'id' => 'person_group_leader',
                                'label' => __('Group leader', 'fmr'),
                                'type' => 'checkbox',
                                'cols' => 4,
                                'on_label' => __('Yes', 'fmr'),
                                'off_label' => __('No', 'fmr'),
                            ],
                            [
                                'id' => 'person_listing',
                                'label' => __('Listing', 'fmr'),
                                'type' => 'text',
                                'cols' => 4,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'home_info',
                'label' => __('Home Information', 'fmr'),
                'tab' => 'addresses',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'person_home_visiting_address',
                                'label' => __('Visiting address', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_home_address',
                                'label' => __('Address', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_home_zip',
                                'label' => __('Zip code', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_home_city',
                                'label' => __('City', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_home_webpage',
                                'label' => __('Webpage', 'fmr'),
                                'type' => 'url',
                                'cols' => 12,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_home_email',
                                'label' => __('Email', 'fmr'),
                                'type' => 'email',
                                'cols' => 12,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_home_phone',
                                'label' => __('Phone', 'fmr'),
                                'type' => 'tel',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_home_mobile',
                                'label' => __('Mobile', 'fmr'),
                                'type' => 'tel',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'work_info',
                'label' => __('Work Information', 'fmr'),
                'tab' => 'addresses',
                'fields' => [
                    [
                        'fields' => [
                            [
                                'id' => 'person_work_visiting_address',
                                'label' => __('Visiting address', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_work_address',
                                'label' => __('Address', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_work_zip',
                                'label' => __('Zip code', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_work_city',
                                'label' => __('City', 'fmr'),
                                'type' => 'text',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_work_webpage',
                                'label' => __('Webpage', 'fmr'),
                                'type' => 'url',
                                'cols' => 12,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_work_email',
                                'label' => __('Email', 'fmr'),
                                'type' => 'email',
                                'cols' => 12,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'fields' => [
                            [
                                'id' => 'person_work_phone',
                                'label' => __('Phone', 'fmr'),
                                'type' => 'tel',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                            [
                                'id' => 'person_work_mobile',
                                'label' => __('Mobile', 'fmr'),
                                'type' => 'tel',
                                'cols' => 6,
                                'optional' => true,
                                'visibility' => [
                                    'default' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}