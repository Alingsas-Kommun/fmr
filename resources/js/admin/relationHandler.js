/**
 * Creates a relation handler instance for managing dynamic form relationships
 * @param {Object} config - Configuration object for the relation handler
 * @param {Array} config.fields - Array of field configuration objects
 * @param {string} config.grouping_field - Field key used for grouping relations (typically end date)
 * @param {string} config.storage_key - Key for storing serialized data in hidden input
 * @param {Array} [existingData=[]] - Initial data array of existing relations
 * @returns {Object} Relation handler instance with methods and properties
 */
export function relationHandler(config, existingData = []) {
    return {
        /** @type {Object} Configuration object passed to the handler */
        config: config,
        /** @type {Array} Array of relation objects */
        relations: existingData || [],
        /** @type {Set} Set of expanded relation IDs */
        expandedRelations: new Set(),
        /** @type {Object} Grouped relations by status (ongoing/historical) */
        groupedRelations: {
            ongoing: [],
            historical: []
        },
        /** @type {Object} New relation object being created */
        newRelation: {},
        /** @type {boolean} Whether to show the new relation form */
        showNewForm: false,
        /** @type {boolean} Loading state indicator */
        loading: false,
        
        /**
         * Initializes the relation handler
         * Sets up relations, new relation form, grouping, and date validation
         */
        init() {
            this.loading = true;
            
            // Use setTimeout to ensure the loading state is visible before processing
            setTimeout(() => {
                this.initializeRelations();
                this.initializeNewRelation();
                this.groupRelations();
                this.setupDateValidation();
                this.loading = false;
            }, 100);
        },
        
        /**
         * Initializes existing relations with default properties
         * Adds isNew and hasChanges flags to all relations
         */
        initializeRelations() {
            this.relations = this.relations.map(relation => ({
                ...relation,
                isNew: false,
                hasChanges: false,
            }));
        },
        
        /**
         * Initializes the new relation object with default values
         * Sets empty strings for text fields and false for checkboxes
         */
        initializeNewRelation() {
            this.newRelation = this.config.fields.reduce((acc, field) => {
                acc[field.key] = field.type === 'checkbox' ? false : '';
                return acc;
            }, {});
        },
        
        /**
         * Groups relations into ongoing and historical categories
         * Ongoing relations have no end date or end date >= today
         * Historical relations have end date < today
         * Sorts ongoing by start date (newest first) and historical by end date (newest first)
         */
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
        
        /**
         * Gets the start date field key from configuration
         * @returns {string} The field key for the start date field, defaults to 'period_start'
         */
        getDateField() {
            return this.config.fields.find(f => f.type === 'date' && 
                !f.key.includes('end'))?.key || 'period_start';
        },
        
        /**
         * Toggles the expanded state of a relation
         * @param {string} relationId - The ID of the relation to toggle
         */
        toggleRelation(relationId) {
            if (this.expandedRelations.has(relationId)) {
                this.expandedRelations.delete(relationId);
            } else {
                this.expandedRelations.add(relationId);
            }
        },
        
        /**
         * Checks if a relation is currently expanded
         * @param {string} relationId - The ID of the relation to check
         * @returns {boolean} True if the relation is expanded
         */
        isExpanded(relationId) {
            return this.expandedRelations.has(relationId);
        },
        
        /**
         * Adds a new relation to the relations array
         * Validates the new relation data before adding
         * Updates relation objects for fields with relation configuration
         * Expands the new relation and hides the form
         */
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
        
        /**
         * Validates the new relation form data
         * Checks all required fields and marks invalid fields
         * @returns {boolean} True if all required fields are valid
         */
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
        
        /**
         * Shows validation error message to the user
         * Placeholder for future implementation
         */
        showValidationError() {
            // Show validation error message
        },
        
        /**
         * Marks a field as invalid by adding CSS class
         * @param {string} fieldKey - The key of the field to mark as invalid
         */
        markFieldAsInvalid(fieldKey) {
            // Add invalid class to the field container
            const fieldElement = document.querySelector(`[data-field-key="${fieldKey}"]`);
            if (fieldElement) {
                fieldElement.classList.add('field-invalid');
            }
        },
        
        /**
         * Clears all validation error states
         * Removes the 'field-invalid' class from all field containers
         */
        clearValidationErrors() {
            // Remove invalid class from all field containers
            const invalidFields = document.querySelectorAll('.field-invalid');
            invalidFields.forEach(field => {
                field.classList.remove('field-invalid');
            });
        },
        
        /**
         * Updates a specific field value in a relation
         * @param {string} relationId - The ID of the relation to update
         * @param {string} fieldKey - The field key to update
         * @param {*} value - The new value for the field
         */
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
        
        /**
         * Clears validation error for a specific field
         * @param {string} fieldKey - The key of the field to clear validation for
         */
        clearFieldValidation(fieldKey) {
            // Clear validation for a specific field when user starts typing
            const fieldElement = document.querySelector(`[data-field-key="${fieldKey}"]`);
            if (fieldElement) {
                fieldElement.classList.remove('field-invalid');
            }
        },
        
        /**
         * Formats a date string for HTML date input fields
         * @param {string} dateString - The date string to format
         * @returns {string} Formatted date string (YYYY-MM-DD) or empty string
         */
        formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        },
        
        /**
         * Updates relation object when select field changes
         * Creates a relation object with id and title for select fields
         * @param {Object} relation - The relation object to update
         * @param {Object} field - The field configuration
         * @param {string} value - The selected value
         */
        updateRelationObject(relation, field, value) {
            if (!value) {
                relation[field.relation_field] = null;
                return;
            }
            
            // Check if this field should store simple values instead of relation objects
            // If relation_field is the same as the field key, store simple value
            if (field.relation_field === field.key) {
                relation[field.relation_field] = value;
                return;
            }
            
            // Find the option label from field options
            if (field.options && field.options[value]) {
                const relationObject = {
                    id: value
                };
                
                // Use relation_title_key if defined, otherwise use 'title' as default
                const titleKey = field.relation_title_key || 'title';
                relationObject[titleKey] = field.options[value];
                
                relation[field.relation_field] = relationObject;
            }
        },
        
        /**
         * Deletes a relation after user confirmation
         * @param {string} relationId - The ID of the relation to delete
         */
        deleteRelation(relationId) {
            if (confirm('Are you sure you want to delete this item?')) {
                this.relations = this.relations.filter(r => r.id !== relationId);
                this.expandedRelations.delete(relationId);
                this.groupRelations();
                this.markAsChanged();
            }
        },
        
        /**
         * Marks the form as changed and updates the hidden input
         * Serializes the relations data to JSON for form submission
         */
        markAsChanged() {
            // Update the hidden input with serialized data
            const hiddenInput = document.getElementById(this.config.storage_key);
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(this.relations);
            }
        },
        
        /**
         * Gets the options array for a specific field
         * @param {string} fieldKey - The key of the field to get options for
         * @returns {Array} Array of field options or empty array
         */
        getFieldOptions(fieldKey) {
            const field = this.config.fields.find(f => f.key === fieldKey);
            return field?.options || [];
        },
        
        /**
         * Sets up date validation for date fields
         * Placeholder for future date validation implementation
         */
        setupDateValidation() {
            // Add date validation logic if needed
            const dateFields = this.config.fields.filter(f => f.type === 'date');
            // Implementation for date validation
        },
        
        /**
         * Generates a title for a relation based on the field marked as is_title
         * @param {Object} relation - The relation object
         * @returns {string} The title for the relation or 'Untitled'
         */
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
        
        /**
         * Generates a subtitle for a relation based on date fields
         * @param {Object} relation - The relation object
         * @returns {string} The subtitle for the relation or empty string
         */
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
        
        /**
         * Gets the display value for a relation field or regular field
         * First tries to get the relation field value, then falls back to regular field value
         * @param {Object} relation - The relation object
         * @returns {string} The display value for the field or empty string
         */
        getRelationFieldValue(relation) {
            const relationField = this.config.fields.find(f => f.relation_field);
            
            if (!relationField) {
                return '';
            }
            
            // Try to get relation field value first
            const titleKey = relationField.relation_title_key || 'title';
            if (relation[relationField.relation_field] && relation[relationField.relation_field][titleKey]) {
                return relation[relationField.relation_field][titleKey];
            }
            
            // Fall back to regular field value
            const fieldValue = relation[relationField.key];
            if (fieldValue) {
                // If it's a select field, look up the label from options
                if (relationField.type === 'select' && relationField.options && relationField.options[fieldValue]) {
                    return relationField.options[fieldValue];
                }
                return fieldValue;
            }

            return '';
        },
    }
}
