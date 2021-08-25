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

class mpmassuploadmassuploadviewModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', [], 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Mass Upload', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        if ($id_customer = $this->context->customer->id) {
            $id_customer = $this->context->customer->id;
            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
            $smaryVar = array();
            if ($mp_seller && $mp_seller['active']) {
                $obj_massupload = new MarketplaceMassUpload();
                $request_list = $obj_massupload->getMassUploadByIdSeller($mp_seller['id_seller']);
                if ($request_list) {
                    $smaryVar['request_list'] = $request_list;
                }

                $success = Tools::getValue('success');
                if ($success) {
                    $smaryVar['success'] = $success;
                } else {
                    $smaryVar['success'] = 0;
                }

                if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                    $smaryVar['massupload_combination_approve'] = Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE');
                }

                if (isset($this->context->cookie->wk_massupload_warning)) {
                    $csv_warning = $this->context->cookie->wk_massupload_warning;
                    $warning_arr = explode('===', $csv_warning);
                    $smaryVar['warning_arr'] = $warning_arr;

                    unset($this->context->cookie->wk_massupload_warning);
                    $this->context->cookie->write();
                }

                $obj_marketplace_seller = new WkMpSeller();
                $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
                $sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller']);

                $smaryVar['sellerHasProducts'] = $sellerProducts;
                $smaryVar['link_new_request'] = $link->getModuleLink('mpmassupload', 'addnewuploadrequest');
                $smaryVar['link_update_request'] = $link->getModuleLink('mpmassupload', 'exportdetails');
                $smaryVar['uploaded_csv_link'] = _MODULE_DIR_ .'mpmassupload/views/uploaded_csv';
                $smaryVar['is_seller'] = 1;
                $smaryVar['logic'] = 'massupload';
                $this->context->smarty->assign($smaryVar);

                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/massuploadview.tpl');
            } else {
                Tools::redirect(__PS_BASE_URI__.'pagenotfound');
            }
        } else {
            Tools::redirect($link->getPageLink('my-account'));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        // Register CSS
        $this->registerStylesheet('mass-upload-menu', 'modules/'.$this->module->name.'/views/css/massuploadmenu.css');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
