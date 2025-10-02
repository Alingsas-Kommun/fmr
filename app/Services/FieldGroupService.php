<?php

namespace App\Services;

use App\Utilities\Dir;
use Illuminate\Support\Str;

class FieldGroupService
{
    /**
     * Get expected meta fields for a post type based on field group definitions
     */
    public static function getExpectedFieldsForPostType(string $postType): array
    {
        $fieldGroups = self::getFieldGroupsForPostType($postType);
        $allFields = [];

        foreach ($fieldGroups as $fieldGroupClass) {
            $fields = self::getFieldsFromGroup($fieldGroupClass);
            $allFields = array_merge($allFields, $fields);
        }

        return $allFields;
    }

    /**
     * Get all field group classes for a specific post type
     */
    protected static function getFieldGroupsForPostType(string $postType): array
    {
        $fieldGroups = [];
        $dir = __DIR__ . '/../Core/Admin/FieldGroups';
        $fieldGroupFiles = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Admin\\FieldGroups\\';

        foreach ($fieldGroupFiles as $file) {
            $className = $namespace . basename($file, '.php');
            
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                
                // Check if this field group handles the requested post type
                if ($reflection->hasProperty('post_types')) {
                    $postTypesProperty = $reflection->getProperty('post_types');
                    $postTypesProperty->setAccessible(true);
                    
                    // Get static property value
                    $postTypes = $postTypesProperty->getValue();
                    
                    if (is_array($postTypes) && in_array($postType, $postTypes)) {
                        $fieldGroups[] = $className;
                    }
                }
            }
        }

        return $fieldGroups;
    }

    /**
     * Get fields from a specific field group class
     */
    protected static function getFieldsFromGroup(string $fieldGroupClass): array
    {
        $reflection = new \ReflectionClass($fieldGroupClass);
        $getFieldsMethod = $reflection->getMethod('getFields');
        $getFieldsMethod->setAccessible(true);
        
        // Create a temporary instance just to call the method
        $tempInstance = $reflection->newInstanceWithoutConstructor();
        $fields = $getFieldsMethod->invoke($tempInstance);
        
        return self::extractFieldsFromGroup($fields);
    }

    /**
     * Extract field definitions from field group structure
     */
    protected static function extractFieldsFromGroup(array $groups): array
    {
        $fields = [];

        foreach ($groups as $group) {
            if (isset($group['fields']) && is_array($group['fields'])) {
                foreach ($group['fields'] as $row) {
                    if (isset($row['fields']) && is_array($row['fields'])) {
                        // Complex configuration with rows
                        foreach ($row['fields'] as $field) {
                            if (isset($field['id'])) {
                                $type = $field['type'] ?? 'text';
                                $fields[$field['id']] = [
                                    'id' => $field['id'],
                                    'type' => $type,
                                    'label' => $field['label'] ?? '',
                                    'optional' => $field['optional'] ?? false,
                                    'default' => self::getDefaultValueForType($type),
                                ];
                            }
                        }
                    } elseif (isset($row['id'])) {
                        // Simple configuration - this row is actually a field
                        $type = $row['type'] ?? 'text';
                        $fields[$row['id']] = [
                            'id' => $row['id'],
                            'type' => $type,
                            'label' => $row['label'] ?? '',
                            'optional' => $row['optional'] ?? false,
                            'default' => self::getDefaultValueForType($type),
                        ];
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Cast value based on field type (handles both existing values and defaults)
     */
    public static function castValueByType($value, string $type): mixed
    {
        // Handle special types that need specific casting
        return match($type) {
            'checkbox' => $value === '1' || $value === 1 || $value === true,
            'number', 'range' => is_numeric($value) ? (float) $value : 0,
            'post_relation', 'taxonomy_relation' => is_numeric($value) ? (int) $value : '',
            'image' => is_numeric($value) ? (int) $value : null,
            default => (string) $value
        };
    }

    /**
     * Get default value for a field type
     */
    public static function getDefaultValueForType(string $type): mixed
    {
        return self::castValueByType(null, $type);
    }

    /**
     * Get default value for a field based on its definition
     */
    public static function getDefaultValueForField(array $fieldDefinition): mixed
    {
        return $fieldDefinition['default'] ?? self::getDefaultValueForType($fieldDefinition['type'] ?? 'text');
    }

    /**
     * Check if a field should be included in the meta object
     */
    public static function shouldIncludeField(string $fieldId): bool
    {
        // Skip system fields
        $systemFields = ['_edit_lock', '_edit_last', '_thumbnail_id'];
        if (in_array($fieldId, $systemFields)) {
            return false;
        }

        // Skip visibility fields
        if (str_ends_with($fieldId, '_visibility')) {
            return false;
        }

        return true;
    }

    /**
     * Remove prefix from field key based on post type
     */
    public static function removePrefix(string $key, string $postType): string
    {
        $prefix = $postType . '_';
        
        if (Str::startsWith($key, $prefix)) {
            return Str::replace($prefix, '', $key);
        }
        
        return $key;
    }

    /**
     * Get relation field IDs for a post type (fields that should be excluded from meta)
     */
    public static function getRelationFieldIds(string $postType): array
    {
        $expectedFields = self::getExpectedFieldsForPostType($postType);
        $relationFields = [];
        
        foreach ($expectedFields as $fieldId => $fieldDefinition) {
            $type = $fieldDefinition['type'] ?? 'text';
            
            // Identify relation field types
            if (in_array($type, ['post_relation', 'taxonomy_relation'])) {
                $relationFields[] = $fieldId;
            }
        }
        
        return $relationFields;
    }

    /**
     * Format meta values for display with proper casting and prefix removal
     */
    public static function formatMetaValues(array $rawMeta, string $postType, array $excludeFields = []): array
    {
        $formatted = [];
        $expectedFields = self::getExpectedFieldsForPostType($postType);
        $relationFields = self::getRelationFieldIds($postType);
        $allExcludedFields = array_merge($relationFields, $excludeFields);
        
        // First, add all expected fields with default values (excluding relation fields and excluded fields)
        foreach ($expectedFields as $fieldId => $fieldDefinition) {
            if (!self::shouldIncludeField($fieldId) || in_array($fieldId, $allExcludedFields)) {
                continue;
            }
            
            $cleanKey = self::removePrefix($fieldId, $postType);
            $value = $rawMeta[$fieldId] ?? null;
            $formatted[$cleanKey] = self::castValueByType($value, $fieldDefinition['type']);
        }
        
        // Then, add any additional fields from raw meta that aren't in expected fields
        foreach ($rawMeta as $key => $value) {
            if (!self::shouldIncludeField($key)) {
                continue;
            }
            
            // Skip if already processed in expected fields
            if (isset($expectedFields[$key])) {
                continue;
            }
            
            $cleanKey = self::removePrefix($key, $postType);
            $formatted[$cleanKey] = self::castValueByType($value, 'text'); // Default to text type
        }
        
        return $formatted;
    }
}
