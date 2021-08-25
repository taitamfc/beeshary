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

class AdminMangopayBankDetailsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->fields_list = array(
            'UserId' => array(
                'title' => $this->l('Mangopay User Id'),
                'align' => 'center',
                'search' => false,
            ),
            'Type' => array(
                'title' => $this->l('Account Type'),
                'align' => 'center',
                'search' => false,
            ),
            'Id' => array(
                'title' => $this->l('Mangopay Account Id'),
                'align' => 'center',
                'search' => false,
            ),
            'OwnerName' => array(
                'title' => $this->l('Owner Name'),
                'align' => 'center',
                'search' => false,
            ),
            'OwnerAddress' => array(
                'title' => $this->l('Owner Address'),
                'align' => 'center',
                'search' => false,
                'callback' => 'getOwnerAddressLine',
            ),
            'Active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'callback' => 'getAccountStatus',
                'search' => false,
            ),
        );
        $this->list_no_link = true;
    }

    /**
     * Call back function used to create deactivate bank account.
     *
     * @param [type] $echo
     * @param [type] $row
     * @return void
     */
    public function getAccountStatus($echo, $row)
    {
        if ($echo) {
            $return = '<a class="btn btn-primary deactivate_bank_account" href="'.self::$currentIndex.'&token='.$this->token.'&bank_acc_id='.$row['Id'].'&account_user_id='.$row['UserId'].'">'.
            $this->l('Deactivate').'</a>';
        } else {
            $return = $this->l('Deactivated');
        }
        return $return;
    }

    /**
     * Call back function display --- if no address.
     *
     * @param [type] $echo
     * @return void
     */
    public function getOwnerAddressLine($echo)
    {
        if ($echo) {
            $return = $echo->AddressLine1;
        } else {
            $return = '----';
        }

        return $return;
    }

    /**
     * Add the button on the top of the page.
     *
     * @return void
     */
    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Account'),
        );
    }

    /**
     * Generate the render form.
     *
     * @return void
     */
    public function renderForm()
    {
        $mgp_client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        if ($mgp_client_id) {
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
     * Deactivate the bank account.
     *
     * @return void
     */
    public function postProcess()
    {
        Media::addJsDef(array(
            'confirm_account_deactivate_msg' => $this->l('Are you sure? once deactivated, a bank account can not be
            reactivated afterwards.'),
        ));
        if (Tools::getValue('account_user_id') && Tools::getValue('bank_acc_id')) {
            $obj_mgp_mpservice = new MangopayMpService();
            $mgp_bank_account_deactivate = (array) $obj_mgp_mpservice->deactivateBankAccount(Tools::getValue('account_user_id'), Tools::getValue('bank_acc_id'));
            if (isset($mgp_bank_account_deactivate['Id']) && $mgp_bank_account_deactivate['Id']) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            } else {
                $this->errors[] = $mgp_bank_account_deactivate['ResultMessage'];
            }
        }
        parent::postProcess();
    }

    /**
     * Register the bank account details.
     * @return void
     */
    public function processSave()
    {
        $admin_mgp_usr_id = Configuration::get('WK_MP_MANGOPAY_USERID');
        $admin_mgp_wallet_id = Configuration::get('WK_MP_MANGOPAY_WALLETID');
        if ($admin_mgp_wallet_id && $admin_mgp_usr_id) {
            $obj_mgp_mpservice = new MangopayMpService();
            $this->validateSellerBankDetailsFieldsAdmin();
            if (!count($this->errors)) {
                $mgp_registered_bank_acc_result = (array) $obj_mgp_mpservice->registerMangopayBankAccount($admin_mgp_usr_id, $_POST);
                if (isset($mgp_registered_bank_acc_result['Id']) && $mgp_registered_bank_acc_result['Id']) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                } else {
                    if (isset($mgp_registered_bank_acc_result['ResultMessage']) && $mgp_registered_bank_acc_result['ResultMessage']) {
                        $this->errors[] = $mgp_registered_bank_acc_result['ResultMessage'];
                    } else {
                        $this->errors[] = $this->l('Some error occured while registering mangopay bank acccount.
                        Please try again.');
                    }
                }
            } else {
                $this->display = 'add';
            }
        } else {
            $this->errors[] = $this->l('To register bank detials first save  your mangopay configuration form
            module configuration.');
        }
    }

    /**
     * Validate the bank details.
     *
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
            $this->errors[] = $this->l('Please enter account owner address.');
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
     * add js file.
     *
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_.'mpmangopaypayment/views/js/mangopay_bank_details.js');
    }
}
