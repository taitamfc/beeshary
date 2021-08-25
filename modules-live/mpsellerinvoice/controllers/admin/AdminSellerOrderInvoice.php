<?php
/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/

class AdminSellerOrderInvoiceController extends ModuleAdminController
{
    public function __construct()
    {
        $this->identifier = 'seller_customer_id';
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->table = 'wk_mp_seller_order_detail';
        $this->_select = '
            s.`id_seller` as id_seller,
            s.`business_email` as seller_email,
            s.`shop_name_unique` as shop_name_unique,
            COUNT(a.`id_order`) as total_order_count,
            CONCAT(s.`seller_firstname`," ",s.`seller_lastname`) as seller_name';
        $this->_join .= 'JOIN `'._DB_PREFIX_.'wk_mp_seller` s on (s.`seller_customer_id` = a.`seller_customer_id`)';

        $this->_group = 'GROUP BY a.`seller_customer_id`';
        $this->fields_list = array(
            'id_seller' => array(
                'title' => $this->l('Seller ID'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'seller_name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'shop_name_unique' => array(
                'title' => $this->l('Unique Shop Name'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'seller_email' => array(
                'title' => $this->l('Seller Email'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'total_order_count' => array(
                'title' => $this->l('No. Of Orders'),
                'align' => 'center',
                'callback' => 'checkSellerTotalOrder',
                'search' => false,
                'havingFilter' => true
            ),
        );
        if (!Tools::getValue('seller_customer_id')) {
            $this->addRowAction('view');
        }

    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initProcess()
    {
        parent::initProcess();
        if ($action = Tools::getValue('submitAction')) {
            $this->action = $action;
        }
        // $this->display;die;
        $prefix = $this->getCookieFilterPrefix();

        if ($this->context->cookie->{$prefix . $this->list_id . 'Orderby'} == 'order_reference'
            || $this->context->cookie->{$prefix . $this->list_id . 'Orderby'} == 'total'
            || $this->context->cookie->{$prefix . $this->list_id . 'Orderby'} == 'customer'
            || $this->context->cookie->{$prefix . $this->list_id . 'Orderby'} == 'order_status'
        ) {
            $this->context->cookie->{$prefix . $this->list_id . 'Orderby'} = '';
        }

    }

    public function processDownloadSellerInvoice()
    {
        $idOrder = Tools::getValue('id_order');
        $idCustomer = Tools::getValue('id_seller_customer');

        if ($idOrder &&  $idCustomer) {
            $order = new Order($idOrder);
            $create_invoice = new CreateInvoice();
            $create_invoice->createInvoice($order, $idCustomer, true);
            die;
        }
    }

    public function checkSellerTotalOrder($val, $arr)
    {
        if ($val) {
            return WkMpSellerOrder::countTotalOrder(false, $arr['seller_customer_id'], false);
        }
        return '--';
    }

    public static function setOrderCurrency($val, $arr)
    {
        if ($val) {
            return Tools::displayPrice($val, (int) $arr['id_currency']);
        }
    }

    public function displayDownloadSellerInvoiceLink($token = null, $id = null)
    {
        if ($id && ($idSellerCustomer = Tools::getValue('seller_customer_id'))) {
            $objSellerInvoice = new MpSellerOrderInvoiceRecord();
            $mpSeller = new WkMpSeller();
            $seller = $mpSeller->getSellerByCustomerId($idSellerCustomer, $this->context->language->id);
            if ($seller) {
                $isInvoiceSent = $objSellerInvoice->getOrderInvoiceNumber($id, $seller['id_seller']);
                if ($isInvoiceSent) {

                    $this->context->smarty->assign(
                        array(
                            'wk_link' => $this->context->link->getAdminLink('AdminSellerOrderInvoice').'&submitAction=downloadSellerInvoice&id_order='.(int) $id.'&id_seller_customer='.(int)$idSellerCustomer
                        )
                    );
                    return $this->context->smarty->fetch(
                        _PS_MODULE_DIR_.'mpsellerinvoice/views/templates/admin/seller_order_invoice.tpl'
                    );
                }
            }
        }
    }

    public function renderView()
    {
        $idSellerCustomer = (int) Tools::getValue('seller_customer_id');
        unset($this->_group);
        $this->identifier = 'id_order';
        $this->_select = '
            CONCAT(c.`firstname`, \' \', c.`lastname`) `customer`,
            o.`total_paid` as total,
            soi.`id_order_invoice` as id_order_invoice,
            s.`id_seller` as id_seller,
            s.`business_email` as seller_email,
            CONCAT(s.`seller_firstname`," ",s.`seller_lastname`) as seller_name,
            osl.`name` as order_status,
            o.`reference` as order_reference';

        $sellerInfo = WkMpSeller::getSellerByCustomerId($idSellerCustomer, $this->context->language->id);
        $this->toolbar_title = $sellerInfo['shop_name_unique'];
        $this->_join .= 'JOIN `'._DB_PREFIX_.'orders` o on (a.`id_order` = o.`id_order`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'mp_seller_order_invoice` soi on (soi.`id_order` = o.`id_order`)';
        $this->_join .= 'JOIN `'._DB_PREFIX_.'customer` c on (o.`id_customer` = c.`id_customer`)';
        $this->_join .= 'JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = o.`current_state`)';
        $this->_join .= 'JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_group = 'GROUP BY a.`id_order`';
        $this->_where = ' AND a.`seller_customer_id` = '.(int) $idSellerCustomer;
        $prefix = $this->getCookieFilterPrefix();
        if ($this->context->cookie->{$prefix . $this->list_id . 'Orderby'} == 'total_order_count') {
            $this->context->cookie->{$prefix . $this->list_id . 'Orderby'} = '';
        }

        $this->renderSellerList();
        self::$currentIndex = self::$currentIndex.'&token='.$this->token.'&view'.$this->table.'&seller_customer_id='.(int)$idSellerCustomer;
        $this->context->smarty->assign(array(
            'current' => self::$currentIndex,
        ));
        $this->addRowAction('downloadSellerInvoice');
        return parent::renderList();
    }

    public function renderSellerList()
    {
        $this->fields_list = array(
            'id_order_invoice' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
                'havingFilter' => true,
                'filter_key' => 'id_order_invoice'
            ),
            'order_reference' => array(
                'title' => $this->l('Order Reference'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'customer' => array(
                'title' => $this->l('Customer Name'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'total' => array(
                'title' => $this->l('Total'),
                'align' => 'center',
                'callback' => 'setOrderCurrency',
                'havingFilter' => true
            ),
            'order_status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'havingFilter' => true
            ),
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitFilter'.$this->table)) {
            $idSellerCustomer = (int) Tools::getValue('seller_customer_id');
            if ($idSellerCustomer) {
                self::$currentIndex = self::$currentIndex.'&token='.$this->token.'&view'.$this->table.'&seller_customer_id='.(int)$idSellerCustomer;
                $this->context->smarty->assign(array(
                    'current' => self::$currentIndex,
                ));
            }
        }

        if (Tools::isSubmit('submitReset'.$this->table)) {
            $this->processResetFilters();
        }

        parent::postProcess();
    }

    protected function filterToField($key, $filter)
    {
        $idSellerCustomer = (int) Tools::getValue('seller_customer_id');
        if ($idSellerCustomer) {
            $this->renderSellerList();
        }

        return parent::filterToField($key, $filter);
    }
}
