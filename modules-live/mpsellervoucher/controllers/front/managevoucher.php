<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpSellerVoucherManageVoucherModuleFrontController extends ModuleFrontController
{
	public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Manage Vouchers', array(), 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }
    
    public function initContent()
    {
        parent::initContent();
        $id_lang = $this->context->language->id;
        $link = new Link();

        if (isset($this->context->customer->id)) {
            $id_customer = $this->context->customer->id;
            $id_lang = $this->context->language->id;
            $obj_mp_sellerinfo = new WkMpSeller();
            $mp_seller_details = $obj_mp_sellerinfo->getSellerDetailByCustomerId($id_customer);
            if ($mp_seller_details && $mp_seller_details['active']) {
                $id_seller = $mp_seller_details['id_seller'];
                $seller_def_lang = $mp_seller_details['default_lang'];

                $obj_mp_cart_rule = new MpCartRule();
                $seller_voucher = $obj_mp_cart_rule->getSellerVoucherByIdSeller($id_seller, $id_lang);
                if ($seller_voucher) {
                    $this->context->smarty->assign('seller_voucher', $seller_voucher);
                }

                $this->context->smarty->assign(array(
                    'logged' => $this->context->customer->logged,
                    'logic' => 6,
                ));
                $this->defineJSVars();
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/managevoucher.tpl');
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('mpsellervoucher', 'managevoucher')));
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('changeSellerSideVoucherStatus')) {
            $this->changeVoucherStatus();
        }
        elseif (Tools::getValue('deleteVoucher')) {
            $this->deleteSingleVoucher();
        }
        elseif ($selected_products = Tools::getValue('mp_voucher_selected')) {      //delete selected checkbox process
            $obj_mp_cart_rule = new MpCartRule();
            $obj_mp_cart_rule->deleteVoucher($selected_products);
            Tools::redirect($this->context->link->getModuleLink('mpsellervoucher', 'managevoucher', array('deleted' => 1)));
        }
    }

    public function deleteSingleVoucher()
    {
        $id_mp_cart_rule = Tools::getValue('id_mp_cart_rule');
        $obj_mp_cart_rule = new MpCartRule();
        $obj_mp_cart_rule->deleteVoucher($id_mp_cart_rule);

        Tools::redirect($this->context->link->getModuleLink('mpsellervoucher', 'managevoucher', array('deleted' => 1)));
    }

    public function changeVoucherStatus()
    {
        $id_mp_cart_rule = Tools::getValue('id_mp_cart_rule');
        $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);
        if ($obj_mp_cart_rule->admin_approval) {
            if ($obj_mp_cart_rule->active == 1)
                $obj_mp_cart_rule->active = 0;
            else
                $obj_mp_cart_rule->active = 1;
            $obj_mp_cart_rule->save();

            if ($obj_mp_cart_rule->id_ps_cart_rule) {
                $id_ps_cart_rule = $obj_mp_cart_rule->id_ps_cart_rule;
                $cart_rule = new CartRule((int)$id_ps_cart_rule);
                if ($obj_mp_cart_rule->active)
                    $cart_rule->active = 1;
                else
                    $cart_rule->active = 0;

                $cart_rule->save();
            }
            Tools::redirect($this->context->link->getModuleLink('mpsellervoucher', 'managevoucher', array('status_updated' => 1)));
        }
        else {
            Tools::redirect($this->context->link->getModuleLink('mpsellervoucher', 'managevoucher', array('status_updated' => 2)));
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
                'confirm_delete_msg' => $this->trans('Êtes vous sûr ?', [], 'Modules.MpSellerVoucher'),
                'checkbox_select_warning' => $this->trans('Vous devez sélectionner au moins un élément à supprimer.', [], 'Modules.MpSellerVoucher'),
                'admin_approval_msg' => $this->trans("L'approbation de l'administrateur est nécessaire pour modifier le statut du bon.", [], 'Modules.MpSellerVoucher'),
                'display_name' => $this->trans('Affichez ', [], 'Modules.MpSellerVoucher'),
                'records_name' => $this->trans('résultats par page ', [], 'Modules.MpSellerVoucher'),
                'no_product' => $this->trans('Aucun bon trouvé', [], 'Modules.MpSellerVoucher'),
                'show_page' => $this->trans('Page', [], 'Modules.MpSellerVoucher'),
                'show_of' => $this->trans('de ', [], 'Modules.MpSellerVoucher'),
                'no_record' => $this->trans('Aucun bon disponible', [], 'Modules.MpSellerVoucher'),
                'filter_from' => $this->trans('filtre à partir de', [], 'Modules.MpSellerVoucher'),
                't_record' => $this->trans('Bon en total', [], 'Modules.MpSellerVoucher'),
                'search_item' => $this->trans('Rechercher :', [], 'Modules.MpSellerVoucher'),
                'p_page' => $this->trans('Précedent ', [], 'Modules.MpSellerVoucher'),
                'n_page' => $this->trans('Suivant ', [], 'Modules.MpSellerVoucher'),
            ];
        Media::addJsDef($jsVars);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('seller-voucher-front-css', 'modules/'.$this->module->name.'/views/css/sellerVoucherFront.css');
        $this->registerJavascript('seller-voucher-front-js', 'modules/'.$this->module->name.'/views/js/sellerVoucherFront.js');

        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('jquery-dataTables-min-js', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('dataTables-bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');
    }
}
