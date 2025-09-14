<div class="relation-row">
    <div @click="toggleRelation(relation.id)" class="relation-header">
        <div class="relation-header-content">
            <div class="relation-header-info">
                <strong x-text="getRelationTitle(relation)"></strong>
                <span x-show="getRelationSubtitle(relation)" class="relation-subtitle" x-text="getRelationSubtitle(relation)"></span>
                <span x-show="getRelationFieldValue(relation)" class="relation-field-value" x-text="getRelationFieldValue(relation)"></span>
                <span x-show="relation.hasChanges" class="relation-unsaved-changes">{!! __('Unsaved changes', 'fmr') !!}</span>
                <span x-show="relation.isNew" class="relation-new">{!! __('New', 'fmr') !!}</span>
            </div>

            <div class="relation-header-actions">
                <button type="button" @click.stop="deleteRelation(relation.id)" class="relation-delete-btn">
                    <span class="dashicons dashicons-trash"></span>
                </button>

                <span x-show="isExpanded(relation.id)" class="relation-toggle-icon dashicons dashicons-arrow-up-alt2"></span>
                <span x-show="!isExpanded(relation.id)" class="relation-toggle-icon dashicons dashicons-arrow-down-alt2"></span>
            </div>
        </div>
    </div>

    <div x-show="isExpanded(relation.id)" class="relation-row-form" style="display: none;">
        @include('admin.relation-handler.form', ['fields' => $config['fields'], 'prefix' => 'relation', 'model' => 'relation', 'relationId' => 'relation.id'])
    </div>
</div>
