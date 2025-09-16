<?php

namespace App\Core\Admin\Abstracts;

abstract class MetaBox
{
    /**
     * The meta box ID.
     */
    protected $id;

    /**
     * The meta box title.
     */
    protected $title;

    /**
     * The meta box context.
     */
    protected $context = 'normal';

    /**
     * The meta box priority.
     */
    protected $priority = 'default';

    /**
     * The edit page instance.
     */
    protected $editPage;

    /**
     * Constructor.
     */
    public function __construct(EditPage $editPage)
    {
        $this->editPage = $editPage;
        $this->initializeProperties();
        $this->register();
    }

    /**
     * Initialize the meta box properties.
     */
    abstract protected function initializeProperties();

    /**
     * Render the meta box content.
     */
    abstract public function render($object, $box);

    /**
     * Handle saving the meta box data.
     */
    abstract public function save($data, $object);

    /**
     * Handle the save action hook.
     */
    public function handleSave($object, $data, $id)
    {
        $this->save($data, $object);
    }

    /**
     * Register the meta box with the edit page.
     */
    protected function register()
    {
        $this->editPage->addMetaBox(
            $this->id,
            $this->title,
            [$this, 'render'],
            $this->context,
            $this->priority
        );
        
        // Register the save hook
        $this->registerSaveHook();
    }

    /**
     * Register the save hook for this meta box.
     */
    protected function registerSaveHook()
    {
        $pageSlug = $this->editPage->getPageSlug();
        add_action("save_{$pageSlug}", [$this, 'handleSave'], 10, 3);
    }

    /**
     * Add a meta box to the edit page.
     */
    protected function addMetaBox($id, $title, $callback, $context = 'normal', $priority = 'default')
    {
        add_meta_box(
            $id,
            $title,
            $callback,
            $this->editPage->getHook(),
            $context,
            $priority
        );
    }

    /**
     * Get a field value from the current object.
     */
    public function getFieldValue($field, $default = '')
    {
        $object = $this->editPage->getCurrentObjectInstance();
        
        if (!$object) {
            return $default;
        }

        // Handle Laravel Eloquent models
        if (is_object($object) && method_exists($object, 'getAttribute')) {
            $value = $object->getAttribute($field);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("getFieldValue: Field '{$field}' = " . ($value ?? 'NULL') . " (Eloquent model)");
            }
            return $value ?? $default;
        }

        // Handle regular objects
        if (is_object($object) && property_exists($object, $field)) {
            $value = $object->$field;
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("getFieldValue: Field '{$field}' = " . ($value ?? 'NULL') . " (regular object)");
            }
            return $value;
        }

        // Handle arrays
        if (is_array($object) && array_key_exists($field, $object)) {
            $value = $object[$field];
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("getFieldValue: Field '{$field}' = " . ($value ?? 'NULL') . " (array)");
            }
            return $value;
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("getFieldValue: Field '{$field}' not found, returning default: " . $default);
        }
        return $default;
    }
}
