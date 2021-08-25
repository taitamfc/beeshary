<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpWebserviceWebserviceModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;
    }

    public function initContent()
    {
        parent::initContent();
        if (!Configuration::get('WK_WS_SELLER_WEBSERVICE')) {
            Tools::redirect(__PS_BASE_URI__.'pagenotfound');
        }
        if (isset($this->context->customer->id)) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && $mpSeller['active']) {
                $this->saveMpWsKey();

                $action = Tools::getValue('action');
                if ($action == 'add') {
                    $this->context->smarty->assign(array(
                        'mpresources' => WebserviceSpecificManagementSeller::$allowedMethods,
                    ));
                } elseif ($action == 'status') {
                    if (Configuration::get('WK_WS_KEY_SELLER_STATUS')) {
                        $idMpWebservice = Tools::getValue('id_mpwebservice');
                        $mpWsKey = new WkMpWebserviceKey($idMpWebservice);
                        if ($mpWsKey->toggleStatus()) {
                            Tools::redirect($this->context->link->getModuleLink(
                                'mpwebservice',
                                'webservice',
                                array('status' => 1)
                            ));
                        }
                    }
                } elseif ($action == 'edit') {
                    $idMpWebservice = Tools::getValue('id_mpwebservice');
                    $keyInfo = WkMpWebserviceKey::getMpWebserviceKey($mpSeller['id_seller'], $idMpWebservice);
                    if ($keyInfo) {
                        $keyInfo['mpresource'] = Tools::jsonDecode($keyInfo['mpresource']);
                        $this->context->smarty->assign(array(
                            'mpresources' => WebserviceSpecificManagementSeller::$allowedMethods,
                            'selected_mpresources' => $keyInfo,
                        ));
                    }
                } elseif ($action == 'delete') {
                    $idMpWebservice = Tools::getValue('id_mpwebservice');
                    $mpWsKey = new WkMpWebserviceKey($idMpWebservice);
                    if ($mpWsKey->delete()) {
                        Tools::redirect($this->context->link->getModuleLink(
                            'mpwebservice',
                            'webservice',
                            array('delete' => 1)
                        ));
                    }
                }

                $mpWebServiceKey = WkMpWebserviceKey::getMpWebserviceKey($mpSeller['id_seller']);
                $this->context->smarty->assign(array(
                    'logic' => 'mpwebservice_link',
                    'addwebservice_link' => $this->context->link->getModuleLink(
                        'mpwebservice',
                        'webservice',
                        array('action' => 'add')
                    ),
                    'mpwebservicekeys' => $mpWebServiceKey,
                    'WK_WS_KEY_SELLER_STATUS' => Configuration::get('WK_WS_KEY_SELLER_STATUS'),
                    'WK_WS_KEY_ADMIN_APPROVE' => Configuration::get('WK_WS_KEY_ADMIN_APPROVE'),
                ));
                $this->setTemplate('module:mpwebservice/views/templates/front/webservice.tpl');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function saveMpWsKey()
    {
        if (Tools::isSubmit('submitAPIKey')) {
            $idMpWebservice = Tools::getValue('id_mpwebservice');
            $key = Tools::getValue('key');
            $keyDescription = Tools::getValue('key_description');
            $status = Tools::getValue('status');
            $mpAPI = Tools::jsonEncode(Tools::getValue('mpapi'));

            if ($idMpWebservice) {
                $mpWsKey = new WkMpWebserviceKey($idMpWebservice);
            } else {
                $mpWsKey = new WkMpWebserviceKey();
            }

            if (empty($key)) {
                $this->errors[] = $this->module->l('Key is required field.', 'webservice');
            }

            if (empty($this->errors)) {
                $seller = WkMpSeller::getSellerByCustomerId($this->context->customer->id);
                $mpWsKey->key = $key;
                $mpWsKey->description = $keyDescription;
                $mpWsKey->mpresource = $mpAPI;
                $mpWsKey->id_seller = $seller['id_seller'];
                // Status is 0 by default, if admin need to approve and if seller can not change status
                if (!Configuration::get('WK_WS_KEY_SELLER_STATUS')) {
                    if (Configuration::get('WK_WS_KEY_ADMIN_APPROVE')) {
                        $status = 0;
                    }
                }
                $mpWsKey->active = $status;
                if ($mpWsKey->save()) {
                    Tools::redirect($this->context->link->getModuleLink(
                        'mpwebservice',
                        'webservice',
                        array('save' => 1)
                    ));
                }
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mpwebservice'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Webservice', 'mpwebservice'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
        $this->addJS(_MODULE_DIR_.'mpwebservice/views/js/mpwebservice.js');
    }
}
