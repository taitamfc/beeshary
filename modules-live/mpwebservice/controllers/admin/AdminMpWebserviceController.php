<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMpWebserviceController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_webservice_key';
        $this->className = 'WkMpWebserviceKey';
        $this->list_id = 'id_wk_mp_webservice_key';
        $this->identifier = 'id_wk_mp_webservice_key';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` wms ON (wms.`id_seller` = a.`id_seller`)';
        $this->_select = 'wms.`business_email` AS email';
        $this->fields_list = array(
            'id_wk_mp_webservice_key' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'key' => array(
                'title' => $this->l('Key (auth_key)'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'email' => array(
                'title' => $this->l('Seller Email'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'description' => array(
                'title' => $this->l('Key description'),
                'align' => 'center',
                'search' => false,
                'havingFilter' => true,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new'),
            'icon' => 'process-icon-new'
        );
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Seller Webservice Accounts'),
                'icon' => 'icon-lock'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Seller Shop Name'),
                    'name' => 'id_seller',
                    'required' => true,
                    'hint' => $this->l('Marketplace Seller'),
                    'class' => 'chosen',
                    'options' => array(
                        'query' => WkMpSeller::getAllSeller(),
                        'id' => 'id_seller',
                        'name' => 'shop_name_unique'
                    ),
                ),
                array(
                    'type' => 'textbutton',
                    'label' => $this->l('Seller Key'),
                    'name' => 'key',
                    'id' => 'code',
                    'required' => true,
                    'hint' => $this->l('Webservice account key.'),
                    'desc' => $this->l('auth_key in End URL'),
                    'button' => array(
                        'label' => $this->l('Generate!'),
                        'attributes' => array(
                            'onclick' => 'gencode(32)'
                        )
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Key description'),
                    'name' => 'description',
                    'rows' => 3,
                    'cols' => 110,
                    'hint' => $this->l('Quick description of the key: who it is for, what permissions it has, etc.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
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
                    'type' => 'mpresources',
                    'label' => $this->l('Permissions'),
                    'name' => 'mpapi',
                )
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!$this->loadObject(true)) {
            return;
        }

        if ($idMpWebservice = Tools::getValue('id_wk_mp_webservice_key')) {
            $mpWebServiceKey = new WkMpWebserviceKey($idMpWebservice);
            $keyInfo = WkMpWebserviceKey::getMpWebserviceKey($mpWebServiceKey->id_seller, $idMpWebservice);
            if ($keyInfo) {
                $keyInfo['mpresource'] = Tools::jsonDecode($keyInfo['mpresource']);
                $this->context->smarty->assign(array(
                    'selected_mpresources' => $keyInfo,
                ));
                $this->fields_value = array('id_seller' => $mpWebServiceKey->id_seller);
            }
        }

        $this->tpl_form_vars = array(
            'mpresources' => WebserviceSpecificManagementSeller::$allowedMethods,
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        $idMpWebservice = Tools::getValue('id_wk_mp_webservice_key');
        $idSeller = Tools::getValue('id_seller');
        $key = Tools::getValue('key');
        $status = Tools::getValue('active');
        $keyDescription = Tools::getValue('description');
        $mpAPI = Tools::jsonEncode(Tools::getValue('mpapi'));

        if ($idMpWebservice) {
            $mpWsKey = new WkMpWebserviceKey($idMpWebservice);
        } else {
            $mpWsKey = new WkMpWebserviceKey();
        }

        if (empty($idSeller)) {
            $this->errors[] = $this->module->l('Seller is required field.', 'webservice');
        }

        if (empty($key)) {
            $this->errors[] = $this->module->l('Key is required field.', 'webservice');
        }

        if (empty($this->errors)) {
            $mpWsKey->key = $key;
            $mpWsKey->description = $keyDescription;
            $mpWsKey->mpresource = $mpAPI;
            $mpWsKey->id_seller = $idSeller;
            $mpWsKey->active = $status;
            if ($mpWsKey->save()) {
                if ($idMpWebservice) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }
            }
        } else {
            if ($idMpWebservice) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function postProcess()
    {
        $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/mpwebservice.js');
        parent::postProcess();
    }
}
