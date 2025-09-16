<?php

namespace App\Core\Admin\RelationHandlers;

use App\Core\Admin\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use Illuminate\Http\Request;

class BoardDecisionAuthorities extends RelationHandler
{
    protected static $post_type = 'board';
    protected static $meta_box_id = 'board_decision_authorities';
    protected static $priority = 'low';

    protected function getTitle()
    {
        return __('Decision Authorities', 'fmr');
    }

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
                    'key' => 'type',
                    'type' => 'select',
                    'label' => __('Type', 'fmr'),
                    'relation_field' => 'type',
                    'options' => [
                        'Nämnd' => 'Nämnd', 
                        'Styrelse' => 'Styrelse', 
                        'Utskott' => 'Utskott', 
                        'Beredning' => 'Beredning', 
                        'Råd' => 'Råd',
                    ],
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

    protected function loadExistingData($post_id)
    {
        $controller = new DecisionAuthorityController();
        $decision_authorities = $controller->getDecisionAuthoritiesForBoard($post_id)->toArray();
            
        return $decision_authorities;
    }

    protected function processRelationData($post_id, $relation_data)
    {
        $data = json_decode(stripslashes($relation_data), true);
        
        if (!$data) {
            return;
        }

        $controller = new DecisionAuthorityController();
        
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
