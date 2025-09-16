<?php

namespace App\Core\Admin\DecisionAuthorities\MetaBoxes;

use App\Core\Admin\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\PersonController;
use App\Models\Term;
use Illuminate\Http\Request;

class Assignments extends RelationHandler
{
    protected static $meta_box_id = 'decision_authority_assignments';
    protected static $context = 'normal';
    protected static $priority = 'high';

    protected function getTitle()
    {
        return __('Assignments', 'fmr');
    }

    protected function getConfig()
    {
        return [
            'storage_key' => 'decision_authority_assignments_data',
            'entity' => __('assignment', 'fmr'),
            'entity_plural' => __('assignments', 'fmr'),
            'fields' => [
                [
                    'key' => 'person_id',
                    'type' => 'select',
                    'label' => __('Person', 'fmr'),
                    'options' => $this->getPersons(),
                    'relation_field' => 'person',
                    'relation_title_key' => 'post_title',
                    'cols' => 3,
                ],
                [
                    'key' => 'role_term_id',
                    'type' => 'select',
                    'label' => __('Role', 'fmr'),
                    'is_title' => true,
                    'options' => $this->getRoles(),
                    'cols' => 3,
                ],
                [
                    'key' => 'period_start',
                    'type' => 'date',
                    'label' => __('Start Date', 'fmr'),
                    'is_subtitle' => true,
                    'cols' => 3,
                ],
                [
                    'key' => 'period_end',
                    'type' => 'date',
                    'label' => __('End Date', 'fmr'),
                    'cols' => 3,
                ]
            ],
            'grouping_field' => 'period_end',
            'grouping_logic' => 'date_based',
        ];
    }

    protected function loadExistingData($decision_authority_id)
    {
        if (!$decision_authority_id) {
            return [];
        }

        $controller = new AssignmentController();

        return $controller->getByDecisionAuthority($decision_authority_id);
    }

    protected function processRelationData($decision_authority_id, $relation_data)
    {
        $data = json_decode(stripslashes($relation_data), true);
        
        if (!$data) {
            return;
        }

        $controller = new AssignmentController();
        
        $existing_assignments = $this->loadExistingData($decision_authority_id);
        $existing_ids = array_column($existing_assignments->toArray(), 'id');
        
        foreach ($data as $assignment_data) {
            $assignment_data['decision_authority_id'] = $decision_authority_id;
            
            foreach (['period_start', 'period_end'] as $field) {
                if (!empty($assignment_data[$field])) {
                    $assignment_data[$field] = date('Y-m-d', strtotime($assignment_data[$field]));
                }
            }
            
            if (isset($assignment_data['id']) && in_array($assignment_data['id'], $existing_ids)) {
                $controller->update(new Request($assignment_data), $assignment_data['id']);
            } else {
                $controller->store(new Request($assignment_data));
            }
        }
        
        $submitted_ids = array_filter(array_column($data, 'id'));
        $assignments_to_delete = array_diff($existing_ids, $submitted_ids);
        
        foreach ($assignments_to_delete as $id) {
            $controller->destroy($id);
        }
    }

    private function getPersons()
    {
        $personController = new PersonController();
        $persons = $personController->getAll();
        
        $options = ['' => __('Select Person', 'fmr')];

        foreach ($persons as $person) {
            $options[$person->ID] = $person->post_title;
        }
        
        return $options;
    }

    private function getRoles()
    {
        $terms = Term::whereHas('termTaxonomy', function ($query) {
            $query->where('taxonomy', 'role');
        })->orderBy('name')->get();
        
        $options = ['' => __('Select Role', 'fmr')];

        foreach ($terms as $term) {
            $options[$term->term_id] = $term->name;
        }
        
        return $options;
    }
}
