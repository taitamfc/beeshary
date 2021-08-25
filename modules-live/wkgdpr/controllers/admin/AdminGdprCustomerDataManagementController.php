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

class AdminGdprCustomerDataManagementController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_gdpr_customer_requests';
        $this->className = 'WkGdprCustomerRequests';
        $this->identifier = 'id_request';

        $this->_select .= ' CONCAT(cust.`firstname`, " ", cust.`lastname`) as customer_name,
            cust.email as customer_email';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'customer` cust ON (cust.`id_customer` = a.`id_customer`)';
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->addRowAction('view');

        $requestStatuses = array(
            WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING => $this->l('Pending'),
            WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_DONE => $this->l('Fulfilled'),
        );

        $requestTypes = array(
            WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE => $this->l('Data Erasure'),
            WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_UPDATE => $this->l('Data Update'),
        );

        $this->fields_list = array(
            'id_request' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer Name'),
                'havingFilter' => true,
                'callback' => 'getCustomerViewLink',
            ),
            'request_type' => array(
                'title' => $this->l('Request Type'),
                'type' => 'select',
                'align' => 'center',
                'list' => $requestTypes,
                'class' => 'fixed-width-sm',
                'filter_key' => 'a!request_type',
                'filter_type' => 'int',
                'havingFilter' => true,
                'callback' => 'getRequestType',
            ),
            'status' => array(
                'title' => $this->l('Request Status'),
                'type' => 'select',
                'align' => 'center',
                'list' => $requestStatuses,
                'class' => 'fixed-width-sm',
                'filter_key' => 'a!status',
                'filter_type' => 'int',
                'havingFilter' => true,
                'callback' => 'getRequestStatus',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
        );
    }

    public function getRequestStatus($stage)
    {
        if ($stage == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING) {
            $this->context->smarty->assign(
                array(
                    'is_badge' => 1,
                    'badge_class' => 'badge-critical',
                    'request_status' => $this->l('Pending')
                )
            );
            $response = $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->module->name.
                '/views/templates/admin/admin_element.tpl'
            );
        } elseif ($stage == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_DONE) {
            $this->context->smarty->assign(
                array(
                    'is_badge' => 1,
                    'badge_class' => 'badge-success',
                    'request_status' => $this->l('Fulfilled')
                )
            );
            $response = $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->module->name.
                '/views/templates/admin/admin_element.tpl'
            );
        }
        return $response;
    }

    public function getRequestType($type)
    {
        if ($type == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE) {
            $this->context->smarty->assign(
                array(
                    'is_badge' => 1,
                    'badge_class' => 'badge-warning',
                    'request_status' => $this->l('Data Erasure')
                )
            );
            $response = $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->module->name.
                '/views/templates/admin/admin_element.tpl'
            );
        } elseif ($type == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_UPDATE) {
            $this->context->smarty->assign(
                array(
                    'is_badge' => 1,
                    'badge_class' => 'badge-success',
                    'request_status' => $this->l('Data Update')
                )
            );
            $response = $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->module->name.
                '/views/templates/admin/admin_element.tpl'
            );
        }
        return $response;
    }

    public function getCustomerViewLink($customerName, $row)
    {
        $linkCustomer = '';
        if ($idCustomer = $row['id_customer']) {
            $objCustomer = new Customer($idCustomer);
            if (Validate::isLoadedObject($objCustomer)) {
                $this->context->smarty->assign(
                    array(
                        'is_link_customer' => 1,
                        'id_customer' => $idCustomer,
                        'customer_name' => $customerName,
                        'customer_page_link' => $this->context->link->getAdminLink('AdminCustomers')
                    )
                );
                $linkCustomer = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_.$this->module->name.
                    '/views/templates/admin/admin_element.tpl'
                );
            }
        }
        return $linkCustomer;
    }

    public function initContent()
    {
        if (!$this->display) {
            $wkGdprCustomerDataManagementLink = $this->context->link->getAdminlink('AdminGdprCustomerDataManagement');
            $this->context->smarty->assign(
                array(
                    'wkGdprCustomerDataManagementLink' => $wkGdprCustomerDataManagementLink,
                    'wkGdprViewCustomerDataLink' => $wkGdprCustomerDataManagementLink.'&view'.$this->table,
                )
            );

            $this->content .= $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->module->name.
                '/views/templates/admin/gdpr_customer_data_management/helpers/_partials/customer_delete_panel.tpl'
            );
        }
        parent::initContent();
    }

    public function postProcess()
    {
        // change status of the updates request
        if (Tools::getIsset('change_req_status')) {
            $idRequest = Tools::getValue('id_request');
            $status = Tools::getValue('status');
            if ($idRequest && $status) {
                if (Validate::isLoadedObject($objGDPRCustReq = new WkGdprCustomerRequests($idRequest))) {
                    $objGDPRCustReq->status = $status;
                    if ($objGDPRCustReq->save()) {
                        if ($status == WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_DONE) {
                            WkGdprHelper::sendGdprEmails(
                                WkGdprHelper::WK_GDPR_MAIL_DATA_UPDATE,
                                $objGDPRCustReq->id_customer,
                                1,
                                $idRequest
                            );
                        }
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_request='.(int) $idRequest.'&view'.$this->table.
                            '&conf=5&token='.$this->token
                        );
                    } else {
                        $this->errors[] = $this->l('Some error occurred while changing request status.');
                    }
                } else {
                    $this->errors[] = $this->l('Request not found while changing status.');
                }
            } else {
                $this->errors[] = $this->l('Required information not found for changing request status.');
            }
            $this->display = 'view';
        } elseif (Tools::isSubmit('eraseCustomerData')) {
            $idCustomer = Tools::getValue('id_customer');
            if (WkGdprHelper::deleteCustomerData($idCustomer)) {
                $redirectUrl = self::$currentIndex.'&id_customer='.(int)$idCustomer.'&view'.$this->table.
                '&conf=1&token='.$this->token;
                if ($idRequest = Tools::getValue('id_request')) {
                    $redirectUrl .= '&id_request='.(int)$idRequest;
                }
                Tools::redirectAdmin($redirectUrl);
            }
        } elseif (Tools::isSubmit('wkDownloadGdprPdf')) {
            $idCustomer = Tools::getValue('id_customer');
            $WkGdprHelper = new WkGdprHelper();
            if ($WkGdprHelper->generateGdprPDF($idCustomer)) {
                die('File downloaded');
            }
        }
        parent::postProcess();
    }

    public function renderView()
    {
        if (Tools::getValue('id_request')) {
            if (Validate::isLoadedObject($objGDPRCustReq = new WkGdprCustomerRequests(Tools::getValue('id_request')))) {
                $idCustomer = $objGDPRCustReq->id_customer;
                $gdprRequestInfo = (array)$objGDPRCustReq;
                $objCustomer = new Customer($gdprRequestInfo['id_customer']);
                if (Validate::isLoadedObject($objCustomer)) {
                    $gdprRequestInfo['customer_link'] = $this->context->link->getAdminLink('AdminCustomers').
                    '&id_customer='.$idCustomer.'&viewcustomer';
                    $gdprRequestInfo['customer_email'] = $objCustomer->email;
                    $gdprRequestInfo['customer_name'] = $objCustomer->firstname.' '.$objCustomer->lastname;
                }
                $this->context->smarty->assign(
                    array(
                        'gdprRequestInfo' => $gdprRequestInfo,
                        'WK_CUSTOMER_REQUEST_STATE_PENDING'=> WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_PENDING,
                        'WK_CUSTOMER_REQUEST_STATE_DONE' => WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_DONE,
                        'WK_CUSTOMER_REQUEST_TYPE_DELETE' => WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE,
                        'WK_CUSTOMER_REQUEST_TYPE_UPDATE' => WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_UPDATE,
                        'changeStatusLink' => $this->context->link->getAdminLink('AdminCustomers').'&change_status',
                    )
                );
            }
        } else {
            $idCustomer = Tools::getValue('id_customer');
        }

        if (isset($idCustomer) && $idCustomer) {
            $customerData = WkGdprHelper::getCustomerData($idCustomer);
            $this->context->smarty->assign($customerData);
            $viewCustomerLink = $this->context->link->getAdminlink('AdminCustomers').'&viewcustomer&id_customer='
            .(int)$idCustomer;
            $viewOrderLink = $this->context->link->getAdminlink('AdminOrders').'&vieworder';
            $viewCartLink = $this->context->link->getAdminlink('AdminCarts').'&viewcart';
            $addressLink = $this->context->link->getAdminlink('AdminAddresses').'&updateaddress';
            $customerThreadsLink = $this->context->link->getAdminlink('AdminCustomerThreads').'&viewcustomer_thread';
            $customerDataManagementLink = $this->context->link->getAdminlink('AdminGdprCustomerDataManagement');

            $this->context->smarty->assign(
                array(
                    'viewCustomerLink' => $viewCustomerLink,
                    'viewOrderLink' => $viewOrderLink,
                    'viewCartLink' => $viewCartLink,
                    'addressLink' => $addressLink,
                    'customerThreadsLink' => $customerThreadsLink,
                    'customerDataManagementLink' => $customerDataManagementLink,
                    'isCustomerDataErased' => WkGdprAnonymousCustomer::customerDataErased($idCustomer)
                )
            );
        }

        return parent::renderView();
    }

    public function ajaxProcessCustomerSearch()
    {
        $searchText = Tools::getValue('searchText');
        if (Tools::strlen(trim($searchText))) {
            die(json_encode(WkGdprHelper::searchCustomer($searchText)));
        }

        die(false);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        //data table file included
        // set datatable variables
        WkGdprHelper::assignDataTableVariables();
        $this->context->controller->addJS(
            _MODULE_DIR_.$this->module->name.'/views/js/libs/datatable/jquery.dataTables.min.js'
        );
        $this->context->controller->addJS(
            _MODULE_DIR_.$this->module->name.'/views/js/libs/datatable/dataTables.bootstrap.js'
        );
        $this->context->controller->addCSS(
            _MODULE_DIR_.$this->module->name.'/views/css/libs/datatable/datatable_bootstrap.css'
        );
        //Data Table End

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_customer_data_management.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_customer_data_management.js');
    }
}
