<?php

namespace App\Core\Admin\DecisionAuthorities\MetaBoxes;

use App\Models\Term;
use Illuminate\Http\Request;
use App\Core\Admin\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\AssignmentController;

class Assignments extends RelationHandler
{
    /**
     * The meta box id for the decision authority assignments
     *
     * @var string
     */ 
    protected static $meta_box_id = 'decision_authority_assignments';

    /**
     * The context for the decision authority assignments
     *
     * @var string
     */
    protected static $context = 'normal';

    /**
     * The priority for the decision authority assignments
     *
     * @var string
     */
    protected static $priority = 'high';

    /**
     * Get the title for the decision authority assignments
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Assignments', 'fmr');
    }

    /**
     * Get the config for the decision authority assignments
     *
     * @return array
     */
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

    /**
     * Load the existing data for the decision authority assignments
     *
     * @param int $decision_authority_id
     * @return array
     */
    protected function loadExistingData($decision_authority_id)
    {
        if (!$decision_authority_id) {
            return [];
        }

        $controller = app(AssignmentController::class);

        return $controller->getByDecisionAuthority($decision_authority_id);
    }

    /**
     * Process the relation data for the decision authority assignments
     *
     * @param int $decision_authority_id
     * @param array $relation_data
     * @return void
     */
    protected function processRelationData($decision_authority_id, $relation_data)
    {
        $data = json_decode(stripslashes($relation_data), true);
        
        if (!$data) {
            return;
        }

        $controller = app(AssignmentController::class);
        
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

    /**
     * Get the persons for the decision authority assignments
     *
     * @return array
     */
    private function getPersons()
    {
        $personController = app(PersonController::class);
        $persons = $personController->getAll();
        
        $options = ['' => __('Select Person', 'fmr')];

        foreach ($persons as $person) {
            $options[$person->ID] = $person->post_title;
        }
        
        return $options;
    }

    /**
     * Get the roles for the decision authority assignments
     *
     * @return array
     */
    private function getRoles()
    {
        $roleController = app(RoleController::class);
        $terms = $roleController->getAll();
        
        $options = ['' => __('Select Role', 'fmr')];

        foreach ($terms as $term) {
            $options[$term->term_id] = $term->name;
        }
        
        return $options;
    }
}
