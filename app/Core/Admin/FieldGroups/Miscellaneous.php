<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\FieldGroup;

class Miscellaneous extends FieldGroup
{
    protected static $post_types = ['person', 'party', 'board'];
    protected static $id = 'miscellaneous';
    protected static $priority = 'low';
    protected static $context = 'side';

    protected function getTitle()
    {
        return __('Miscellaneous', 'fmr');
    }

    protected function getFields()
    {
        return [
            [
                'fields' => [
                    [
                        'id' => 'notes',
                        'label' => __('Notes', 'fmr'),
                        'type' => 'textarea',
                        'cols' => 12,
                        'optional' => true,
                    ],
                ],
            ],
        ];
    }
}