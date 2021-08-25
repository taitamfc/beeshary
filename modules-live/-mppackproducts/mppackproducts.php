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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/WkMpPackProduct.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';

if (Module::isEnabled('mpvirtualproduct')) {
    include_once dirname(__FILE__).'/../mpvirtualproduct/classes/MarketplaceVirtualProduct.php';
}

class MpPackProducts extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mppackproducts';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Marketplace Pack Products');
        $this->description = $this->l('To create the pack products in marketplace');
    }

    /**
     * Add Media files in backoffice
     *
     * @param  array $params
     * @return void
     */
    public function hookActionAdminControllerSetMedia($params)
    {
        $controller = Tools::getValue('controller');
        if ($controller == 'AdminSellerProductDetail') {
            Media::addJsDef(array(
                'noMatchesFound' => $this->l('No matches found'),
            ));
            $this->context->controller->addCSS($this->_path.'views/css/mppackproducts.css');
            $this->context->controller->addJS($this->_path.'views/js/mppackproducts.js');
        }
    }

    /**
     * Add Media files in front office
     *
     * @param  array $params
     * @return void
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        $controller = Tools::getValue('controller');
        if ('addproduct' == $controller || 'updateproduct' == $controller) {
            $mp_seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);

            $jsDef = array(
                'id_seller' => $mp_seller['id_seller'],
                'mppack_module_dir' => _MODULE_DIR_.'mppackproducts/',
                'id_mp_pack_product' => Tools::getValue('id_mp_product'),
                'invalid_product_name' => $this->l('Please enter valid product name'),
                'invalid_quantity' => $this->l('Please enter valid product quantity'),
                'noMatchesFound' => $this->l('No matches found'),
            );

            Media::addJsDef($jsDef);

            $this->context->controller->registerJavascript(
                'mppackproducts',
                'modules/'.$this->name.'/views/js/mppackproducts.js'
            );

            $this->context->controller->registerStylesheet(
                'mppackcss',
                'modules/'.$this->name.'/views/css/mppackproducts.css'
            );
        }
    }

    /**
     * Action when first time MP product activate and creating in PS
     *
     * @param  array $params
     * @return void
     */
    public function hookActionToogleMPProductCreateStatus($params)
    {
        $ps_id_prod = $params['id_product'];
        if ($ps_id_prod) {
            $mp_product_dtl = WkMpSellerProduct::getSellerProductByPsIdProduct($ps_id_prod);
            if ($mp_product_dtl) {
                $mp_id_prod = $mp_product_dtl['id_mp_product'];

                $obj_mppack = new WkMpPackProduct();
                $isPackProduct = $obj_mppack->isPackProduct($mp_id_prod);
                if ($isPackProduct) {
                    $obj_mppack->addToPsPack($mp_id_prod, $ps_id_prod);
                }
            }
        }
    }

    /**
     * Display Pack form below product name.
     *
     * @return tpl
     */
    public function hookDisplayMpAddProductNameBottom()
    {
        if (isset($this->context->customer->id)) {
            $id_customer = $this->context->customer->id;
            $mp_seller = WkMpSeller::getSellerDetailByCustomerId($id_customer);
            if ($mp_seller && $mp_seller['active']) {
                $this->context->smarty->assign(array(
                    'id_seller' => $mp_seller['id_seller'],
                    'id_customer' => $id_customer,
                ));

                return $this->fetch('module:mppackproducts/views/templates/hook/pack-product-form.tpl');
            }
        } elseif (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            Media::addJsDef(array(
                'mppack_module_dir' => _MODULE_DIR_.'mppackproducts/',
                'invalid_product_name' => $this->l('Please enter valid product name'),
                'invalid_quantity' => $this->l('Please enter valid product quantity'),
                'sugpackprod_url' => $this->context->link->getModuleLink('mppackproducts', 'suggestpackproducts'),
            ));
            $this->context->smarty->assign('module_dir', _MODULE_DIR_.'mppackproducts/');
            return $this->display(__FILE__, 'admin-pack-product-form.tpl');
        }
    }

    /**
     * Hook defined in virtual product module to display radio button
     * When virtual product module installed
     *
     * @return tpl
     */
    public function hookDisplayMpPackProductOption()
    {
        if (isset($this->context->customer->id)) {
            return $this->fetch('module:mppackproducts/views/templates/hook/mppackproductoption.tpl');
        } elseif (isset($this->context->employee->id)) {
            return $this->display(__FILE__, 'mppackproductoption.tpl');
        }
    }

    /**
     * Display radio button in the top of UPDATE product page
     *
     * @return tpl
     */
    public function hookDisplayMpAddProductContentTop()
    {
        return $this->display(__FILE__, 'product-type-radiobtn.tpl');
    }

    /**
     * Display radio button in the top of ADD product page
     *
     * @return tpl
     */
    public function hookDisplayMpUpdateProductContentTop()
    {
        $mp_id_prod = Tools::getValue('id_mp_product');
        $obj_mppack = new WkMpPackProduct();
        $pked_prods = $obj_mppack->getPackedProducts($mp_id_prod);
        if (!$pked_prods) {
            $obj_mppack->isPackProductFieldUpdate($mp_id_prod, 0);
        }
        if (Module::isInstalled('mpvirtualproduct') && Module::isEnabled('mpvirtualproduct')) {
            $obj_mvp = new MarketplaceVirtualProduct();
            $is_virtual_prod = $obj_mvp->isMpProductIsVirtualProduct($mp_id_prod);
        } else {
            $is_virtual_prod = false;
        }

        $isPackProduct = $obj_mppack->isPackProduct($mp_id_prod);
        if ($isPackProduct) {
            $this->context->smarty->assign('product_type', 2);
        } elseif ($is_virtual_prod) {
            $this->context->smarty->assign('product_type', 3);
        } else {
            if (WkMpPackProduct::isPackItem($mp_id_prod)) {
                $this->context->smarty->assign('is_pack_item', 1);
            }

            $this->context->smarty->assign('product_type', 1);
        }
        $combi_exist = WkMpPackProduct::checkProductCombination($mp_id_prod, $this->context->language->id);
        if ($combi_exist) {
            $this->context->smarty->assign('combi_exist', 1);
        }
        $obj_seller_prod = new WkMpSellerProduct($mp_id_prod);
        if ($obj_seller_prod->id_seller) {
            return $this->display(__FILE__, 'product-type-radiobtn.tpl');
        } else {
            return $this->fetch('module:mppackproducts/views/templates/hook/product-type-radiobtn.tpl');
        }
    }

    /**
     * Add Search Pack form after product name input field
     *
     * @return tpl
     */
    public function hookDisplayMpUpdateProductNameBottom()
    {
        $obj_mppack = new WkMpPackProduct();
        $idMpProduct = Tools::getValue('id_mp_product');
        $mpPackProducts = $obj_mppack->getPackedProducts($idMpProduct);
        if (!$mpPackProducts) {
            $obj_mppack->isPackProductFieldUpdate($idMpProduct, 0);
        }

        $objMpSeller = new WkMpSellerProduct($idMpProduct);
        //Assign current lang according to multilanguage functionality
        WkMpHelper::assignDefaultLang($objMpSeller->id_seller);

        $isPackProduct = $obj_mppack->isPackProduct($idMpProduct);
        if ($isPackProduct) {
            if ($mpPackProducts) {
                $mpPackProducts = $this->customizedAllPactProducsArray($mpPackProducts);
            }

            $this->context->smarty->assign([
                'isPackProduct' => $isPackProduct,
                'mpPackProducts' => $mpPackProducts,
            ]);
        }
        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            Media::addJsDef(array(
                'mppack_module_dir' => _MODULE_DIR_.'mppackproducts/',
                'invalid_product_name' => $this->l('Please enter valid product name'),
                'invalid_quantity' => $this->l('Please enter valid product quantity'),
                'sugpackprod_url' => $this->context->link->getModuleLink('mppackproducts', 'suggestpackproducts'),
                'id_seller' => $objMpSeller->id_seller,
                'id_mp_pack_product' => $idMpProduct,
            ));
            //for admin panel at add product controller
            return $this->display(__FILE__, 'admin-pack-product-form.tpl');
        } else {
            return $this->fetch('module:mppackproducts/views/templates/hook/pack-product-form.tpl');
        }
    }

    /**
     * Action after ADD MP products
     *
     * @param  array $params
     * @return void
     */
    public function hookActionAfterAddMPProduct($params)
    {
        $prod_type = (int) Tools::getValue('product_type');
        if ($prod_type == 2) {
            $mp_id_product = $params['id_mp_product'];

            $obj_mppack = new WkMpPackProduct();
            $obj_mppack->isPackProductFieldUpdate($mp_id_product, 1);

            $pspk_products = Tools::getValue('pspk_id_prod');
            $pspk_id_prod_attr = Tools::getValue('pspk_id_prod_attr');
            $pspk_prod_quant = Tools::getValue('pspk_prod_quant');
            if (count($pspk_products) == count($pspk_prod_quant)) {
                foreach ($pspk_products as $key => $value) {
                    $mp_prod_dtls = WkMpSellerProduct::getSellerProductByPsIdProduct($value);
                    $obj_mppack = new WkMpPackProduct();
                    $obj_mppack->new_mp_product_id = $mp_id_product;
                    $obj_mppack->mp_product_id = $mp_prod_dtls['id_mp_product'];
                    if (Module::isInstalled('mpcombination') && Module::isEnabled('mpcombination')) {
                        $mp_id_prod_attr = $obj_mppack->getMpAttributeIdByPsAttributeId($pspk_id_prod_attr[$key]);
                    } else {
                        $mp_id_prod_attr = 0;
                    }
                    $obj_mppack->mp_product_id_attribute = $mp_id_prod_attr;
                    $obj_mppack->quantity = $pspk_prod_quant[$key];
                    $obj_mppack->save();
                }

                $objSellerProduct = new WkMpSellerProduct($mp_id_product);
                if ($objSellerProduct->id_ps_product) {
                    $obj_mppack = new WkMpPackProduct();
                    $obj_mppack->addToPsPack($mp_id_product, $objSellerProduct->id_ps_product);
                }
            }
        }
    }

    /**
     * Action before ADD MP products
     *
     * @return void
     */
    public function hookActionBeforeAddMPProduct()
    {
        $prod_type = (int) Tools::getValue('product_type');
        if ($prod_type == 2) {
            $pk_products = Tools::getValue('pspk_id_prod');
            $pk_prod_quant = Tools::getValue('pspk_prod_quant');

            if (empty($pk_products) || empty($pk_prod_quant)) {
                $this->context->controller->errors[] = Tools::displayError('This pack is empty. You must add at least one product item.');
            } elseif (count($pk_products) != count($pk_prod_quant)) {
                $this->context->controller->errors[] = Tools::displayError('There is some internal error while creating pack product, please try again.');
            } else {
                foreach ($pk_prod_quant as $value) {
                    if (!Validate::isInt($value) || $value <= 0) {
                        $this->context->controller->errors[] = Tools::displayError('Please enter product quantity greater than or equal to 1.');
                        break;
                    }
                }
            }
        }
    }

    /**
     * Action after UPDATE MP products
     *
     * @param  array $params
     * @return void
     */
    public function hookActionAfterUpdateMPProduct($params)
    {
        $prod_type = (int) Tools::getValue('product_type');
        $mp_id_prod = $params['id_mp_product'];

        $obj_mppack = new WkMpPackProduct();
        $isPackProduct = $obj_mppack->isPackProduct($mp_id_prod);
        $mpSellerProduct = new WkMpSellerProduct($mp_id_prod);
        $ps_id_prod = $mpSellerProduct->id_ps_product;
        if ($prod_type == 1) {
            if ($isPackProduct) {
                //pack product to standard product
                $obj_mppack->isPackProductFieldUpdate($mp_id_prod, 0);
                $obj_mppack->deletePrevMpPackedProduct($mp_id_prod);

                if ($ps_id_prod) {
                    Pack::deleteItems($ps_id_prod);
                }
            }
        } elseif ($prod_type == 2) {
            $this->dataProcessIfProductIsPack($mp_id_prod, $isPackProduct);
        }
    }

    /**
     * Action before UPDATE MP products
     *
     * @return void
     */
    public function hookActionBeforeUpdateMPProduct()
    {
        $prod_type = (int) Tools::getValue('product_type');
        if ($prod_type == 2) {
            $pk_products = Tools::getValue('pspk_id_prod');
            $pk_prod_quant = Tools::getValue('pspk_prod_quant');

            $mp_id_prod = Tools::getValue('id');
            $combi_exist = WkMpPackProduct::checkProductCombination($mp_id_prod, $this->context->language->id);
            if ($combi_exist) {
                $this->context->controller->errors[] = Tools::displayError($this->l('This product has combination. You cannot choose pack product type.'));
            }

            if (empty($pk_products) || empty($pk_prod_quant)) {
                $this->context->controller->errors[] = Tools::displayError('This pack is empty. You must add at least one product item.');
            } elseif (count($pk_products) != count($pk_prod_quant)) {
                $this->context->controller->errors[] = Tools::displayError('There is some internal error while creating pack product, please try again.');
            } else {
                foreach ($pk_prod_quant as $value) {
                    if (!Validate::isInt($value) || $value <= 0) {
                        $this->context->controller->errors[] = Tools::displayError('Please enter product quantity greater than or equal to 1.');
                        break;
                    }
                }
            }
        }
    }

    /**
     * Action after delete MP product
     *
     * @param  array $params
     * @return void
     */
    public function hookActionMpProductDelete($params)
    {
        $obj_mppack = new WkMpPackProduct();
        $obj_mppack->deleteMpPack($params['id_mp_product']);
    }

    /**
     * Display Pack Details on MP product details page in bottom
     *
     * @return html
     */
    public function hookDisplayMpProductDetailsFooter()
    {
        $mp_id_prod = Tools::getValue('id_mp_product');
        $obj_mppack = new WkMpPackProduct();
        $isPackProduct = $obj_mppack->isPackProduct($mp_id_prod);
        if ($isPackProduct) {
            $packProducts = $obj_mppack->getPackedProducts($mp_id_prod);
            foreach ($packProducts as $key => $value) {
                $mpSellerProduct = new WkMpSellerProduct($value['mp_product_id']);
                $ps_id_prod = $mpSellerProduct->id_ps_product;
                if ($ps_id_prod) {
                    $product_obj = new Product($ps_id_prod, false, $this->context->language->id);
                    $packProducts[$key]['link_rewrite'] = $product_obj->link_rewrite;
                    $ps_prod_attr_id = $obj_mppack->getPsAttributeIdByMpAttributeId($value['mp_product_id_attribute']);
                    $packProducts[$key]['img_id'] = $this->getProductImageIdInPack($ps_id_prod, $ps_prod_attr_id);
                    $packProducts[$key]['id_ps_product'] = $ps_id_prod;
                    $packProducts[$key]['ps_prod_attr_id'] = $ps_prod_attr_id;
                }

                if (isset($ps_prod_attr_id) && $ps_prod_attr_id) {
                    $packProducts[$key]['product_name'] = Product::getProductName($ps_id_prod, $ps_prod_attr_id);
                } else {
                    $prod_name = $mpSellerProduct->product_name;
                    $packProducts[$key]['product_name'] = $prod_name[$this->context->language->id];
                }
            }

            $this->context->smarty->assign(array(
                'isPackProduct' => $isPackProduct,
                'packProducts' => $packProducts,
            ));

            $obj_seller_prod = new WkMpSellerProduct($mp_id_prod);
            if ($obj_seller_prod->id_seller) {
                return $this->display(__FILE__, 'mpproduct-details-page.tpl');
            } else {
                return $this->fetch('module:mppackproducts/views/templates/hook/mpproduct-details-page.tpl');
            }
        }
    }

    public function customizedAllPactProducsArray($pked_prods)
    {
        if ($pked_prods) {
            $obj_mppack = new WkMpPackProduct();
            foreach ($pked_prods as $key => $value) {
                $obj_seller_prod = new WkMpSellerProduct($value['mp_product_id']);
                $id_ps_product = $obj_seller_prod->id_ps_product;
                if ($id_ps_product) {
                    $product_obj = new Product($id_ps_product, false, $this->context->language->id);
                    $pked_prods[$key]['link_rewrite'] = $product_obj->link_rewrite;
                    $ps_prod_attr_id = $obj_mppack->getPsAttributeIdByMpAttributeId($value['mp_product_id_attribute']);
                    $pked_prods[$key]['img_id'] = $this->getProductImageIdInPack($id_ps_product, $ps_prod_attr_id);
                    $pked_prods[$key]['id_ps_product'] = $id_ps_product;
                    $pked_prods[$key]['ps_prod_attr_id'] = $ps_prod_attr_id;
                }
                if (isset($ps_prod_attr_id) && $ps_prod_attr_id && $obj_mppack->isPsCombinationExists($ps_prod_attr_id)) {
                    $pked_prods[$key]['product_name'] = Product::getProductName($id_ps_product, $ps_prod_attr_id);
                } else {
                    $prod_name = $obj_seller_prod->product_name;
                    $pked_prods[$key]['product_name'] = $prod_name[$this->context->language->id];
                }
            }

            return $pked_prods;
        }

        return array();
    }

    public function dataProcessIfProductIsPack($mp_id_prod, $isPackProduct)
    {
        $pspk_products = Tools::getValue('pspk_id_prod');
        $pspk_prod_quant = Tools::getValue('pspk_prod_quant');
        $pspk_id_prod_attr = Tools::getValue('pspk_id_prod_attr');
        $mpSellerProduct = new WkMpSellerProduct($mp_id_prod);
        $ps_id_prod = $mpSellerProduct->id_ps_product;
        if (Module::isInstalled('mpvirtualproduct') && Module::isEnabled('mpvirtualproduct')) {
            $this->dataProcessIfVirtualProductInstalled($mp_id_prod);
        }
        if (count($pspk_products) == count($pspk_prod_quant)) {
            $obj_mppack = new WkMpPackProduct();
            if (!$isPackProduct) {
                // Standard product to pack product
                $obj_mppack->isPackProductFieldUpdate($mp_id_prod, 1);
            } else {
                // Update pack product
                $obj_mppack->deletePrevMpPackedProduct($mp_id_prod);

                if ($ps_id_prod) {
                    Pack::deleteItems($ps_id_prod);
                }
            }
            $obj_mppack = new WkMpPackProduct();
            foreach ($pspk_products as $key => $value) {
                $mp_prod_dtls = WkMpSellerProduct::getSellerProductByPsIdProduct($value);
                $id_prod_attr = $pspk_id_prod_attr[$key];
                $mp_id_prod_attr = $this->getMpProductAttrID($id_prod_attr, $value);
                $params = array(
                    'for' => 'mp',
                    'pack_product_id' => $mp_id_prod,
                    'item_product_id' => $mp_prod_dtls['id_mp_product'],
                    'item_product_id_attribute' => $mp_id_prod_attr,
                    'quantity' => $pspk_prod_quant[$key]
                );
                $is_duplicate = $obj_mppack->checkIfDuplicateEntry($params);

                if (!$is_duplicate) {
                    $this->saveMpPackProductData($params);
                }
            }
            if ($ps_id_prod) {
                $obj_mppack->addToPsPack($mp_id_prod, $ps_id_prod);
            }
        }
    }

    public function saveMpPackProductData($params)
    {
        $obj_mppack = new WkMpPackProduct();
        $obj_mppack->new_mp_product_id = $params['pack_product_id'];
        $obj_mppack->mp_product_id = $params['item_product_id'];
        $obj_mppack->mp_product_id_attribute = $params['item_product_id_attribute'];
        $obj_mppack->quantity = $params['quantity'];
        $obj_mppack->save();
    }

    public function getMpProductAttrID($id_ps_prod_attr, $id_product)
    {
        if (Module::isInstalled('mpcombination') && Module::isEnabled('mpcombination')) {
            $obj_mppack = new WkMpPackProduct();
            if (!$id_ps_prod_attr) {
                $id_ps_prod_attr = (int) $id_ps_prod_attr ? (int) $id_ps_prod_attr : Product::getDefaultAttribute((int) $id_product);
            }
            $mp_id_prod_attr = $obj_mppack->getMpAttributeIdByPsAttributeId($id_ps_prod_attr);
        } else {
            $mp_id_prod_attr = 0;
        }

        return $mp_id_prod_attr;
    }

    /**
     * Process when virtual product module installed
     *
     * @param  int $mp_id_prod
     * @return void
     */
    public function dataProcessIfVirtualProductInstalled($mp_id_prod)
    {
        $obj_mvp = new MarketplaceVirtualProduct();
        $is_virtual_product = $obj_mvp->isMpProductIsVirtualProduct($mp_id_prod);
        if ($is_virtual_product) {
            // virtual product to standard product
            $mpSellerProduct = new WkMpSellerProduct($mp_id_prod);
            $ps_id_prod = $mpSellerProduct->id_ps_product;
            if ($ps_id_prod) {
                $product = new Product($ps_id_prod);
                $product->is_virtual = 0;
                $product->save();

                $id_prod_download = ProductDownload::getIdFromIdProduct($ps_id_prod);
                $download = new ProductDownload($id_prod_download);

                if (trim($download->filename)) {
                    if (file_exists(_PS_DOWNLOAD_DIR_.$download->filename)) {
                        unlink(_PS_DOWNLOAD_DIR_.$download->filename);
                    }
                }

                $obj_mvp->deleteProdDownloadByIdProductDownload($id_prod_download); //row delete from product download table
            } else {
                if ($is_virtual_product['reference_file']) {
                    $file_link = _PS_MODULE_DIR_.$this->name.'/upload/'.$is_virtual_product['reference_file'];
                    if (file_exists($file_link)) {
                        unlink($file_link);
                    }
                }
            }
            $obj_mvp->deleteVirtualProductById($is_virtual_product['id']);
        }
    }

    public function getProductImageIdInPack($id_product, $id_product_attribute)
    {
        $id_image = 0;
        if ($id_product_attribute) {
            $id_image_arr = Product::getCombinationImageById($id_product_attribute, (int) $this->context->cookie->id_lang);
            $id_image = $id_image_arr['id_image'];
        } else {
            $cover = Product::getCover($id_product);
            $id_image = $cover['id_image'];
        }

        return $id_image;
    }

    public function alterTable($table_name_without_prefix, $action)
    {
        if ($action == 'add') {
            $add = Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.$table_name_without_prefix." ADD COLUMN is_pack_product int(10) NOT NULL DEFAULT '0'");
            if (!$add) {
                return false;
            }

            return true;
        } elseif ($action == 'drop') {
            $drop = Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.$table_name_without_prefix.' DROP COLUMN is_pack_product');
            if (!$drop) {
                return false;
            }

            return true;
        }
    }

    protected function createTables()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        return true;
    }

    public function registerModuleHook()
    {
        return $this->registerHook(array(
            'displayMpAddProductHeader', 'displayMpAddProductContentTop',
            'displayMpUpdateProductNameBottom', 'displayMpUpdateProductContentTop',
            'displayMpAddProductNameBottom', 'displayMpProductDetailsFooter',
            'displayMpPackProductOption', 'actionAfterAddMPProduct',
            'actionAfterUpdateMPProduct', 'actionMpProductDelete',
            'actionBeforeUpdateMPProduct', 'actionBeforeAddMPProduct',
            'actionToogleMPProductCreateStatus', 'displayHeader',
            'displayBackOfficeHeader', 'actionFrontControllerSetMedia',
            'actionAdminControllerSetMedia',
        ));
    }

    public function install()
    {
        if (Module::isInstalled('mpvirtualproduct')) {
            $idModule = Module::getModuleIdByName('mpvirtualproduct');
            $module = Module::getInstanceById((int) $idModule);
            $module->unregisterHook('displayMpAddProductContentTop');
            $module->unregisterHook('displayMpUpdateProductContentTop');
            $module->unregisterHook('displayMpAddProductHeader');
        }

        if (!$this->createTables()
            || !$this->alterTable('wk_mp_seller_product', 'add')
            || !parent::install()
            || !$this->registerModuleHook()
            ) {
            return false;
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_mp_pack_product`'
        );
    }

    public function uninstall()
    {
        if (Module::isInstalled('mpvirtualproduct')) {
            $id_module = Module::getModuleIdByName('mpvirtualproduct');
            $module = Module::getInstanceById((int) $id_module);
            $module->registerHook('displayMpAddProductContentTop');
            $module->registerHook('displayMpUpdateProductContentTop');
            $module->registerHook('displayMpAddProductHeader');
        }

        if (!parent::uninstall()
            || !WkMpPackProduct::changePsProductsType(1)
            || !$this->alterTable('wk_mp_seller_product', 'drop')
            || !$this->deleteTables()
            ) {
            return false;
        }

        return true;
    }

    public function disable($force_all = false)
    {
        if (Module::isEnabled('mpvirtualproduct')) {
            $id_module = Module::getModuleIdByName('mpvirtualproduct');
            $module = Module::getInstanceById((int) $id_module);
            $module->registerHook('displayMpAddProductContentTop');
            $module->registerHook('displayMpUpdateProductContentTop');
            $module->registerHook('displayMpAddProductHeader');

            $this->unregisterHook('displayMpAddProductContentTop');
            $this->unregisterHook('displayMpUpdateProductContentTop');
            $this->unregisterHook('displayMpAddProductHeader');
        }
        if (Module::isInstalled('mppackproducts')) {
            if (!WkMpPackProduct::changePackProductsStatus(0)) {
                return false;
            }
        }

        return parent::disable();
    }

    public function enable($force_all = false)
    {
        if (Module::isEnabled('mpvirtualproduct')) {
            $id_module = Module::getModuleIdByName('mpvirtualproduct');
            $module = Module::getInstanceById((int) $id_module);
            $module->unregisterHook('displayMpAddProductContentTop');
            $module->unregisterHook('displayMpUpdateProductContentTop');
            $module->unregisterHook('displayMpAddProductHeader');

            $this->registerHook('displayMpAddProductContentTop');
            $this->registerHook('displayMpUpdateProductContentTop');
            $this->registerHook('displayMpAddProductHeader');
        }
        if (!Module::isInstalled('mppackproducts')) {
            if (!WkMpPackProduct::changePackProductsStatus(1)) {
                return false;
            }
        }

        return parent::enable();
    }
}
