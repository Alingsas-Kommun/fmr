<?php

namespace App\Core\Admin\Abstracts;

use Illuminate\Http\Request;
use function Roots\view;

abstract class EditPage
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
     * The route name.
     */
    protected $routeName;

    /**
     * The page title for the edit button.
     */
    protected $pageTitleEdit;

    /**
     * The page title for the add new button.
     */
    protected $addNewButtonTitle;


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
     * The button text for create operation.
     */
    protected $createButtonText;

    /**
     * The button text for update operation.
     */
    protected $updateButtonText;

    /**
     * Whether to show the title field above post boxes.
     */
    protected $showTitleField = true;

    /**
     * The current object being edited.
     */
    protected $currentObject;

    /**
     * Meta boxes registered for this page.
     */
    protected $metaBoxes = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initializeProperties();
        $this->initializeSubmitTexts();
        $this->registerHooks();
        $this->registerCustomMetaBoxes();
    }

    /**
     * Initialize the properties that must be defined by child classes.
     */
    abstract protected function initializeProperties();

    /**
     * Initialize button text properties with default values.
     * Override this method in child classes to customize button text.
     */
    protected function initializeSubmitTexts()
    {
        $this->createButtonText = __('Create', 'fmr');
        $this->updateButtonText = __('Update', 'fmr');
    }

    /**
     * Get the current object being edited.
     */
    abstract protected function getCurrentObject($id = null);

    /**
     * Handle the save operation.
     */
    abstract protected function handleSave(Request $request, $id = null);

    /**
     * Get the success message for create operation.
     */
    abstract protected function getCreateSuccessMessage();

    /**
     * Get the success message for update operation.
     */
    abstract protected function getUpdateSuccessMessage();

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
            [$this, 'renderEditScreen']
        );

        add_action("load-{$this->hook}", [$this, 'setupMetaBoxes']);
    }

    /**
     * Setup meta boxes for this page.
     */
    public function setupMetaBoxes()
    {
        // Register the publish meta box
        add_meta_box(
            $this->pageSlug . '_publish',
            __('Publish', 'fmr'),
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
    }

    /**
     * Register custom meta boxes. Override this method in child classes.
     */
    protected function registerCustomMetaBoxes()
    {
        // Override in child classes
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
     * Render the edit screen.
     */
    public function renderEditScreen()
    {   
        $id = $_GET['id'] ?? null;
        $this->currentObject = $this->getCurrentObject($id);

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

        echo view('admin.edit-page', [
            'pageTitle' => $id ? $this->pageTitleEdit : $this->pageTitle,
            'routeName' => $this->routeName,
            'addNewButtonTitle' => $this->addNewButtonTitle,
            'pageSlug' => $this->pageSlug,
            'nonceAction' => $this->nonceAction,
            'nonceField' => $this->nonceField,
            'formAction' => $this->formAction,
            'id' => $id,
            'hook' => $this->hook,
            'currentObject' => $this->currentObject,
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
            'showTitleField' => $this->showTitleField,
            'getFieldValue' => [$this, 'getFieldValue']
        ])->render();
    }

    /**
     * Render the publish meta box.
     */
    public function renderPublishMetaBox($object, $box)
    {
        $id = $_GET['id'] ?? null;
        $buttonText = $id ? $this->updateButtonText : $this->createButtonText;
        
        echo view('admin.meta-boxes.publish', [
            'buttonText' => $buttonText
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
        $id = $request->input('id');

        // Load the current object for meta box saving
        $this->currentObject = $this->getCurrentObject($id);

        try {
            // Call the main save handler first
            $result = $this->handleSave($request, $id);

            if ($result === false) {
                throw new \Exception(__('Failed to save the object.', 'fmr'));
            }
            
            // Fire action hook for meta boxes to listen to (similar to save_post)
            do_action("save_{$this->pageSlug}", $this->currentObject, $data, $id);

            $message = $id 
                ? $this->getUpdateSuccessMessage()
                : $this->getCreateSuccessMessage();

            wp_redirect(add_query_arg(
                [
                    'page' => $this->redirectPage, 
                    'updated' => 1,
                    'message' => urlencode($message),
                    'id' => $id
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
     * Get the current object being edited.
     */
    public function getCurrentObjectInstance()
    {
        return $this->currentObject;
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

    /**
     * Get field value from current object or POST data.
     */
    public function getFieldValue($field, $default = null)
    {
        // First check POST data (for form submission)
        if (isset($_POST[$field])) {
            return $_POST[$field];
        }
        
        // Then check current object
        if ($this->currentObject && isset($this->currentObject->{$field})) {
            return $this->currentObject->{$field};
        }
        
        return $default;
    }

}
