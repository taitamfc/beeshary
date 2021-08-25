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

class MpStoreLocatorStoreListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->id) {
            $idCustomer = $this->context->customer->id;
            $idLang = $this->context->language->id;

            $objMpSeller = new WkMpSeller();
            $mpSeller = $objMpSeller->getSellerDetailByCustomerId($idCustomer);
            if ($mpSeller && $mpSeller['active']) {
                $storeConfiguration = MpStoreConfiguration::getStoreConfiguration($mpSeller['id_seller']);
                if (empty($storeConfiguration)) {
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'mpstorelocator',
                            'storeconfiguration',
                            array(
                                'addConfig' => 1
                            )
                        )
                    );
                }
                if ($selectedProducts = Tools::getValue('mp_product_selected')) {
                    $this->deleteSelectedStores($selectedProducts);
                }

                //change product status
                if (Tools::getValue('mp_store_status')) {
                    $this->changeProductStatus($mpSeller['id_seller']);
                }
                $this->context->smarty->assign('logic', 'manage_store_list');
                $idSeller = $mpSeller['id_seller'];
                $sellerStores = MarketplaceStoreLocator::getSellerStore($idSeller);
                if ($sellerStores) {
                    //get store products
                    $mpProducts = MarketplaceStoreProduct::getMpSellerActiveProducts($idSeller);
                    if ($mpProducts) {
                        foreach ($mpProducts as &$product) {
                            $obj_product = new Product($product['id_product'], false, $idLang);
                            $product['product_name'] = $obj_product->name;
                        }
                        $this->context->smarty->assign('mp_products', $mpProducts);
                    }

                    // get store location details
                    foreach ($sellerStores as &$store) {
                        $obj_country = new Country($store['country_id'], $idLang);
                        $obj_state = new State($store['state_id']);
                        $store['country_name'] = $obj_country->name;
                        $store['state_name'] = $obj_state->name;

                        if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
                            $store['img_exist'] = 1;
                        } else {
                            $store['img_exist'] = 0;
                        }
                    }
                    $storeLocationsJson = Tools::jsonEncode($sellerStores);
                    $this->context->smarty->assign(array(
                        'id_seller' => $idSeller,
                        'manage_status' => Configuration::get('MP_STORE_LOCATION_ACTIVATION'),
                        'store_locations' => $sellerStores,
                        'storelists' => $sellerStores,
                    ));

                    Media::addJsDef(array(
                        'storeLocationsJson' => $storeLocationsJson,
                        'id_seller' => $idSeller,
                    ));
                } else {
                    $this->context->smarty->assign(
                        array(
                            'storelists' => $sellerStores,
                        )
                    );
                }
                $country = $this->context->country;
                $MP_GEOLOCATION_API_KEY = Configuration::get('MP_GEOLOCATION_API_KEY');

                $this->context->smarty->assign(array(
                    'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                    'modules_dir' => _MODULE_DIR_,
                    'country' => $country,
                    'manageStoreStatus' => Configuration::get('MP_STORE_LOCATION_ACTIVATION'),
                ));

                $this->defineJSVars();
                $this->setTemplate('module:mpstorelocator/views/templates/front/storelist.tpl');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'mporderdetails_link' => $this->context->link->getModuleLink('marketplace', 'mporderdetails'),
        );
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $jsVars['friendly_url'] = 1;
        } else {
            $jsVars['friendly_url'] = 0;
        }
        Media::addJsDef($jsVars);

        $jsVars = array(
                'space_error' => $this->module->l('Space is not allowed.', 'mpstorelocator'),
                'confirm_delete_msg' => $this->module->l('Are you sure you want to delete?', 'mpstorelocator'),
                'delete_msg' => $this->module->l('Deleted.', 'mpstorelocator'),
                'error_msg' => $this->module->l('An error occurred.', 'mpstorelocator'),
                'checkbox_select_warning' => $this->module->l('You must select at least one element to delete.', 'mpstorelocator'),
                'display_name' => $this->module->l('Display', 'mpstorelocator'),
                'records_name' => $this->module->l('records per page', 'mpstorelocator'),
                'no_product' => $this->module->l('No product found', 'mpstorelocator'),
                'show_page' => $this->module->l('Showing page', 'mpstorelocator'),
                'show_of' => $this->module->l('of', 'mpstorelocator'),
                'no_record' => $this->module->l('No records available', 'mpstorelocator'),
                'filter_from' => $this->module->l('filtered from', 'mpstorelocator'),
                't_record' => $this->module->l('total records', 'mpstorelocator'),
                'p_page' => $this->module->l('Previous', 'mpstorelocator'),
                'n_page' => $this->module->l('Next', 'mpstorelocator'),
                'search_item' => $this->module->l('Search', 'mpstorelocator'),
                'update_success' => $this->module->l('Updated Successfully', 'mpstorelocator'),
            );
        Media::addJsDef($jsVars);
    }

    public function changeProductStatus($idSeller)
    {
        $idStore = Tools::getValue('id_store');
        if ($idStore) {
            $objStore = new MarketplaceStoreLocator($idStore);
            if ($objStore->id_seller == $idSeller) {
                if (!count($this->errors)) {
                    if ($objStore->active) {
                        $objStore->active = 0;
                        $objStore->save();
                    } else {
                        $objStore->active = 1;
                        $objStore->save();
                    }
                    Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'storelist', array('status_updated' => 1)));
                }
            }
        }
    }

    public function deleteSelectedStores($mpIdStores)
    {
        $mpDelete = true;
        foreach ($mpIdStores as $$idStore) {
            $objStore = new MarketplaceStoreLocator($$idStore);
            if (!$objStore->delete()) {
                $mpDelete = false;
            }
        }

        if ($mpDelete) {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'storelist', array('deleted' => 1)));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'storelist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Store List', 'storelist'),
            'url' => ''
        );
        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        $language = $this->context->language;
        $country = $this->context->country;
        $MP_GEOLOCATION_API_KEY = Configuration::get('MP_GEOLOCATION_API_KEY');
        $this->registerJavascript(
            'google-map-lib',
            "https://maps.googleapis.com/maps/api/js?key=$MP_GEOLOCATION_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code",
            [
              'server' => 'remote'
            ]
        );
        // Register JS
        $this->registerJavascript('sellerstores', 'modules/'.$this->module->name.'/views/js/sellerstores.js');
        $this->registerJavascript('store-list-js', 'modules/'.$this->module->name.'/views/js/front/storelist.js');

        // Register CSS
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('store_details', 'modules/'.$this->module->name.'/views/css/store_details.css');

        $this->addJqueryPlugin('tablednd');
        $this->addjQueryPlugin('growl', null, false);

        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');
    }
}
