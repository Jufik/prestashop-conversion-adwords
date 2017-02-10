<?php
if (!defined('_CAN_LOAD_FILES_'))
    exit;


class Googleadword extends Module {
    
    public function __construct($name = NULL) {
        $this->name = "VingtCinq Goolge adwords module"
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Jufik';
        parent::__construct($name);
        $this->displayName = $this->l('Conversions Google Adwords');
        $this->description = $this->l('Ajoute le tag de conversion pour google adwords sur le hook de confirmation de commande.');
    }
    
    /**
    * install module *
    * @return bool
    */
    public function install(){
        if(!parent::install()
                || !$this->registerHook('orderConfirmation')
                || !Configuration::updateValue('google_adwords_id', '') 
                || !Configuration::updateValue('google_adwords_label', ''))
            return false;
        return true;
    }
    
    /**
    * uninstall module
    * @return bool
    */
    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('google_adwords_id', '') 
            || !Configuration::deleteByName('google_adwords_label', ''))
        )
        return false;

        return true;
    }
    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();

        // Module, t    oken and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Google Adword General Settings'),
            ),
            'input' => array(
                //add enable&disable switch
                // google_adwords_id
                // google_adwords_label
                // facebook_pixel_id
                array(
                    'type' => 'text',
                    'label' => $this->l('Adwords Tracking label'),
                    'name' => 'google_adwords_label',
                    'size' => 20,
                    'required' => true,
                    'hint'=>'Get tracking Id from Google Adword'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adwords Tracking Id'),
                    'name' => 'google_adwords_id',
                    'size' => 20,
                    'required' => true,
                    'hint'=>'Get tracking Id from Google Adword'
                ),

            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
        // Load current value
        $helper->fields_value['google_adwords_label'] = Configuration::get('google_adwords_label');
        $helper->fields_value['google_adwords_id'] = Configuration::get('google_adwords_id');

        return $helper->generateForm($fields_form);

    }


    /**
    * back office module configuration page content
    */
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit'.$this->name))
        {
            $error = false;
            $google_adwords_label = Tools::getValue('google_adwords_label');
            $google_adwords_id = Tools::getValue('google_adwords_id');

            if (!$error)
            {
                Configuration::updateValue('google_adwords_label', $google_adwords_label);
                Configuration::updateValue('google_adwords_id', $google_adwords_id);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    /**
    * hook page header to add CSS and JS files
    */
    public function hookDisplayHeader()
    {
        if (Configuration::get('google_adwords_label') != '' && Configuration::get('google_adwords_id') != '')
        {
            $this->context->smarty->assign(
                array(
                    'google_adwords_id' => Configuration::get('google_adwords_id'),
                    'google_adwords_label' => Configuration::get('google_adwords_label'),
                )
            );
            return $this->display(__FILE__, 'hookDisplayHeader.tpl');
        }

    }
}

