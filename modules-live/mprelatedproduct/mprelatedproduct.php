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

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once dirname(__FILE__).'/../mprelatedproduct/classes/RelatedProductInfo.php';
class MpRelatedProduct extends Module
{
    public $html = '';
    public function __construct()
    {
        $this->tab = 'front_office_features';
        $this->name = 'mprelatedproduct';
        $this->bootstrap = true;
        $this->author = 'Webkul';
        $this->version = '5.0.1';
        $this->need_instance = 0;
        $this->dependencies = array('marketplace');
        parent::__construct();

        $this->displayName = $this->trans('Marketplace Related Product');
        $this->description = $this->trans('Display all related product of marketplace');
    }

    public function hookDisplayFooterProduct($params)
    {
        $context = Context::getContext();
        $id_customer = $this->context->cookie->id_customer;
        if (!$id_customer) {
            $id_customer = 0;
        }
        $id_product = $params['product']['id_product'];
        $id_lang = $context->language->id;
        $p = 1;
        if (0 == Configuration::get('MP_DISPLAY_RELATED_PRODUCT_COUNT')) {
            $n = 1000;
        } else {
            $n = (int) Configuration::get('MP_DISPLAY_RELATED_PRODUCT_COUNT');
        }

        $display_id = (int) Configuration::get('MP_DISPLAY_RELATED_PRODUCT');
        $all_sellers = (int) Configuration::get('MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS');
        $mp_id_product = array();
        $seller_product_obj = new WkMpSellerProduct();
        $mp_product_details = $seller_product_obj->getSellerProductByPsIdProduct($id_product);
        if ($mp_product_details) {
            $id_seller = $mp_product_details['id_seller'];
            $id_category_arr = Product::getProductCategoriesFull($id_product, $id_lang);
            //start
            if ($id_category_arr) {
                $id_category_arr = array_reverse($id_category_arr, true);
                if ($display_id == 1) {
                    // 1 for listing will be of same category products
                    if ($all_sellers) {
                        $mp_id_product = $this->displayAllRelatedProducts($id_product, false, $id_category_arr, $p, $n);
                    } else {
                        $mp_id_product = $this->displayAllRelatedProducts(
                            $id_product,
                            $id_seller,
                            $id_category_arr,
                            $p,
                            $n
                        );
                    }
                } elseif ($display_id == 2) {
                    // 2 for listing will be of who bought also bought this products
                    if ($all_sellers) {
                        $mp_id_product = $this->displayPurchasedProducts($id_product, false, $id_category_arr, $n);
                    } else {
                        $mp_id_product = $this->displayPurchasedProducts($id_product, $id_seller, $id_category_arr, $n);
                    }
                }
            }
            if (count($mp_id_product) > 0) {
                $product_details = array();
                $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
                $productSettings = $factory->getPresentationSettings();
                $presenter = $factory->getPresenter();

                foreach ($mp_id_product as $mp_pid) {
                    if ($mp_pid) {
                        $productInfos = new Product((int) $mp_pid, false, $this->context->language->id);
                        $productInfos = (array)$productInfos;
                        $productInfos['id_product'] = $productInfos['id'];
                        $productInfos = Product::getProductProperties($this->context->language->id, $productInfos);
                        $product_details[] = $presenter->present(
                            $productSettings,
                            $productInfos,
                            $this->context->language
                        );
                    }
                }

                $this->context->smarty->assign(
                    array(
                        'mpProducts' => $product_details,
                        'display_id' => $display_id
                    )
                );
                return $this->fetch('module:mprelatedproduct/views/templates/hook/related_product.tpl');
            }
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if ('product' === $this->context->controller->php_self) {
            $this->context->controller->addJQueryPlugin('bxslider');
            $this->context->controller->registerStylesheet(
                'module-mp-related-css',
                'modules/'.$this->name.'/views/css/related_products.css'
            );

            $this->context->controller->registerJavascript(
                'relatedproduct-js',
                'modules/'.$this->name.'/views/js/relatedproduct.js',
                [
                  'position' => 'bottom',
                  'priority' => 900,
                ]
            );
        }
    }

    public function displayAllRelatedProducts($id_product, $id_seller, $id_category_arr, $p, $n)
    {
        $mp_id_product = array();
        foreach ($id_category_arr as $category) {
            $catg_product = RelatedProductInfo::getProductsByCatId($category['id_category'], $p, $n + 1, $id_seller);
            if ($catg_product) {
                foreach ($catg_product as $mp_product) {
                    $mp_product_info = WkMpSellerProduct::getSellerProductByIdProduct($mp_product['id_seller_product']);
                    if ($mp_product_info) {
                        if ($id_seller) {
                            if ($mp_product_info['id_ps_product'] != $id_product
                                && $id_seller == $mp_product_info['id_seller']
                            ) {
                                $mp_id_product[] = $mp_product_info['id_ps_product'];
                            }
                        } else {
                            if ($mp_product_info['id_ps_product'] != $id_product) {
                                $mp_id_product[] = $mp_product_info['id_ps_product'];
                            }
                        }
                    }
                }
                if ($mp_id_product) {
                    break;
                }
            }
        }

        return $mp_id_product;
    }

    public function displayPurchasedProducts($id_product, $id_seller, $id_category_arr, $n)
    {
        $mp_id_product = array();
        foreach ($id_category_arr as $category) {
            $catg_product = RelatedProductInfo::getProductsByCatId($category['id_category']);
            if ($catg_product) {
                $i = 1;
                foreach ($catg_product as $mp_product) {
                    $mp_product_info = WkMpSellerProduct::getSellerProductByIdProduct($mp_product['id_seller_product']);
                    if ($mp_product_info) {
                        if ($id_seller) {
                            if ($mp_product_info['id_ps_product'] != $id_product
                                && $id_seller == $mp_product_info['id_seller']
                            ) {
                                $order_detail = RelatedProductInfo::getOrderDetailsByPsId(
                                    $mp_product_info['id_ps_product']
                                );
                                if ($order_detail) {
                                    $mp_id_product[] = $mp_product_info['id_ps_product'];
                                    if ($i == $n) {
                                        break;
                                    }
                                    ++$i;
                                }
                            }
                        } else {
                            if ($mp_product_info['id_ps_product'] != $id_product) {
                                $order_detail = RelatedProductInfo::getOrderDetailsByPsId(
                                    $mp_product_info['id_ps_product']
                                );
                                if ($order_detail) {
                                    $mp_id_product[] = $mp_product_info['id_ps_product'];
                                    if ($i == $n) {
                                        break;
                                    }
                                    ++$i;
                                }
                            }
                        }
                    }
                }
                if ($mp_id_product) {
                    break;
                }
            }
        }

        return $mp_id_product;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('actionFrontControllerSetMedia')
            ) {
            return false;
        }
        Configuration::updateValue('MP_DISPLAY_RELATED_PRODUCT', 1);
        Configuration::updateValue('MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS', 1);
        Configuration::updateValue('MP_DISPLAY_RELATED_PRODUCT_COUNT', 0);

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('MP_DISPLAY_RELATED_PRODUCT')
            || !Configuration::deleteByName('MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS')
            || !Configuration::deleteByName('MP_DISPLAY_RELATED_PRODUCT_COUNT')) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();
            if (!count($this->_postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        } else {
            $this->html .= '<br />';
        }

        $this->html .= $this->renderForm();

        return $this->html;
    }

