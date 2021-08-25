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

class AdminSellerVacationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_seller_vacation';
        $this->className = 'SellerVacationDetail';
        $this->list_no_link = true;
        $this->context = Context::getContext();
        $this->addRowAction('delete');

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_vacation_lang` msvl ON (msvl.`id` = a.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (mpsi.`id_seller` = a.`id_seller`)';
        $this->_select = 'CONCAT(mpsi.`seller_firstname`, \' \', mpsi.`seller_lastname`) as seller_name, mpsi.`business_email`, msvl.`description`';
        $this->_where = 'AND msvl.`id_lang` = '.(int) $this->context->language->id;
        $this->identifier = 'id';
        parent::__construct();

        $this->fields_list = array(
            'id' => array(
                'title' => $this->trans('ID'),
                'align' => 'center',
            ),

            'seller_name' => array(
                'title' => $this->trans('Name'),
                'align' => 'center',
            ),

            'business_email' => array(
                'title' => $this->trans('Email'),
                'align' => 'center',
            ),

            'from' => array(
                'title' => $this->trans('From'),
                'align' => 'center',
            ),

            'to' => array(
                'title' => $this->trans('To'),
                'align' => 'center',
            ),

            'description' => array(
                'title' => $this->trans('Description'),
                'align' => 'center',
            ),

            'addtocart' => array(
                'title' => $this->trans('Display Add To Cart'),
                'align' => 'center',
                'active' => 'addtocart_status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ),

            'active' => array(
                'title' => $this->trans('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ),
        );

        $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->trans('Delete selected'),
                    'confirm' => $this->trans('Delete selected items?'),
                    'icon' => 'icon-trash', ),
                );

    }

    public function initToolbar()
    {
        parent::initToolbar();

        // Remove "Add" button from toolbar
        unset($this->toolbar_btn['new']);
    }
    
    public function renderList()
    {
        $html = '<p class="alert-info alert">'. $this->trans('For setting up cron job for vacation please use following path :').'<br> <br> <span class="text-danger"> 0 24 * * * /usr/bin/php&nbsp; &nbsp;'._PS_ROOT_DIR_.'/modules/mpsellervacation/ps_cron.php </span> </p>';

        $html .= parent::renderList();
        return $html;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('addtocart_statusmarketplace_seller_vacation')) {
            if ($this->loadObject(true)) {
                $obj_vacation = $this->loadObject(true);
                if ($obj_vacation->active == '1') {
                    if ($obj_vacation->addtocart == '1') {
                        $this->changeMpCartStatus($obj_vacation, 0);
                    } else {
                        $this->changeMpCartStatus($obj_vacation, 1);
                    }

                    $this->redirect_after = self::$currentIndex;
                }
            }
        }

        if (Tools::isSubmit('statusmarketplace_seller_vacation')) {
            if ($this->loadObject(true)) {
                $obj_vacation = $this->loadObject(true);
                $vacation_id = $obj_vacation->id;
                $seller_id = $obj_vacation->id_seller;
                if ($obj_vacation->active) {
                    //if vacation active
                    $obj_seller_vacation_detail = new SellerVacationDetail();
                    $seller_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetail($seller_id);
                    if ($seller_product_detail) {
                        $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, true);
                    }
                    $this->mpsvMailSend(1); // going to disapproved
                } else {
                    if ($vacation_id) {
                        $obj_seller_vacation_detail = new SellerVacationDetail();
                        $vacation_expired = $obj_seller_vacation_detail->mpSellerVacationExpire($vacation_id);
                        if ($vacation_expired) {
                            $this->errors[] = Tools::displayError('Vacation has been expired.');
                        } else {
                            $seller_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetail($seller_id);
                            if ($seller_product_detail) {
                                if ($obj_vacation->addtocart == 1) {
                                    $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, true);
                                } else {
                                    $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, false);
                                }
                            }

                            $this->mpsvMailSend(2); // going to approved
                        }
                    }
                }
            }
        }

        if (empty($this->errors)) {
            return parent::postProcess();
        }
    }

    /**
     * changing cart Status either true or false.
     *
     * @param [type] $obj_vacation [description]
     * @param [type] $status       [description]
     *
     * @return [type] [description]
     */
    public function changeMpCartStatus($obj_vacation, $status)
    {
        $obj_seller_vacation_detail = new SellerVacationDetail($obj_vacation->id);
        $obj_seller_vacation_detail->addtocart = $status;
        $obj_seller_vacation_detail->save();
        $valid_vacation_info = $obj_seller_vacation_detail->mpSellerValidVacation($obj_vacation->id_seller, $this->context->language->id);
        if ($valid_vacation_info) {
            $seller_id = $obj_vacation->id_seller;
            $seller_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetail($seller_id);
            if ($seller_product_detail) {
                if ($status == 1) {
                    $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
                } else {
                    $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 0);
                }
            }
        }
    }

    /**
     * [mpsvMailSend - sending email when vacation is approved or disapproved].
     *
     * @param [type] $mailvalue [description]
     *
     * @return [type] [description]
     */
    public function mpsvMailSend($mailvalue)
    {
        $id_lang = $this->context->language->id;
        $obj_seller_vacation_detail = new SellerVacationDetail();
        $obj_vacation = $this->loadObject(true);
        $id_seller = $obj_vacation->id_seller;
        $mail_detail = $obj_seller_vacation_detail->getMpSellerDetailsForMail($id_seller);
        $templateVars = array(
            '{seller_name}' => $mail_detail['seller_name'],
            '{business_email}' => $mail_detail['business_email'],
            '{from}' => $obj_vacation->from,
            '{to}' => $obj_vacation->to,
            '{description}' => $obj_vacation->description,
            );

        if ($mailvalue == 1) {
            Mail::Send(
                $id_lang,
                'disapproved_mail',
                Mail::l('Disapproved Mail For Vacation', $id_lang),
                $templateVars,
                $mail_detail['business_email'],
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'mpsellervacation/mails/',
                false,
                null,
                null
            );
        } elseif ($mailvalue == 2) {
            Mail::Send(
                $id_lang,
                'approved_mail',
                Mail::l('Approved Mail For Vacation', $id_lang),
                $templateVars,
                $mail_detail['business_email'],
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'mpsellervacation/mails/',
                false,
                null,
                null
            );
        }
    }
}
