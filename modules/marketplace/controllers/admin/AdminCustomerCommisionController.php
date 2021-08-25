<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminCustomerCommisionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'wk_mp_commision';
        $this->className = 'WkMpCommission';
        $this->_select = 'CONCAT(c.`firstname`," ",c.`lastname`) as seller_customer_name';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`seller_customer_id`)';
        $this->bootstrap = true;
        $this->identifier = 'id_wk_mp_commision';

        parent::__construct();
        $this->toolbar_title = $this->l('Manage Commission');

        $taxDistributor = array(
            array('id' => 'admin'),
            array('id' => 'seller'),
            array('id' => 'distribute_both'),
        );
        $taxDistributor[0]['name'] = $this->l('Admin');
        $taxDistributor[1]['name'] = $this->l('Seller');
        $taxDistributor[2]['name'] = $this->l('Distribute between seller and admin');
        $shippingDistributor = $taxDistributor;
        unset($shippingDistributor[2]);

        $this->fields_options = array(
            'global' => array(
                'title' => $this->l('Global Commission'),
                'icon' => 'icon-globe',
                'fields' => array(
                    'WK_MP_GLOBAL_COMMISSION' => array(
                        'title' => $this->l('Commission Rate'),
                        'hint' => $this->l('The default commission rate apply on all sellers.'),
                        'validation' => 'isFloat',
                        'required' => true,
                        'type' => 'text',
                        'class' => 'fixed-width-xxl',
                        'suffix' => $this->l('%'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
            'tax_distribution' => array(
                'title' => $this->l('Tax Distribution'),
                'icon' => 'icon-globe',
                'fields' => array(
                    'WK_MP_PRODUCT_TAX_DISTRIBUTION' => array(
                        'title' => $this->l('Product Tax'),
                        'type' => 'select',
                        'list' => $taxDistributor,
                        'identifier' => 'id',
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
        );

        $this->fields_list = array(
            'id_wk_mp_commision' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
               'class' => 'fixed-width-xs',
            ),
            'seller_customer_name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'commision_rate' => array(
                'title' => $this->l('Commission Rate'),
                'align' => 'center',
                'suffix' => $this->l('%'),
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function initContent()
    {
        parent::initContent();
        $this->content .= $this->renderList();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->tpl_list_vars['title'] = $this->l('Seller Wise Commission');
        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add Admin Commission'),
        );
    }

    public function renderForm()
    {
        $objMpComm = new WkMpCommission();
        $remainSeller = array();
        if ($id = Tools::getValue('id_wk_mp_commision')) {
            $objMpCommission = new WkMpCommission($id);

            $sellerInfo = WkMpSeller::getSellerDetailByCustomerId($objMpCommission->seller_customer_id);

            $remainSeller[] = array(
                'seller_customer_id' => $objMpCommission->seller_customer_id,
                'business_email' => $sellerInfo['business_email'],
            );
        } else {
            $remainSeller = $objMpComm->getSellerWithoutCommission();
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Admin Commission'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'label' => $this->l('Select Seller'),
                    'name' => 'seller_customer_id',
                    'type' => 'select',
                    'required' => true,
                    'identifier' => 'id',
                    'options' => array(
                        'query' => $remainSeller,
                        'id' => 'seller_customer_id',
                        'name' => 'business_email',
                    ),
                ),
                array(
                    'label' => $this->l('Commission'),
                    'name' => 'add',
                    'type' => 'hidden',
                    'value' => '1',
                ),
                array(
                    'label' => $this->l('Admin Commission'),
                    'name' => 'commision_rate',
                    'type' => 'text',
                    'required' => true,
                    'default' => '10',
                    'col' => 2,
                    'suffix' => $this->l('%'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        if (!$remainSeller) { //if no seller fond or active and commission set for all
            $this->displayWarning($this->l('No active marketplace seller OR you have already set commission for all sellers.'));
        } else {
            return parent::renderForm();
        }
    }

    public function processSave()
    {
        $commission = trim(Tools::getValue('commision_rate'));

        if ($commission == '') {
            $this->errors[] = $this->l('field Commission Rate is required.');
        } elseif (!Validate::isUnsignedFloat($commission)) {
            $this->errors[] = $this->l('field Commission Rate is invalid.');
        } elseif ($commission > 100) {
            $this->errors[] = $this->l('field Commission Rate must be a valid percentage (0 to 100).');
        }
        if (empty($this->errors)) {
            parent::processSave();
        } else {
            $this->display = 'add';
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionswk_mp_commision')) {
            $globalCommission = trim(Tools::getValue('WK_MP_GLOBAL_COMMISSION'));
            if ($globalCommission > 100 || $globalCommission < 0) {
                $this->errors[] = $this->l('field Commission Rate must be a valid percentage (0 to 100).');
            }
        }

        parent::postProcess();
    }
}
