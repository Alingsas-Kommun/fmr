<?php

namespace App\Core\Admin\Abstracts;

use Illuminate\Http\Request;
use function Roots\view;

/**
 * Abstract OptionsEditPage class for creating settings pages with field groups
 * 
 * Integrates with OptionsFieldGroup to create settings panels that store data in options table
 */
abstract class OptionsPage
{
    /**
     * The admin page hook suffix.
     */
    protected $hook;

    /**
     * The parent menu slug.
     */
    protected $parentMenuSlug;

    /**
     * The menu title.
     */
    protected $menuTitle;

    /**
     * The page slug.
     */
    protected $pageSlug;

    /**
     * The page title.
     */
    protected $pageTitle;

    /**
     * The position of the page in the menu.
     */
    protected $position = '50';

    /**
     * The capability required to access this page.
     */
    protected $capability = 'manage_options';

    /**
     * The nonce action for form submission.
     */
    protected $nonceAction;

    /**
     * The nonce field name.
     */
    protected $nonceField;

    /**
     * The form action for submission.
     */
    protected $formAction;

    /**
     * The redirect page after successful save.
     */
    protected $redirectPage;

    /**
     * The button text for save operation.
     */
    protected $saveButtonText;

    /**
     * Meta boxes registered for this page.
     */
    protected $metaBoxes = [];

    /**
     * Field groups registered for this page.
     */
    protected $fieldGroups = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initializeProperties();
        $this->initializeSubmitTexts();
        $this->registerHooks();
        $this->registerFieldGroups();
    }

    /**
     * Initialize the properties that must be defined by child classes.
     */
    abstract protected function initializeProperties();

    /**
     * Register field groups for this page.
     */
    abstract protected function registerFieldGroups();

    /**
     * Initialize button text properties with default values.
     * Override this method in child classes to customize button text.
     */
    protected function initializeSubmitTexts()
    {
        $this->saveButtonText = __('Save Configuration', 'fmr');
    }

    /**
     * Register WordPress hooks.
     */
    protected function registerHooks()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action("admin_post_{$this->formAction}", [$this, 'handleFormSubmission']);
    }

    /**
     * Register the admin menu.
     */
    public function registerMenu()
    {
        $this->hook = add_submenu_page(
            $this->parentMenuSlug,
            $this->pageTitle,
            $this->menuTitle,
            $this->capability,
            $this->pageSlug,
            [$this, 'renderEditScreen'],
            $this->position
        );

        add_action("load-{$this->hook}", [$this, 'setupMetaBoxes']);
    }

    /**
     * Setup meta boxes for this page.
     */
    public function setupMetaBoxes()
    {
        // Register screen options for metabox position persistence
        add_screen_option('layout_columns', [
            'max' => 2,
            'default' => 2
        ]);

        // Register the publish meta box
        add_meta_box(
            $this->pageSlug . '_publish',
            __('Save Configuration', 'fmr'),
            [$this, 'renderPublishMetaBox'],
            $this->hook,
            'side',
            'default'
        );

        // Register all stored meta boxes
        foreach ($this->metaBoxes as $metaBox) {
            add_meta_box(
                $metaBox['id'],
                $metaBox['title'],
                $metaBox['callback'],
                $this->hook,
                $metaBox['context'],
                $metaBox['priority']
            );
        }

        // Register field groups as meta boxes
        foreach ($this->fieldGroups as $fieldGroup) {
            $fieldGroup->registerWithOptionsPage($this);
        }
    }

    /**
     * Add a meta box to the page.
     */
    public function addMetaBox($id, $title, $callback, $context = 'normal', $priority = 'default')
    {
        // Store meta box info for later registration
        $this->metaBoxes[] = [
            'id' => $id,
            'title' => $title,
            'callback' => $callback,
            'context' => $context,
            'priority' => $priority
        ];

        // If hook is available, register immediately
        if ($this->hook) {
            add_meta_box(
                $id,
                $title,
                $callback,
                $this->hook,
                $context,
                $priority
            );
        }
    }

    /**
     * Add a field group to the page.
     */
    public function addFieldGroup($fieldGroup)
    {
        $this->fieldGroups[] = $fieldGroup;
    }

    /**
     * Render the edit screen.
     */
    public function renderEditScreen()
    {   
        // Check for error message
        $errorMessage = '';
        if (isset($_GET['error']) && $_GET['error'] == 1 && isset($_GET['message'])) {
            $errorMessage = urldecode($_GET['message']);
        }

        // Check for success message
        $successMessage = '';
        if (isset($_GET['updated']) && $_GET['updated'] == 1 && isset($_GET['message'])) {
            $successMessage = urldecode($_GET['message']);
        }

        echo view('admin.options-edit-page', [
            'pageTitle' => $this->pageTitle,
            'pageSlug' => $this->pageSlug,
            'nonceAction' => $this->nonceAction,
            'nonceField' => $this->nonceField,
            'formAction' => $this->formAction,
            'hook' => $this->hook,
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
        ])->render();
    }

    /**
     * Render the publish meta box.
     */
    public function renderPublishMetaBox($object, $box)
    {
        echo view('admin.meta-boxes.publish', [
            'buttonText' => $this->saveButtonText
        ])->render();
    }

    /**
     * Handle form submission.
     */
    public function handleFormSubmission()
    {
        if (!current_user_can($this->capability)) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'fmr'));
        }

        if (!wp_verify_nonce($_POST[$this->nonceField] ?? '', $this->nonceAction)) {
            wp_die(__('Security check failed.', 'fmr'));
        }

        $data = $_POST;
        $request = new Request($data);

        try {
            // Trigger field group save actions
            foreach ($this->fieldGroups as $fieldGroup) {
                $fieldGroup->handleSave();
            }

            wp_redirect(add_query_arg(
                [
                    'page' => $this->redirectPage, 
                    'updated' => 1,
                ],
                admin_url('admin.php')
            ));
            exit;

        } catch (\Exception $e) {
            wp_redirect(add_query_arg(
                [
                    'page' => $this->pageSlug, 
                    'error' => 1,
                    'message' => urlencode($e->getMessage())
                ],
                admin_url('admin.php')
            ));
            exit;
        }
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueueAdminAssets($hookSuffix)
    {
        if ($hookSuffix !== $this->hook) {
            return;
        }

        wp_enqueue_script('postbox');
        wp_enqueue_script('jquery-ui-sortable');
    }

    /**
     * Get the page hook.
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Get the page slug.
     */
    public function getPageSlug()
    {
        return $this->pageSlug;
    }
}
