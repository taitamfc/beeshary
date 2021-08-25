<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMpStorePickUpConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $this->initToolbar();
        $this->display = '';
        $this->content .= $this->renderForm();

        $this->context->smarty->assign(
            array(
                'content' => $this->content,
            )
        );
    }

    public function renderForm()
    {
        if (!($this->loadObject(true))) {
            return;
        }
        $form = $this->pickupConfiguration();
        return $form;
    }

    public function pickupConfiguration()
    {
        $countries = Country::getCountries($this->context->language->id, true);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('General Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Store Pick up'),
                    'name' => 'MP_STORE_PICK_UP',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Country Restriction'),
                    'name' => 'MP_STORE_COUNTRY_ENABLE',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Countries'),
                    'name' => 'MP_STORE_COUNTRIES[]',
                    'form_group_class' => 'mp_store_country_restrict',
                    'multiple' => true,
                    'class' => 'mp-multiselect-countries',
                    'options' => array(
                        'query' => $countries,
                        'id' => 'id_country',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l("Enable pickup date selection"),
                    'name' => 'MP_STORE_PICKUP_DATE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'btnSubmitStorePickupConfig',
            ),
        );
        unset($this->fields_value);
        $this->fields_value = array(
            'MP_STORE_PICKUP_DATE' => Tools::getValue(
                'MP_STORE_PICKUP_DATE',
                Configuration::get('MP_STORE_PICKUP_DATE')
            ),
            'MP_STORE_PICK_UP' => Tools::getValue(
                'MP_STORE_PICK_UP',
                Configuration::get('MP_STORE_PICK_UP')
            ),
            'MP_STORE_COUNTRIES[]' => Tools::getValue(
                'MP_STORE_COUNTRIES',
                json_decode(Configuration::get('MP_STORE_COUNTRIES'))
            ),
            'MP_STORE_COUNTRY_ENABLE' => Tools::getValue(
                'MP_STORE_COUNTRY_ENABLE',
                Configuration::get('MP_STORE_COUNTRY_ENABLE')
            ),
        );
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmitStorePickupConfig')) {
            $this->configStorePickUpDataValidation();
            if (!count($this->errors)) {
                $this->configStorePickUpDataProcess();
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/pickup-store.js');
        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/bootstrap-multiselect.js');
        $this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/admin/bootstrap-multiselect.css');
    }

    private function configStorePickUpDataProcess()
    {
        if (Tools::isSubmit('btnSubmitStorePickupConfig')) {
            Configuration::updateValue('MP_STORE_PICKUP_DATE', Tools::getValue('MP_STORE_PICKUP_DATE'));

            Configuration::updateValue('MP_STORE_COUNTRY_ENABLE', Tools::getValue('MP_STORE_COUNTRY_ENABLE'));
            if (Configuration::get('MP_STORE_COUNTRY_ENABLE')) {
                Configuration::updateValue(
                    'MP_STORE_COUNTRIES', 
                    json_encode(Tools::getValue('MP_STORE_COUNTRIES'))
                );
            }

            Configuration::updateValue('MP_STORE_PICK_UP', Tools::getValue('MP_STORE_PICK_UP'));
            $objCarrier = new Carrier((int)Configuration::get('MP_STORE_ID_CARRIER'));
            $objCarrier->active = (int)Configuration::get('MP_STORE_PICK_UP');
            $objCarrier->save();
            $moduleConfig = $this->context->link->getAdminLink('AdminMpStorePickUpConfiguration');
            Tools::redirectAdmin(
                $moduleConfig.'&conf=4'
            );
        }
    }

    private function configStorePickUpDataValidation()
    {
        if (Tools::isSubmit('btnSubmitStorePickupConfig')) {
            if (Tools::getValue('MP_STORE_COUNTRY_ENABLE') && empty(Tools::getValue('MP_STORE_COUNTRIES'))) {
                $this->errors[] = $this->l('Select atleast 1 country');
            }
        }
    }
}
