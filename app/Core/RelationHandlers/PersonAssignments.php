<?php

namespace App\Core\RelationHandlers;

use App\Core\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\AssignmentController;
use Illuminate\Http\Request;

class PersonAssignments extends RelationHandler
{
    protected static $post_type = 'person';
    protected static $meta_box_id = 'person_assignments';
    protected static $meta_box_title = 'Assignments';
    protected static $priority = 'low';
    
    protected static $config = [
        'entity' => 'Assignment',
        'entity_plural' => 'Assignments',
        'storage_key' => '_assignments_data',
        'fields' => [
            [
                'key' => 'decision_authority_id',
                'type' => 'select',
                'label' => 'Decision Authority',
                'required' => true,
                'options' => [],
                'relation_field' => 'decision_authority',
                'relation_title_key' => 'title',
            ],
            [
                'key' => 'role',
                'type' => 'text',
                'label' => 'Role',
                'required' => true,
                'is_title' => true,
            ],
            [
                'key' => 'period_start',
                'type' => 'date',
                'label' => 'Start Date',
                'required' => true,
                'is_subtitle' => true,
            ],
            [
                'key' => 'period_end',
                'type' => 'date',
                'label' => 'End Date',
                'required' => false,
            ],
        ],
        'grouping_field' => 'period_end',
        'grouping_logic' => 'date_based',
    ];

    protected function loadExistingData($post_id)
    {
        $controller = new AssignmentController();
        $assignments = $controller->getPersonsAssignments($post_id)->toArray();
            
        $this->loadDecisionAuthorities();
            
        return $assignments;
    }

    protected function loadDecisionAuthorities()
    {
        $authorities = \App\Models\DecisionAuthority::with('board')
            ->orderBy('title')
            ->get()
            ->map(function ($authority) {
                return [
                    'id' => $authority->id,
                    'title' => $authority->title . ' (' . $authority->board->post_title . ')',
                ];
            });

        $authorityField = array_search('decision_authority_id', array_column(static::$config['fields'], 'key'));
        if ($authorityField !== false) {
            static::$config['fields'][$authorityField]['options'] = $authorities->pluck('title', 'id')->toArray();
        }
    }

    protected function processRelationData($post_id, $relation_data)
    {
        $data = json_decode(stripslashes($relation_data), true);
        
        if (!$data) {
            return;
        }

        $controller = new AssignmentController();
        
        // Get existing assignments for this person
        $existing_assignments = $this->loadExistingData($post_id);
        $existing_ids = array_column($existing_assignments, 'id');
        
        // Process each assignment
        foreach ($data as $assignment_data) {
            $assignment_data['person_id'] = $post_id;
            
            if (isset($assignment_data['id']) && in_array($assignment_data['id'], $existing_ids)) {
                $controller->update(new Request($assignment_data), $assignment_data['id']);
            } else {
                $controller->store(new Request($assignment_data));
            }
        }
        
        // Delete assignments that are no longer in the data
        $submitted_ids = array_filter(array_column($data, 'id'));
        $assignments_to_delete = array_diff($existing_ids, $submitted_ids);
        
        foreach ($assignments_to_delete as $id) {
            $controller->destroy($id);
        }
    }
}
