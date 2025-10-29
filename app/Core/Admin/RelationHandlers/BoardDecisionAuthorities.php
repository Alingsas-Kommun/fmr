<?php

namespace App\Core\Admin\RelationHandlers;

use App\Core\Admin\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\TypeController;
use Illuminate\Http\Request;

class BoardDecisionAuthorities extends RelationHandler
{
    /**
     * The post type for the board decision authorities
     *
     * @var string
     */
    protected static $post_type = 'board';

    /**
     * The meta box id for the board decision authorities
     *
     * @var string
     */
    protected static $meta_box_id = 'board_decision_authorities';

    /**
     * The priority for the board decision authorities
     *
     * @var string
     */
    protected static $priority = 'low';

    /**
     * The type terms for the board decision authorities
     *
     * @var array
     */
    protected static $type_terms = [];

    /**
     * Get the title for the board decision authorities
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Decision Authorities', 'fmr');
    }

    /**
     * Get the config for the board decision authorities
     *
     * @return array
     */
    protected function getConfig()
    {
        return [
            'entity' => __('decision authority', 'fmr'),
            'entity_plural' => __('decision authorities', 'fmr'),
            'storage_key' => '_decision_authorities_data',
            'fields' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'label' => __('Title', 'fmr'),
                    'is_title' => true,
                    'cols' => 3,
                ],
                [
                    'key' => 'type_term_id',
                    'type' => 'select',
                    'label' => __('Type', 'fmr'),
                    'options' => static::$type_terms,
                    'cols' => 3,
                ],
                [
                    'key' => 'start_date',
                    'type' => 'date',
                    'label' => __('Start Date', 'fmr'),
                    'is_subtitle' => true,
                    'cols' => 3,
                ],
                [
                    'key' => 'end_date',
                    'type' => 'date',
                    'label' => __('End Date', 'fmr'),
                    'cols' => 3,
                ],
            ],
            'grouping_field' => 'end_date',
            'grouping_logic' => 'date_based',
        ];
    }

    /**
     * Load the existing data for the board decision authorities
     *
     * @param int $post_id
     * @return array
     */
    protected function loadExistingData($post_id)
    {
        $this->loadTypeTerms();
        
        $controller = app(DecisionAuthorityController::class);
        $decision_authorities = $controller->getDecisionAuthoritiesForBoard($post_id)->toArray();
            
        return $decision_authorities;
    }

    /**
     * Load the type terms for the board decision authorities
     *
     * @return void
     */
    protected function loadTypeTerms()
    {
        $typeController = app(TypeController::class);
        $typeTerms = $typeController->getAll();

        static::$type_terms = $typeTerms->pluck('name', 'term_id')->toArray();
    }

    /**
     * Process the relation data for the board decision authorities
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

        $controller = app(DecisionAuthorityController::class);
        
        // Get existing decision authorities for this board
        $existing_decision_authorities = $this->loadExistingData($post_id);
        $existing_ids = array_column($existing_decision_authorities, 'id');
        
        // Process each decision authority
        foreach ($data as $decision_authority_data) {
            $decision_authority_data['board_id'] = $post_id;
            
            if (isset($decision_authority_data['id']) && in_array($decision_authority_data['id'], $existing_ids)) {
                $controller->update(new Request($decision_authority_data), $decision_authority_data['id']);
            } else {
                $controller->store(new Request($decision_authority_data));
            }
        }
        
        // Delete decision authorities that are no longer in the data
        $submitted_ids = array_filter(array_column($data, 'id'));
        $decision_authorities_to_delete = array_diff($existing_ids, $submitted_ids);
        
        foreach ($decision_authorities_to_delete as $id) {
            $controller->destroy($id);
        }
    }
}
