<?php

namespace App\Core\Admin\RelationHandlers;

use App\Core\Admin\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Http\Request;

class PersonAssignments extends RelationHandler
{
    /**
     * The post type for the person assignments
     *
     * @var string
     */
    protected static $post_type = 'person';

    /**
     * The meta box id for the person assignments
     *
     * @var string
     */
    protected static $meta_box_id = 'person_assignments';

    /**
     * The priority for the person assignments
     *
     * @var string
     */
    protected static $priority = 'low';

    /**
     * The decision authorities for the person assignments
     *
     * @var array
     */
    protected static $decision_authorities = [];

    /**
     * The role terms for the person assignments
     *
     * @var array
     */
    protected static $role_terms = [];

    /**
     * Get the title for the person assignments
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Assignments', 'fmr');
    }
    
    /**
     * Get the config for the person assignments
     *
     * @return array
     */
    protected function getConfig()
    {
        return [
            'entity' => __('assignment', 'fmr'),
            'entity_plural' => __('assignments', 'fmr'),
            'storage_key' => '_assignments_data',
            'fields' => [
                [
                    'key' => 'decision_authority_id',
                    'type' => 'select-grouped',
                    'label' => __('Decision Authority', 'fmr'),
                    'options' => static::$decision_authorities,
                    'relation_field' => 'decision_authority',
                    'relation_title_key' => 'title',
                    'cols' => 7,
                ],
                [
                    'key' => 'role_term_id',
                    'type' => 'select',
                    'label' => __('Role', 'fmr'),
                    'is_title' => true,
                    'options' => static::$role_terms,
                    'cols' => 5,
                ],
                [
                    'key' => 'period_start',
                    'type' => 'date',
                    'label' => __('Start Date', 'fmr'),
                    'is_subtitle' => true,
                    'cols' => 6,
                ],
                [
                    'key' => 'period_end',
                    'type' => 'date',
                    'label' => __('End Date', 'fmr'),
                    'cols' => 6,
                ],
            ],
            'grouping_field' => 'period_end',
            'grouping_logic' => 'date_based',
        ];
    }

    /**
     * Load the existing data for the person assignments
     *
     * @param int $post_id
     * @return array
     */
    protected function loadExistingData($post_id)
    {
        $this->loadDecisionAuthorities();
        $this->loadRoleTerms();
        
        $controller = app(AssignmentController::class);
        $assignments = $controller->getPersonsAssignments($post_id)->toArray();
            
        return $assignments;
    }

    /**
     * Load the decision authorities for the person assignments
     *
     * @return void
     */
    protected function loadDecisionAuthorities()
    {
        $controller = app(DecisionAuthorityController::class);
        $authorities = $controller->getAll();
        
        $groupedAuthorities = [];
        
        foreach ($authorities as $authority) {
            $boardTitle = $authority->board->post_title;
            $timePeriod = $this->formatTimePeriod($authority->start_date, $authority->end_date);
            $title = $authority->title . ' (' . $timePeriod . ')';
            
            if (!isset($groupedAuthorities[$boardTitle])) {
                $groupedAuthorities[$boardTitle] = [];
            }
            
            $groupedAuthorities[$boardTitle][$authority->id] = $title;
        }

        static::$decision_authorities = $groupedAuthorities;
    }
    
    /**
     * Format the time period for the person assignments
     *
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    protected function formatTimePeriod($startDate, $endDate)
    {
        $start = $startDate ? date('Y-m-d', strtotime($startDate)) : '';
        $end = $endDate ? date('Y-m-d', strtotime($endDate)) : __('ongoing', 'fmr');
        
        return $start . ' - ' . $end;
    }

    /**
     * Load the role terms for the person assignments
     *
     * @return void
     */
    protected function loadRoleTerms()
    {
        $roleController = app(RoleController::class);
        $roleTerms = $roleController->getAll();

        static::$role_terms = $roleTerms->pluck('name', 'term_id')->toArray();
    }

    /**
     * Process the relation data for the person assignments
     *
     * @param int $post_id
     * @param array $relation_data
     * @return void
     */
    protected function processRelationData($post_id, $relation_data)
    {
        $data = json_decode(stripslashes($relation_data), true);
        
        if (!$data) {
            return;
        }

        $controller = app(AssignmentController::class);
        
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
