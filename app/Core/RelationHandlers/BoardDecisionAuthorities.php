<?php

namespace App\Core\RelationHandlers;

use App\Core\Abstracts\RelationHandler;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use Illuminate\Http\Request;

class BoardDecisionAuthorities extends RelationHandler
{
    protected static $post_type = 'board';
    protected static $meta_box_id = 'board_decision_authorities';
    protected static $meta_box_title = 'Decision Authorities';
    protected static $priority = 'low';
    
    protected static $config = [
        'entity' => 'Decision Authority',
        'entity_plural' => 'Decision Authorities',
        'storage_key' => '_decision_authorities_data',
        'fields' => [
            [
                'key' => 'title',
                'type' => 'text',
                'label' => 'Title',
                'required' => true,
                'is_title' => true,
            ],
            [
                'key' => 'type',
                'type' => 'select',
                'label' => 'Type',
                'required' => true,
                'options' => [
                    'Nämnd' => 'Nämnd', 
                    'Styrelse' => 'Styrelse', 
                    'Utskott' => 'Utskott', 
                    'Beredning' => 'Beredning', 
                    'Råd' => 'Råd',
                ],
            ],
            [
                'key' => 'start_date',
                'type' => 'date',
                'label' => 'Start Date',
                'required' => true,
                'is_subtitle' => true,
            ],
            [
                'key' => 'end_date',
                'type' => 'date',
                'label' => 'End Date',
                'required' => false,
            ],
        ],
        'grouping_field' => 'end_date',
        'grouping_logic' => 'date_based',
    ];

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
