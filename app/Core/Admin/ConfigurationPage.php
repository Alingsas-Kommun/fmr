<?php

namespace App\Core\Admin;

use App\Core\Admin\Abstracts\OptionsPage;
use App\Core\Admin\FieldGroups\General;

class ConfigurationPage extends OptionsPage
{
    /**
     * Initialize the properties for the configuration page
     *
     * @return void
     */
    protected function initializeProperties()
    {
        $this->parentMenuSlug = 'options-general.php';
        $this->menuTitle = __('Configuration', 'fmr');
        $this->pageSlug = 'configuration';
        $this->pageTitle = __('Configuration', 'fmr');
        $this->capability = 'manage_options';
        $this->nonceAction = 'fmr_configuration_nonce_action';
        $this->nonceField = 'fmr_configuration_nonce';
        $this->formAction = 'fmr_configuration_save';
        $this->redirectPage = 'configuration';
        $this->position = '1';
    }

    /**
     * Register the field groups for the configuration page
     *
     * @return void
     */
    protected function registerFieldGroups()
    {
        $this->addFieldGroup(app(General::class));
    }
}