    public function renderForm()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $list_option = array(
            array('id_category' => '1' , 'name' => $this->trans('All Products')),
            array('id_category' => '2', 'name' => $this->trans('Purchased Products')),
            );
        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->trans('Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Display Slider By'),
                        'name' => 'MP_DISPLAY_RELATED_PRODUCT',
                        'lang' => true,
                        'required' => true,
                        'hint' => $this->trans('Marketplace related product will display'),
                        'options' => array(
                            'query' => $list_option,
                            'id' => 'id_category',
                            'name' => 'name', ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Show all seller\'s products'),
                        'name' => 'MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled'),
                            ),
                        ),
                        'hint' => $this->trans('If No, display only current seller\'s products'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Number of Products'),
                        'name' => 'MP_DISPLAY_RELATED_PRODUCT_COUNT',
                        'col' => '2',
                        'required' => true,
                        'hint' => $this->trans('Enter 0 for all product.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save'),
                ),
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->currentIndex .= '&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;

        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        return array(
            'MP_DISPLAY_RELATED_PRODUCT' => Tools::getValue(
                'MP_DISPLAY_RELATED_PRODUCT',
                Configuration::get('MP_DISPLAY_RELATED_PRODUCT')
            ),
            'MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS' => Tools::getValue(
                'MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS',
                Configuration::get('MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS')
            ),
            'MP_DISPLAY_RELATED_PRODUCT_COUNT' => Tools::getValue(
                'MP_DISPLAY_RELATED_PRODUCT_COUNT',
                Configuration::get('MP_DISPLAY_RELATED_PRODUCT_COUNT')
            ),
        );
    }

    private function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Validate::isInt(trim(Tools::getValue('MP_DISPLAY_RELATED_PRODUCT_COUNT')))
                || trim(Tools::getValue('MP_DISPLAY_RELATED_PRODUCT_COUNT')) < 0
            ) {
                $this->_postErrors[] = $this->trans('Number of products are invalid.');
            }
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MP_DISPLAY_RELATED_PRODUCT', Tools::getValue('MP_DISPLAY_RELATED_PRODUCT'));
            Configuration::updateValue(
                'MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS',
                Tools::getValue('MP_DISPLAY_RELATED_PRODUCT_FOR_SELLERS')
            );
            Configuration::updateValue(
                'MP_DISPLAY_RELATED_PRODUCT_COUNT',
                Tools::getValue('MP_DISPLAY_RELATED_PRODUCT_COUNT')
            );
        }

        $this->html .= $this->displayConfirmation($this->trans('Settings updated'));
        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin(
            $module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4'
        );
    }
}
