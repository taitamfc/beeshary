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

class AdminMpStoreConfigurationController extends ModuleAdminController
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
        $form = $this->generalConfiguration();
        $form .= $this->storeConfiguration();
        return $form;
    }

    public function generalConfiguration()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('General Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Geolocation API Key'),
                    'name' => 'MP_GEOLOCATION_API_KEY',
                    'required' => true,
                    'hint' => $this->l('Unique API key for geolocation google map'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Seller can manage store status'),
                    'name' => 'MP_STORE_LOCATION_ACTIVATION',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'hint' => $this->l('If No, Sellers can not activate their store location status. Admin have to approve first.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display all seller\'s stores'),
                    'name' => 'MP_STORE_ALL_SELLER',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'hint' => $this->l('If Yes, User can see all seller\'s stores in one page'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l("Display map on product page"),
                    'name' => 'MP_STORE_DISPLAY_PRODUCT_MAP',
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
                    'label' => $this->l('Display stores on product page'),
                    'name' => 'MP_STORE_PRODUCT_TAB',
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
                    'label' => $this->l('Display map on homepage'),
                    'name' => 'MP_STORE_HOME_PAGE',
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
                    'label' => $this->l('Enable store page'),
                    'name' => 'MP_STORE_STORE_PAGE',
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'btnSubmitGeneralconfig',
            ),
        );
        unset($this->fields_value);
        $this->fields_value = array(
            'MP_GEOLOCATION_API_KEY' => Tools::getValue(
                'MP_GEOLOCATION_API_KEY',
                Configuration::get('MP_GEOLOCATION_API_KEY')
            ),
            'MP_STORE_PRODUCT_TAB' => Tools::getValue(
                'MP_STORE_PRODUCT_TAB',
                Configuration::get('MP_STORE_PRODUCT_TAB')
            ),
            'MP_STORE_STORE_PAGE' => Tools::getValue(
                'MP_STORE_STORE_PAGE',
                Configuration::get('MP_STORE_STORE_PAGE')
            ),
            'MP_STORE_HOME_PAGE' => Tools::getValue(
                'MP_STORE_HOME_PAGE',
                Configuration::get('MP_STORE_HOME_PAGE')
            ),
            'MP_STORE_DISPLAY_PRODUCT_MAP' => Tools::getValue(
                'MP_STORE_DISPLAY_PRODUCT_MAP',
                Configuration::get('MP_STORE_DISPLAY_PRODUCT_MAP')
            ),
            'MP_STORE_LOCATION_ACTIVATION' => Tools::getValue('MP_STORE_LOCATION_ACTIVATION', Configuration::get('MP_STORE_LOCATION_ACTIVATION')),
            'MP_STORE_ALL_SELLER' => Tools::getValue('MP_STORE_ALL_SELLER', Configuration::get('MP_STORE_ALL_SELLER')),
        );
        return parent::renderForm();
    }
    
    public function storeConfiguration()
    {
        $distanceUnit = array(
            array(
                'id' => 'METRIC',
                'type' => $this->l('Km'),
                'val' => 1
            ),
            array(
                'id' => 'IMPERIAL',
                'type' => $this->l('Miles'),
                'val' => 2
            )
        );
        $zoomLevel = array();
        for ($i=0; $i<=22; $i++) {
            $zoomLevel[] = array(
                'id' => $i,
                'type' => $i,
            );
        }
        $logoName = Configuration::get('MP_STORE_MARKER_NAME');
        $psImgUrl = _PS_MODULE_DIR_.'mpstorelocator/views/img/'.$logoName;
        if ($imgExist = file_exists($psImgUrl)) {
            $imgUrl = _MODULE_DIR_.'mpstorelocator/views/img/'.$logoName;
            $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$imgUrl."'>";
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Store Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Distance Unit'),
                    'name' => 'MP_STORE_DISTANCE_UNIT',
                    'options' => array(
                        'query' => $distanceUnit,
                        'id' => 'id',
                        'name' => 'type'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l("Set zoom level"),
                    'name' => 'MP_STORE_MAP_ZOOM_ENABLE',
                    'required' => true,
                    'form_group_class' => 'mp_store_map',
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
                    'label' => $this->l('Default Minimum Zoom'),
                    'name' => 'MP_STORE_MAP_ZOOM',
                    'options' => array(
                        'query' => $zoomLevel,
                        'id' => 'id',
                        'name' => 'type'
                    ),
                    'desc' => $this->l('Set 0 (lowest zoom level) and 22 (maximum zoom level)')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l("Enable Search by Product"),
                    'name' => 'MP_STORE_SEARCH_BY_PRODUCT',
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
                    'label' => $this->l("Display cluster on map"),
                    'name' => 'MP_STORE_CLUSTER',
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
                    'label' => $this->l('Auto detect user location'),
                    'name' => 'MP_AUTO_LOCATE',
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
                    'label' => $this->l("Display store timing"),
                    'name' => 'MP_DISPLAY_STORE_TIMING',
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
                    'label' => $this->l("Display email of store"),
                    'name' => 'MP_STORE_DISPLAY_EMAIL',
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
                    'label' => $this->l("Display fax number of store"),
                    'name' => 'MP_STORE_DISPLAY_FAX',
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
                    'label' => $this->l("Display contact details of store"),
                    'name' => 'MP_STORE_CONTACT_DETAILS',
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'btnSubmitStoreConfig',
            ),
        );
        unset($this->fields_value);
        $this->fields_value = array(
            'MP_AUTO_LOCATE' => Tools::getValue(
                'MP_AUTO_LOCATE',
                Configuration::get('MP_AUTO_LOCATE')
            ),
            'MP_STORE_CLUSTER' => Tools::getValue(
                'MP_STORE_CLUSTER',
                Configuration::get('MP_STORE_CLUSTER')
            ),
            'MP_STORE_SEARCH_BY_PRODUCT' => Tools::getValue(
                'MP_STORE_SEARCH_BY_PRODUCT',
                Configuration::get('MP_STORE_SEARCH_BY_PRODUCT')
            ),
            'MP_STORE_DISTANCE_UNIT' => Tools::getValue(
                'MP_STORE_DISTANCE_UNIT',
                Configuration::get('MP_STORE_DISTANCE_UNIT')
            ),
            'MP_STORE_CONTACT_DETAILS' => Tools::getValue(
                'MP_STORE_CONTACT_DETAILS',
                Configuration::get('MP_STORE_CONTACT_DETAILS')
            ),
            'MP_STORE_DISPLAY_FAX' => Tools::getValue(
                'MP_STORE_DISPLAY_FAX',
                Configuration::get('MP_STORE_DISPLAY_FAX')
            ),
            'MP_STORE_MAP_ZOOM' => Tools::getValue(
                'MP_STORE_MAP_ZOOM',
                Configuration::get('MP_STORE_MAP_ZOOM')
            ),
            'MP_STORE_MAP_ZOOM_ENABLE' => Tools::getValue(
                'MP_STORE_MAP_ZOOM_ENABLE',
                Configuration::get('MP_STORE_MAP_ZOOM_ENABLE')
            ),
            'MP_STORE_DISPLAY_EMAIL' => Tools::getValue(
                'MP_STORE_DISPLAY_EMAIL',
                Configuration::get('MP_STORE_DISPLAY_EMAIL')
            ),
            'MP_DISPLAY_STORE_TIMING' => Tools::getValue(
                'MP_DISPLAY_STORE_TIMING',
                Configuration::get('MP_DISPLAY_STORE_TIMING')
            ),
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmitGeneralconfig')) {
            $this->configDataValidation();
            if (!count($this->errors)) {
                $this->configDataProcess();
            }
        }
        if (Tools::isSubmit('btnSubmitStoreConfig')) {
            if (!count($this->errors)) {
                $this->storeConfigDataProcess();
            }
        }
        parent::postProcess();
    }

    private function configDataProcess()
    {
        if (Tools::isSubmit('btnSubmitGeneralconfig')) {
            Configuration::updateValue('MP_GEOLOCATION_API_KEY', Tools::getValue('MP_GEOLOCATION_API_KEY'));
            Configuration::updateValue('MP_STORE_LOCATION_ACTIVATION', Tools::getValue('MP_STORE_LOCATION_ACTIVATION'));
            Configuration::updateValue('MP_STORE_ALL_SELLER', Tools::getValue('MP_STORE_ALL_SELLER'));
            Configuration::updateValue('MP_STORE_STORE_PAGE', Tools::getValue('MP_STORE_STORE_PAGE'));
            Configuration::updateValue('MP_STORE_HOME_PAGE', Tools::getValue('MP_STORE_HOME_PAGE'));
            Configuration::updateValue('MP_STORE_DISPLAY_PRODUCT_MAP', Tools::getValue('MP_STORE_DISPLAY_PRODUCT_MAP'));
            
            Configuration::updateValue('MP_STORE_PRODUCT_TAB', Tools::getValue('MP_STORE_PRODUCT_TAB'));

            $moduleConfig = $this->context->link->getAdminLink('AdminMpStoreConfiguration');
            Tools::redirectAdmin(
                $moduleConfig.'&conf=4'
            );
        }
    }
    private function storeConfigDataProcess()
    {
        if (Tools::isSubmit('btnSubmitStoreConfig')) {
            Configuration::updateValue('MP_STORE_CONTACT_DETAILS', Tools::getValue('MP_STORE_CONTACT_DETAILS'));
            Configuration::updateValue('MP_STORE_DISPLAY_FAX', Tools::getValue('MP_STORE_DISPLAY_FAX'));
            Configuration::updateValue('MP_STORE_DISPLAY_EMAIL', Tools::getValue('MP_STORE_DISPLAY_EMAIL'));
            Configuration::updateValue('MP_DISPLAY_STORE_TIMING', Tools::getValue('MP_DISPLAY_STORE_TIMING'));

            Configuration::updateValue('MP_AUTO_LOCATE', Tools::getValue('MP_AUTO_LOCATE'));
            Configuration::updateValue('MP_STORE_CLUSTER', Tools::getValue('MP_STORE_CLUSTER'));
            Configuration::updateValue('MP_STORE_SEARCH_BY_PRODUCT', Tools::getValue('MP_STORE_SEARCH_BY_PRODUCT'));
            Configuration::updateValue('MP_STORE_DISTANCE_UNIT', Tools::getValue('MP_STORE_DISTANCE_UNIT'));
            Configuration::updateValue('MP_STORE_MAP_ZOOM_ENABLE', Tools::getValue('MP_STORE_MAP_ZOOM_ENABLE'));
            if (Tools::getValue('MP_STORE_MAP_ZOOM_ENABLE')) {
                Configuration::updateValue('MP_STORE_MAP_ZOOM', Tools::getValue('MP_STORE_MAP_ZOOM'));
            }
            $moduleConfig = $this->context->link->getAdminLink('AdminMpStoreConfiguration');
            Tools::redirectAdmin(
                $moduleConfig.'&conf=4'
            );
        }
    }

    private function configDataValidation()
    {
        if (Tools::isSubmit('btnSubmitGeneralconfig')) {
            if (Tools::getValue('MP_GEOLOCATION_API_KEY') == '') {
                $this->errors[] = $this->l('Geolocation API KEY is required.');
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->context->controller->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/mpstoreconfig.js');
    }
}
