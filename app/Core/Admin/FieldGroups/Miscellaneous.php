<?php

namespace App\Core\Admin\FieldGroups;

use App\Core\Admin\Abstracts\FieldGroup;

class Miscellaneous extends FieldGroup
{
    /**
     * The post types for the miscellaneous settings
     *
     * @var array
     */
    protected static $post_types = ['person', 'party', 'board'];

    /**
     * The ID for the miscellaneous settings
     *
     * @var string
     */
    protected static $id = 'miscellaneous';

    /**
     * The priority for the miscellaneous settings
     *
     * @var string
     */
    protected static $priority = 'low';

    /**
     * The context for the miscellaneous settings
     *
     * @var string
     */
    protected static $context = 'side';

    /**
     * Get the title for the miscellaneous settings
     *
     * @return string
     */

    protected function getTitle()
    {
        return __('Miscellaneous', 'fmr');
    }

    /**
     * Get the fields for the miscellaneous settings
     *
     * @return array
     */
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