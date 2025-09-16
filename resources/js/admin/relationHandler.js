export function relationHandler(config, existingData = []) {
    return {
        config: config,
        relations: existingData || [],
        expandedRelations: new Set(),
        groupedRelations: {},
        newRelation: {},
        showNewForm: false,
        loading: false,
        
        init() {
            this.initializeRelations();
            this.initializeNewRelation();
            this.groupRelations();
            this.setupDateValidation();
        },
        
        initializeRelations() {
            this.relations = this.relations.map(relation => ({
                ...relation,
                isNew: false,
                hasChanges: false,
            }));
        },
        
        initializeNewRelation() {
            this.newRelation = this.config.fields.reduce((acc, field) => {
                acc[field.key] = field.type === 'checkbox' ? false : '';
                return acc;
            }, {});
        },
        
        groupRelations() {
            const today = new Date().toISOString().split('T')[0];
            const groupingField = this.config.grouping_field;
            
            this.groupedRelations = {
                ongoing: [],
                historical: []
            };
            
            this.relations.forEach(relation => {
                const endDate = relation[groupingField];
                const isOngoing = !endDate || endDate >= today;
                                
                if (isOngoing) {
                    this.groupedRelations.ongoing.push(relation);
                } else {
                    this.groupedRelations.historical.push(relation);
                }
            });
                        
            // Sort ongoing by start date (newest first)
            // Sort historical by end date (newest first)
            this.groupedRelations.ongoing.sort((a, b) => {
                const aStart = a[this.getDateField()] || '';
                const bStart = b[this.getDateField()] || '';
                return bStart.localeCompare(aStart);
            });
            
            this.groupedRelations.historical.sort((a, b) => {
                const aEnd = a[groupingField] || '';
                const bEnd = b[groupingField] || '';
                return bEnd.localeCompare(aEnd);
            });
        },
        
        getDateField() {
            return this.config.fields.find(f => f.type === 'date' && 
                !f.key.includes('end'))?.key || 'period_start';
        },
        
        toggleRelation(relationId) {
            if (this.expandedRelations.has(relationId)) {
                this.expandedRelations.delete(relationId);
            } else {
                this.expandedRelations.add(relationId);
            }
        },
        
        isExpanded(relationId) {
            return this.expandedRelations.has(relationId);
        },
        
        addRelation() {
            if (!this.validateNewRelation()) {
                this.showValidationError();
                return;
            }
            
            const newRelation = {
                ...this.newRelation,
                id: 'new_' + Date.now(),
                isNew: true,
                hasChanges: false,
            };
            
            // Update relation objects for fields with relation configuration
            this.config.fields.forEach(field => {
                if (field.relation_field && newRelation[field.key]) {
                    this.updateRelationObject(newRelation, field, newRelation[field.key]);
                }
            });
            
            this.relations.push(newRelation);
            this.initializeNewRelation();
            this.groupRelations();
            this.expandedRelations.add(newRelation.id);
            this.showNewForm = false; // Hide the form after adding
            
            // Mark as changed for save tracking
            this.markAsChanged();
        },
        
        validateNewRelation() {
            const requiredFields = this.config.fields.filter(f => !f.optional);
            let allValid = true;
            
            this.clearValidationErrors();
            
            requiredFields.forEach(field => {
                const value = this.newRelation[field.key];
                const isValid = value !== '' && value !== null && value !== undefined;
                
                if (!isValid) {
                    this.markFieldAsInvalid(field.key);
                    allValid = false;
                }
            });
            
            return allValid;
        },
        
        showValidationError() {
            // Show validation error message
        },
        
        markFieldAsInvalid(fieldKey) {
            // Add invalid class to the field container
            const fieldElement = document.querySelector(`[data-field-key="${fieldKey}"]`);
            if (fieldElement) {
                fieldElement.classList.add('field-invalid');
            }
        },
        
        clearValidationErrors() {
            // Remove invalid class from all field containers
            const invalidFields = document.querySelectorAll('.field-invalid');
            invalidFields.forEach(field => {
                field.classList.remove('field-invalid');
            });
        },
        
        updateRelation(relationId, fieldKey, value) {
            const relation = this.relations.find(r => r.id === relationId);
            if (relation) {
                relation[fieldKey] = value;
                relation.hasChanges = true;
                
                // Check if this field has relation configuration
                const field = this.config.fields.find(f => f.key === fieldKey);
                if (field && field.relation_field) {
                    this.updateRelationObject(relation, field, value);
                }
                
                this.markAsChanged();
                
                // Re-group relations after update
                this.groupRelations();
            }
        },
        
        clearFieldValidation(fieldKey) {
            // Clear validation for a specific field when user starts typing
            const fieldElement = document.querySelector(`[data-field-key="${fieldKey}"]`);
            if (fieldElement) {
                fieldElement.classList.remove('field-invalid');
            }
        },
        
        // Helper method to format date for input fields
        formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        },
        
        // Update relation object when select field changes
        updateRelationObject(relation, field, value) {
            if (!value) {
                relation[field.relation_field] = null;
                return;
            }
            
            // Find the option label from field options
            if (field.options && field.options[value]) {
                relation[field.relation_field] = {
                    id: value,
                    [field.relation_title_key]: field.options[value]
                };
            }
        },
        
        deleteRelation(relationId) {
            if (confirm('Are you sure you want to delete this item?')) {
                this.relations = this.relations.filter(r => r.id !== relationId);
                this.expandedRelations.delete(relationId);
                this.groupRelations();
                this.markAsChanged();
            }
        },
        
        markAsChanged() {
            // Update the hidden input with serialized data
            const hiddenInput = document.getElementById(this.config.storage_key);
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(this.relations);
            }
        },
        
        getFieldOptions(fieldKey) {
            const field = this.config.fields.find(f => f.key === fieldKey);
            return field?.options || [];
        },
        
        setupDateValidation() {
            // Add date validation logic if needed
            const dateFields = this.config.fields.filter(f => f.type === 'date');
            // Implementation for date validation
        },
        
        getRelationTitle(relation) {
            // Generate a title for the relation based on field marked as is_title
            const titleField = this.config.fields.find(f => f.is_title);
            if (!titleField) return 'Untitled';
            
            const value = relation[titleField.key];
            if (!value) return 'Untitled';
            
            // If it's a select field, look up the label from options
            if (titleField.type === 'select' && titleField.options) {
                return titleField.options[value] || value;
            }
            
            return value;
        },
        
        getRelationSubtitle(relation) {
            // Get subtitle field marked as is_subtitle
            const subtitleField = this.config.fields.find(f => f.is_subtitle);
            const endDateField = this.config.grouping_field;
            
            if (subtitleField) {
                const startDate = this.formatDateForInput(relation[subtitleField.key]);
                const endDate = this.formatDateForInput(relation[endDateField]);
                
                if (startDate && endDate) {
                    return `${startDate} - ${endDate}`;
                } else if (startDate) {
                    return `From ${startDate}`;
                }
            }
            
            return '';
        },
        
        getRelationFieldValue(relation) {
            // Find field with relation_field configuration
            const relationField = this.config.fields.find(f => f.relation_field);

            console.log(relationField);
            console.log(relation);
            
            if (relationField && relation[relationField.relation_field] && relation[relationField.relation_field][relationField.relation_title_key]) {
                return relation[relationField.relation_field][relationField.relation_title_key];
            }
            return '';
        },
    }
}

window.relationHandler = relationHandler