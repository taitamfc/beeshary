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

class AdminMangopaySellerBankDetailsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller';
        $this->identifier = 'id_seller';
        parent::__construct();
        $this->_select = 'CONCAT(a.`seller_firstname`, " ", a.`seller_lastname`) as seller_name';
        $this->fields_list = array(
            'id_seller' => array(
                'title' => $this->l('Seller Id'),
                'align' => 'center',
                'search' => false,
            ),
            'seller_name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center',
                'search' => false,
            ),
            'business_email' => array(
                'title' => $this->l('Seller Email'),
                'align' => 'center',
                'search' => false,
            ),
            'shop_name_unique' => array(
                'title' => $this->l('Shop Name'),
                'align' => 'center',
                'search' => false,
            ),
            'num_accounts' => array(
                'title' => $this->l('Mangopay Accounts'),
                'align' => 'center',
                'search' => false,
            ),
            'mgp_user' => array(
                'title' => $this->l('Mangopay User'),
                'align' => 'center',
                'search' => false,
            ),
        );
    }

    /**
     * Generate render list with row action view.
     * @return void
     */
    public function renderList()
    {
        $this->addRowAction('view');
        return parent::renderList();
    }

    /**
     * Add the toolbar button on the top of the page
     * @return void
     */
    public function initToolbar()
    {
        parent::initToolbar();
        $href_seller_payout = $this->context->link->getAdminLink('AdminMangopaySellerPayOut');
        $this->page_header_toolbar_btn['cogs'] = array(
            'href' => $href_seller_payout,
            'desc' => $this->l('Mangopay Seller payout'),
        );
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Account'),
        );
    }

    /**
     * Generate the render form by tpl.
     * @return void
     */
    public function renderForm()
    {
        $mgp_client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        if ($mgp_client_id) {
            $sellers_info = WkMpSeller::getAllSeller();
            if ($sellers_info) {
                $this->context->smarty->assign('sellers_info', $sellers_info);
            }
            $countries = Country::getCountries($this->context->language->id, false, false, false);
            if ($countries) {
                $this->context->smarty->assign('countries', $countries);
            }
        }
        $this->context->smarty->assign('mgp_client_id', $mgp_client_id);
        $this->fields_form = array(
            'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'button',
            ),
        );

        return parent::renderForm();
    }

    /**
     * Save the seller bank details
     * @return void
     */
    public function processSave()
    {
        if (Tools::isSubmit('saveSellerbankDetails')) {
            $id_seller = Tools::getValue('seller_id');
            if ($id_seller) {
                $seller_mgp_dtls = MangopayMpSeller::sellerMangopayDetails($id_seller);
                if ($seller_mgp_dtls) {
                    $seller_mgp_usr_id = $seller_mgp_dtls['mgp_userid'];
                    if ($seller_mgp_usr_id) {
                        $obj_mgp_mpservice = new MangopayMpService();
                        $this->validateSellerBankDetailsFieldsAdmin();
                        if (!count($this->errors)) {
                            $mgp_registered_bank_acc_result = (array) $obj_mgp_mpservice->registerMangopayBankAccount(
                                $seller_mgp_usr_id,
                                $_POST
                            );
                            if (isset($mgp_registered_bank_acc_result['Id']) && $mgp_registered_bank_acc_result['Id']) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                            } else {
                                if (isset($mgp_registered_bank_acc_result['ResultMessage']) && $mgp_registered_bank_acc_result['ResultMessage']) {
                                    $this->errors[] = $mgp_registered_bank_acc_result['ResultMessage'];
                                } else {
                                    $this->errors[] = $this->l('Some error occured while registering mangopay bank
                                    acccount. Please try again.');
                                }
                            }
                        } else {
                            $this->display = 'add';
                        }
                    } else {
                        $this->errors[] = $this->l('Seller mangopay user id is missing. To register bank details seller
                        has to save his country first to create mangopay user id.');
                    }
                } else {
                    $this->errors[] = $this->l('Seller mangopay details not found.');
                }
            } else {
                $this->errors[] = $this->l('Seller not found.');
            }
        }
    }

    /**
     * Generate the render view.
     * @return void
     */
    public function renderView()
    {
        parent::renderView();
        $id_seller = Tools::getValue('id_seller');
        $link = $this->context->link->getAdminLink('AdminSellerInfoDetail');
        Tools::redirectAdmin($link.'&id_seller='.$id_seller.'&viewwk_mp_seller');
    }

    /**
     * Validate the seller bank details.
     * @return void
     */
    private function validateSellerBankDetailsFieldsAdmin()
    {
        if (!Tools::getValue('mgp_bank_type')) {
            $this->errors[] = $this->l('Please select a valid bank type.');
        }
        if (!Tools::getValue('mgp_owner_name')) {
            $this->errors[] = $this->l('Please enter account owner name.');
        }
        if (!Tools::getValue('mgp_owner_addressline1')) {
            $this->errors[] = $this->l('Please enter account owner required address line.');
        }
        if (!Tools::getValue('mgp_owner_city')) {
            $this->errors[] = $this->l('Please enter account owner city.');
        }
        if (!Tools::getValue('mgp_owner_postcode')) {
            $this->errors[] = $this->l('Please enter account owner postal code.');
        }
        if (!Tools::getValue('mgp_owner_region')) {
            $this->errors[] = $this->l('Please enter account owner region.');
        }
        if (!Tools::getValue('mgp_owner_country')) {
            $this->errors[] = $this->l('Please enter account owner country.');
        }
        if (Tools::getValue('mgp_bank_type') == 'IBAN') {
            if (!Tools::getValue('mgp_iban')) {
                $this->errors[] = $this->l('IBAN is required field for type IBAN/BIC.');
            }
            if (!Tools::getValue('mgp_bic')) {
                $this->errors[] = $this->l('BIC is required field for type IBAN/BIC.');
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'GB') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->l('Account Number is required field for type GB.');
            }
            if (!Tools::getValue('mgp_sort_code')) {
                $this->errors[] = $this->l('Sort Code is required field for type GB.');
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'US') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->l('Account Number is required field for type US.');
            }
            if (!Tools::getValue('mgp_aba')) {
                $this->errors[] = $this->l('aba is required field for type US.');
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'CA') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->l('Account Number is required field for type CA.');
            }
            if (!Tools::getValue('mgp_bank_name')) {
                $this->errors[] = $this->l('Bank Name is required field for type CA.');
            }
            if (!Tools::getValue('mgp_institution_number')) {
                $this->errors[] = $this->l('Institution Number is required field for type CA.');
            }
            if (!Tools::getValue('mgp_branch_code')) {
                $this->errors[] = $this->l('Branch Code is required field for type CA.');
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'OTHER') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->l('Account Number is required field for type OTHER.');
            }
            if (!Tools::getValue('mgp_bic')) {
                $this->errors[] = $this->l('BIC is required field for type OTHER.');
            }
        }
    }

    /**
     * Add Js file
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_.'mpmangopaypayment/views/js/mangopay_bank_details.js');
    }
}
