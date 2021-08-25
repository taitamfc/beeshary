<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpSellerVacationSellerVacationDetailModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $customer_id = $this->context->customer->id;
            $id_lang = $this->context->language->id;

            $obj_marketplace_seller = new WkMpSeller();
            $seller_info = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
            $seller_id = $seller_info['id_seller'];
            if ($seller_id) {
                $obj_seller_vacation_detail = new SellerVacationDetail();

                //Delete Seller vacation
                $del_id = Tools::getValue('del_id');
                if ($del_id) {
                    $obj_seller_vacation_detail->deleteMpSellerVacationDetail($del_id);
                    $redirect_link = $this->context->link->getModuleLink('mpsellervacation', 'sellerVacationDetail', array('deleted' => 1));
                    Tools::redirect($redirect_link);
                }

                $vacation_info = $obj_seller_vacation_detail->getMarketPlaceSellerVacationDetails($seller_id, $id_lang);
$objMpSeller = new WkMpSeller();
						//setMedia();
						if ($this->context->customer->isLogged()) {
						$smartyVar = array();
						$seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);

						}
						parent::initContent();
						$this->context->smarty->assign(array(
						'mp_seller_info' => $seller));
                $this->context->smarty->assign(array(
                                'logic' => 'vac_details',
                                'mps_vacation_detail' => $vacation_info,
                                'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
                                'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR'),
                                'confirm_msg' => $this->trans('This vacation detail will be deleted permanently. Are you sure to delete this vacation?'),
                                ));
                $this->setTemplate('module:mpsellervacation/views/templates/front/seller_vacation_detail.tpl');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(array('confirm_msg' => $this->trans('This vacation detail will be deleted permanently. Are you sure to delete this vacation?')));
        $this->registerstylesheet('mpseller_vacation_css', 'modules/marketplace/views/css/marketplace_account.css');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Seller Vacation Details', array(), 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }
}
