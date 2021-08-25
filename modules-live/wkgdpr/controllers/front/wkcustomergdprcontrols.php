<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkGdprWkCustomerGdprControlsModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('My Account', 'wkcustomergdprcontrols'),
            'url' => $this->context->link->getPageLink('my-account'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Personal Data Management (GDPR)', 'wkcustomergdprcontrols'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        if (!Tools::getIsset('action') || !Tools::getValue('action') == 'setCookieAcceptedByCustomer') {
            if (Validate::isLoadedObject($this->context->customer) && isset($this->context->customer->id)) {
                $objGDPRCustReqs = new WkGdprCustomerRequests();
                $smartyVars = array();
                $smartyVars['deleteDataPending'] = 0;
                if ($deleteDataReq = $objGDPRCustReqs->getGDPRCustomerRequests(
                    $this->context->customer->id,
                    WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE
                )) {
                    if ($deleteDataReq['status'] == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING) {
                        $smartyVars['deleteDataPending'] = 1;
                    }
                }
                $smartyVars['customer_data_delete_approve'] = Configuration::get(
                    'WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE'
                );
                $smartyVars['customerEmail'] = $this->context->customer->email;
                $smartyVars['active_tab'] = Tools::getValue('tab');

                $this->context->smarty->assign($smartyVars);
                $this->setTemplate(
                    'module:'.$this->module->name.'/views/templates/front/wk-customer-gdpr-controls.tpl'
                );
            } else {
                Tools::redirect(
                    'index.php?controller=authentication&back='.
                    urlencode($this->context->link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols'))
                );
            }
        }
    }

    public function postProcess()
    {
        if (!Tools::getIsset('action') || !Tools::getValue('action') == 'setCookieAcceptedByCustomer') {
            if (Validate::isLoadedObject($this->context->customer) && isset($this->context->customer->id)) {
                $idCustomer = $this->context->customer->id;
                if (Tools::isSubmit('wk-data-update-submit')) {
                    if (!trim(Tools::getValue('data_update_reason'))) {
                        $this->errors[] = $this->module->l('Please enter updation information to update all your personal data.', 'wkcustomergdprcontrols');
                    } else {
                        $objGDPRCustReqs = new WkGdprCustomerRequests();
                        $objGDPRCustReqs->id_customer = $idCustomer;
                        $objGDPRCustReqs->request_type = WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_UPDATE;
                        $objGDPRCustReqs->request_reason = trim(Tools::getValue('data_update_reason'));
                        $objGDPRCustReqs->status = WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING;
                        if ($objGDPRCustReqs->save()) {
                            WkGdprHelper::sendGdprEmails(
                                WkGdprHelper::WK_GDPR_MAIL_DATA_UPDATE,
                                $idCustomer,
                                2,
                                $objGDPRCustReqs->id
                            );
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'wkgdpr',
                                    'wkcustomergdprcontrols',
                                    array('upd_req' => 1, 'tab' => Tools::getValue('tab'))
                                )
                            );
                        } else {
                            $this->errors[] = $this->module->l('Some error has been occurred while submitting update request please try again.', 'wkcustomergdprcontrols');
                        }
                    }
                } elseif (Tools::getValue('data_erasure_submit')) {
                    // data erasure submit
                    if (Tools::getValue('data_erasure_confirmed')) {
                        if (Configuration::get('WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE')) {
                            if (!trim(Tools::getValue('data_erasure_reason'))) {
                                $this->errors[] = $this->module->l(
                                    'Please enter reason to delete all your personal data.',
                                    'wkcustomergdprcontrols'
                                );
                            } else {
                                $CUSTOMER_REQUEST_TYPE_DELETE = WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE;
                                $objGDPRCustReqs = new WkGdprCustomerRequests();
                                $objGDPRCustReqs->id_customer = $idCustomer;
                                $objGDPRCustReqs->request_type = $CUSTOMER_REQUEST_TYPE_DELETE;
                                $objGDPRCustReqs->request_reason = trim(Tools::getValue('data_erasure_reason'));
                                $objGDPRCustReqs->status = WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING;
                                if ($objGDPRCustReqs->save()) {
                                    WkGdprHelper::sendGdprEmails(
                                        WkGdprHelper::WK_GDPR_MAIL_DATA_ERASURE,
                                        $idCustomer,
                                        2,
                                        $objGDPRCustReqs->id
                                    );
                                    Tools::redirect(
                                        $this->context->link->getModuleLink(
                                            'wkgdpr',
                                            'wkcustomergdprcontrols',
                                            array('dlt_req' => 1, 'tab' => Tools::getValue('tab'))
                                        )
                                    );
                                } else {
                                    $this->errors[] = $this->module->l('Some error occurred while submitting erasure request please try again', 'wkcustomergdprcontrols');
                                }
                            }
                            // submit deletion request to the admin with reason
                        } else {
                            // delete directly all the customer data
                            if (WkGdprHelper::deleteCustomerData($idCustomer)) {
                                Tools::redirect(
                                    $this->context->link->getModuleLink(
                                        'wkgdpr',
                                        'wkcustomergdprcontrols',
                                        array('data_dlt' => 1, 'tab' => Tools::getValue('tab'))
                                    )
                                );
                            } else {
                                $this->errors[] = $this->module->l('Some error has been occurred while deleting your data. Please contact to admin regarding this.', 'wkcustomergdprcontrols');
                            }
                        }
                    } else {
                        $this->errors[] = $this->module->l('Please first confirm the deletion of your personal data.', 'wkcustomergdprcontrols');
                    }
                } elseif (Tools::getValue('wkDownloadCustomerData')) {
                    $idCustomer = $this->context->customer->id;
                    $WkGdprHelper = new WkGdprHelper();
                    if ($WkGdprHelper->generateGdprPDF($idCustomer)) {
                        die('File downloaded');
                    }
                } elseif (Tools::getValue('data_access_submit')) {
                    $custEmail = Tools::getValue('gdpr_cusstomer_email');
                    if (!trim($custEmail)) {
                        $this->errors[] = $this->module->l('Please enter an email in which we will send your personal data.', 'wkcustomergdprcontrols');
                    } elseif (!Validate::isEmail($custEmail)) {
                        $this->errors[] = $this->module->l('Please enter a valid email.', 'wkcustomergdprcontrols');
                    } else {
                        if (WkGdprHelper::sendGdprEmails(
                            WkGdprHelper::WK_GDPR_MAIL_DATA_ACCESS,
                            $idCustomer,
                            1,
                            0,
                            $custEmail
                        )) {
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'wkgdpr',
                                    'wkcustomergdprcontrols',
                                    array('data_emailed' => 1, 'tab' => Tools::getValue('tab'))
                                )
                            );
                        }
                    }
                }
            } else {
                Tools::redirect(
                    'index.php?controller=authentication&back='.
                    urlencode($this->context->link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols'))
                );
            }
        }
    }

    public function displayAjaxSetCookieAcceptedByCustomer()
    {
        if (!$this->isTokenValid()) {
            die('ko');
        }
        $this->context->cookie->cookie_accepted_customer = strtotime(date('Y-m-d'));
        if (isset($this->context->cookie->cookie_accepted_customer)
            && $this->context->cookie->cookie_accepted_customer
        ) {
            die('ok');
        } elseif ($this->context->cookie->write()) {
            die('ok');
        }
        die('ko');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->context->controller->registerStylesheet(
            'module-wkgdpr-customer-gdpr-controls-css',
            'modules/'.$this->module->name.'/views/css/front/wk-customer-gdpr-controls.css'
        );

        $this->context->controller->registerJavascript(
            'module-wkgdpr-customer-gdpr-controls-js',
            'modules/'.$this->module->name.'/views/js/front/wk_customer_gdpr_controls.js',
            array('position' => 'bottom', 'priority' => 999)
        );
    }
}
