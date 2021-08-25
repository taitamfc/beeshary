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

class MpMangopayPaymentMangopaySellerBankDetailsModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Payment ', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Bank Details', array(), 'Breadcrumb'),
            'url' => ''
        );
        return $breadcrumb;
    }

    // Get the bank account details and assign to tpl.
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $smartyVars = array();
            $idCustomer = $this->context->customer->id;
            $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($sellerDetail && $sellerDetail['active']) {
                if (Configuration::get('WK_MP_MANGOPAY_SELLER_BANK_DTL')) {
                    if (Configuration::get('WK_MP_MANGOPAY_CLIENTID')) {
                        $idSeller = $sellerDetail['id_seller'];
                        if ($countries = Country::getCountries($this->context->language->id, false, false, false)) {
                            $smartyVars['countries'] = $countries;
                        }
                        $objMgpSeller = new MangopayMpSeller();
                        $objMgpService = new MangopayMpService();
                        $sellerMgpDetail = $objMgpSeller->sellerMangopayDetails($idSeller);
                        if ($sellerMgpDetail && $sellerMgpDetail['mgp_userid']) {
                            if ($mgpRegBankAccIds = $objMgpService->getMangopayBankAccounts(
                                $sellerMgpDetail['mgp_userid']
                            )) {
                                $smartyVars['mgp_registered_bank_acc_ids'] = $mgpRegBankAccIds;
                            }
                            $smartyVars['seller_not_registered'] = 1;
                        }
                        $smartyVars['is_seller'] = 1;
                        $smartyVars['id_module'] = $this->module->id;
                        $smartyVars['logic'] = 'mgp_seller_bank_details';
                        $smartyVars['seller_bank_details_enable'] = Configuration::get('WK_MP_MANGOPAY_SELLER_BANK_DTL');

                        $this->defineJSVars();
                        $this->context->smarty->assign($smartyVars);
                        $this->setTemplate(
                            'module:mpmangopaypayment/views/templates/front/mangopaysellerbankdetails.tpl'
                        );
                    } else {
                        Tools::redirect('index.php?controller=my-account&back='.urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails')));
                    }
                } else {
                    Tools::redirect(
                        'index.php?controller=my-account&back='.
                        urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails'))
                    );
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails'))
            );
        }
    }

    public function defineJSVars()
    {
        $jsVars = array(
                'confirm_account_deactivate_msg' => $this->module->l(
                    'Are you sure to deactivate this bank account ?',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_name_err' => $this->module->l(
                    'Please enter bank account owner name.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addr_err' => $this->module->l(
                    'Please enter bank account owner address.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addrline1_err' => $this->module->l(
                    'Bank account owner\'s address line1 is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addr_city_err' => $this->module->l(
                    'Bank account owner\'s city is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addr_zipcode_err' => $this->module->l(
                    'Bank account owner\'s postal code is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addr_region_err' => $this->module->l(
                    'Bank account owner\'s region is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_owner_addr_country_err' => $this->module->l(
                    'Bank account owner\'s country is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_account_number_err' => $this->module->l(
                    'Bank account owner\'s Account Number is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_institution_number_err' => $this->module->l(
                    'Bank account owner\'s institution number is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_branch_code_err' => $this->module->l(
                    'Bank account owner\'s Branch Code is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_iban_err' => $this->module->l(
                    'Bank account owner\'s IBAN is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_bic_err' => $this->module->l(
                    'Bank account owner\'s BIC is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_sort_code_err' => $this->module->l(
                    'Bank account owner\'s Sort Code is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_aba_err' => $this->module->l(
                    'Bank account owner\'s ABA is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_bank_name_err' => $this->module->l(
                    'Bank account owner\'s Bank Name is required field.',
                    'mangopaysellerbankdetails'
                ),
                'mgp_country_err' => $this->module->l(
                    'Bank account owner\'s Country is required field.',
                    'mangopaysellerbankdetails'
                ),
            );

        Media::addJsDef($jsVars);
    }

    //Register mangopay bank account details
    public function postProcess()
    {
        $idCustomer = $this->context->customer->id;
        if (Tools::isSubmit('mgp_bank_account_deactivate')) {
            $objMgpService = new MangopayMpService();
            $mgpBankAccDeactivate = (array) $objMgpService->deactivateBankAccount(
                Tools::getValue('mgp_bank_author_id'),
                Tools::getValue('mgp_bank_account_id')
            );
            if (isset($mgpBankAccDeactivate['Id']) && $mgpBankAccDeactivate['Id']) {
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'mpmangopaypayment',
                        'mangopaysellerbankdetails',
                        array('mgp_account_deactivate_success' => 1)
                    )
                );
            } else {
                $this->errors[] = $mgpBankAccDeactivate['ResultMessage'];
            }
        }
        if (isset($idCustomer)) {
            $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($sellerDetail && $sellerDetail['active']) {
                if (Tools::isSubmit('submit_mgp_bank_account')) {
                    $idSeller = $sellerDetail['id_seller'];
                    $objMgpSeller = new MangopayMpSeller();
                    $sellerMgpDetail = $objMgpSeller->sellerMangopayDetails($idSeller);
                    if ($sellerMgpDetail && $sellerMgpDetail['mgp_userid']) {
                        $sellerMgpUserId = $sellerMgpDetail['mgp_userid'];
                        $objMgpService = new MangopayMpService();
                        //validate fields first.
                        $this->validateSellerBankDetailsFields();
                        if (!count($this->errors)) {
                            $mgpRegBankAccResult = (array) $objMgpService->registerMangopayBankAccount(
                                $sellerMgpUserId,
                                $_POST
                            );
                            if (isset($mgpRegBankAccResult['Id']) && $mgpRegBankAccResult['Id']) {
                                Tools::redirect(
                                    $this->context->link->getModuleLink(
                                        'mpmangopaypayment',
                                        'mangopaysellerbankdetails',
                                        array('mgp_account_success' => 1)
                                    )
                                );
                            } else {
                                $this->errors[] = $mgpRegBankAccResult['ResultMessage'];
                            }
                        }
                    } else {
                        $this->errors[] = $this->module->l(
                            'Mangopay user id is missing. To register bank details first save your country to create
                            mangopay user id.',
                            'mangopaysellerbankdetails'
                        );
                    }
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails'))
            );
        }
    }

    // Validate the bank account details.
    private function validateSellerBankDetailsFields()
    {
        if (!Tools::getValue('mgp_bank_type')) {
            $this->errors[] = $this->module->l('Please select a valid bank type.', 'mangopaysellerbankdetails');
        }
        if (!Tools::getValue('mgp_owner_name')) {
            $this->errors[] = $this->module->l('Please enter account owner name.', 'mangopaysellerbankdetails');
        }
        if (!Tools::getValue('mgp_owner_addressline1')) {
            $this->errors[] = $this->module->l(
                'Please enter account owner required address line.',
                'mangopaysellerbankdetails'
            );
        }
        if (!Tools::getValue('mgp_owner_city')) {
            $this->errors[] = $this->module->l('Please enter account owner city.', 'mangopaysellerbankdetails');
        }
        if (!Tools::getValue('mgp_owner_postcode')) {
            $this->errors[] = $this->module->l('Please enter account owner postal code.', 'mangopaysellerbankdetails');
        }
        if (!Tools::getValue('mgp_owner_region')) {
            $this->errors[] = $this->module->l('Please enter account owner region.', 'mangopaysellerbankdetails');
        }
        if (!Tools::getValue('mgp_owner_country')) {
            $this->errors[] = $this->module->l('Please enter account owner country.', 'mangopaysellerbankdetails');
        }
        if (Tools::getValue('mgp_bank_type') == 'IBAN') {
            if (!Tools::getValue('mgp_iban')) {
                $this->errors[] = $this->module->l(
                    'IBAN is required field for type IBAN/BIC.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_bic')) {
                $this->errors[] = $this->module->l(
                    'BIC is required field for type IBAN/BIC.',
                    'mangopaysellerbankdetails'
                );
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'GB') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->module->l(
                    'Account Number is required field for type GB.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_sort_code')) {
                $this->errors[] = $this->module->l(
                    'Sort Code is required field for type GB.',
                    'mangopaysellerbankdetails'
                );
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'US') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->module->l(
                    'Account Number is required field for type US.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_aba')) {
                $this->errors[] = $this->module->l(
                    'aba is required field for type US.',
                    'mangopaysellerbankdetails'
                );
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'CA') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->module->l(
                    'Account Number is required field for type CA.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_bank_name')) {
                $this->errors[] = $this->module->l(
                    'Bank Name is required field for type CA.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_institution_number')) {
                $this->errors[] = $this->module->l(
                    'Institution Number is required field for type CA.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_branch_code')) {
                $this->errors[] = $this->module->l(
                    'Branch Code is required field for type CA.',
                    'mangopaysellerbankdetails'
                );
            }
        } elseif (Tools::getValue('mgp_bank_type') == 'OTHER') {
            if (!Tools::getValue('mgp_account_number')) {
                $this->errors[] = $this->module->l(
                    'Account Number is required field for type OTHER.',
                    'mangopaysellerbankdetails'
                );
            }
            if (!Tools::getValue('mgp_bic')) {
                $this->errors[] = $this->module->l(
                    'BIC is required field for type OTHER.',
                    'mangopaysellerbankdetails'
                );
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerJavascript(
            'module-mangopay_bank_detailsjs',
            'modules/mpmangopaypayment/views/js/mangopay_bank_details.js'
        );
        $this->registerStylesheet(
            'bank_account_css',
            'modules/'.$this->module->name.'/views/css/wk_seller_bank_account.css'
        );
        $this->registerStylesheet('marketplace_accountcss', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
