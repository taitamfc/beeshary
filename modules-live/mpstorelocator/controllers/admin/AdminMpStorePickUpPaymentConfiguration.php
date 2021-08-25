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

class AdminMpStorePickUpPaymentConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->lang = true;
        $this->table = 'mpstore_pay';
        $this->className = 'MpStorePay';
        $this->_select = 'a.`id_mp_store_pay` as `id_image`';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'mpstore_pay_lang` spl';
        $this->_join .= ' ON (spl.`id_mp_store_pay` = a.`id_mp_store_pay`)';
        $this->_where = ' AND spl.`id_lang` = '.(int) $this->context->language->id;
        $this->_group = 'GROUP BY a.`id_mp_store_pay`';
        $this->identifier = 'id_mp_store_pay';
        parent::__construct();

        $this->fields_list = array(
            'id_image' => array(
                'title' => $this->l('Logo'),
                'align' => 'text-center',
                'type' => 'text',
                'search' => false,
                'callback' => 'callPaymentLogo'
            ),
            'payment_name' => array(
                'title' => $this->l('Payment Name'),
                'align' => 'text-center',
                'type' => 'text',
                'havingFilter' => true
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'align' => 'center',
                'active' => 'status',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'left',
                'type' => 'date',
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function callPaymentLogo($idLogo)
    {
        return "<img src='"._MODULE_DIR_."mpstorelocator/views/img/payment_logo/".$idLogo.".jpg'/>";
    }

    public function initContent()
    {
        parent::initContent();
        $this->initToolbar();

        if ($this->display == '') {
            $this->content = $this->storePaymentConfigRenderForm() . $this->content;
        }
        $this->context->smarty->assign(
            array(
                'content' => $this->content,
            )
        );
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['Add Outlet'] = array(
                'href' => self::$currentIndex.'&addmpstore_pay&token='.$this->token,
                'desc' => $this->l('Add New Payment'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * renderList generate renderlist with edit and delete action.
     *
     * @return [type] [description]
     */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $psImgUrl = _PS_MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'.Tools::getValue('id_mp_store_pay').'.jpg';
        if ($imgExist = file_exists($psImgUrl)) {
            $imgUrl = _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'.Tools::getValue('id_mp_store_pay').'.jpg';
            $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$imgUrl."'>";
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add new payment method'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l("Name"),
                    'name' => 'payment_name',
                    'required' => true,
                    'havingFilter' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Payment icon'),
                    'name' => 'mp_payment_icon',
                    'required' => true,
                    'image' => $imgExist ? $image : false,
                    'display_image' => true,
                    'desc' => $this->l('For the best view, please upload a 40 x 40 pixel size PNG image file.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l("Status"),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'btnSubmitStorePickupPayment',
            ),
        );
        return parent::renderForm();
    }

    public function storePaymentConfigRenderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Store Payment Configuration'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Store Pick up payment'),
                    'name' => 'MP_STORE_PICK_UP_PAYMENT',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display option for removing the product'),
                    'name' => 'MP_PICK_UP_PAYMENT_RESTRICT',
                    'required' => true,
                    'form_group_class' => 'wk_store_payment_enable',
                    'is_bool' => true,
                    'desc' => $this->l(
                        'Option to remove the product from cart if the product is not available in any store'
                    ),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'btnSubmitStorePickupPaymentConfig',
            ),
        );
        $this->fields_value = array(
            'MP_STORE_PICK_UP_PAYMENT' => Tools::getValue(
                'MP_STORE_PICK_UP_PAYMENT',
                Configuration::get('MP_STORE_PICK_UP_PAYMENT')
            ),
            'MP_PICK_UP_PAYMENT_RESTRICT' => Tools::getValue(
                'MP_PICK_UP_PAYMENT_RESTRICT',
                Configuration::get('MP_PICK_UP_PAYMENT_RESTRICT')
            ),
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmitStorePickupPayment')) {
            $psDefaultLang = Configuration::get('PS_LANG_DEFAULT');
            $idStorePayment = Tools::getValue('id_mp_store_pay');
            if (empty($_FILES['mp_payment_icon']['size']) && !$idStorePayment) {
                $this->errors[] = $this->l(
                    'Payment icon is required'
                );
            } else {
                if ($_FILES['mp_payment_icon']['size'] != 0) {
                    list($shopWidth, $shopHeight) = getimagesize($_FILES['mp_payment_icon']['tmp_name']);
                    if (800 < $shopWidth || 800 < $shopHeight) {
                        $this->errors[] = $this->l('File size must be less than 40 x 40 px.');
                    } else {
                        if (0 == $_FILES['mp_payment_icon']['error']) {
                            if (!ImageManager::isCorrectImageFileExt($_FILES['mp_payment_icon']['name'])) {
                                $this->errors[] = $this->l(
                                    'Invalid image extension. Only jpg, jpeg, gif file can be uploaded.'
                                );
                            }
                        }
                    }
                }
            }
            if (empty(Tools::getValue('payment_name_'.$psDefaultLang))) {
                $this->errors['payment_name_'.$psDefaultLang] = $this->l(
                    'Required payment name in default lang'
                );
            }
            foreach (Language::getLanguages(true) as $language) {
                if (!Validate::isName(Tools::getValue('payment_name_'.$language['id_lang']))) {
                    $this->errors['payment_name_'.$language['id_lang']] = $this->l(
                        'Invalid payment name '. $language['name']. 'language'
                    );
                }
            }
            if (empty($this->errors)) {
                if ($idStorePayment) {
                    $objStorePay = new MpStorePay($idStorePayment);
                } else {
                    $objStorePay = new MpStorePay();
                }
                foreach (Language::getLanguages(true) as $language) {
                    $langId = $language['id_lang'];

                    if (!Tools::getValue('payment_name_'.$language['id_lang'])) {
                        $langId = $psDefaultLang;
                    }
                    $objStorePay->payment_name[$language['id_lang']] = pSQL(Tools::getValue('payment_name_'.$langId));
                }
                
                $objStorePay->active = (int)Tools::getValue('active');
                $objStorePay->save();
                $idInsert = $objStorePay->id;

                if ($idInsert) {
                    $width = 40;
                    $height = 40;
                    $paymentLogoPath = _PS_MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'.$idInsert.'.jpg';
                    if (0 != $_FILES['mp_payment_icon']['size']) {
                        ImageManager::resize(
                            $_FILES['mp_payment_icon']['tmp_name'],
                            $paymentLogoPath,
                            $width,
                            $height
                        );
                    } else {
                        if (!$idStorePayment) {  // if edit store
                            $defaultImagePath = _PS_MODULE_DIR_.'mpstorelocator/views/img/payment_logo/default.jpg';
                            ImageManager::resize($defaultImagePath, $paymentLogoPath, $width, $height);
                        }
                    }
                    // Save store products if provided
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = Tools::displayError(
                        $this->l('Some problem occured while updating records. Please try after some time.')
                    );
                }
            }
        } elseif (Tools::isSubmit('btnSubmitStorePickupPaymentConfig')) {
            Configuration::updateValue('MP_STORE_PICK_UP_PAYMENT', Tools::getValue('MP_STORE_PICK_UP_PAYMENT'));
            Configuration::updateValue('MP_PICK_UP_PAYMENT_RESTRICT', Tools::getValue('MP_PICK_UP_PAYMENT_RESTRICT'));
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/mpstore_pickup_payment.js');
        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/mpstore_pickup_payment.js');
        $this->addCss(_MODULE_DIR_.'mpstorelocator/views/css/admin/mpstoreconfig.css');
    }
}
