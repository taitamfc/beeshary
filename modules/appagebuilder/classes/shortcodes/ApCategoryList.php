<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class ApCategoryList extends ApShortCodeBase
{
    public $name = 'ApCategoryList';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Seller Category List'),
            'position' => 5,
            'desc' => $this->l('Seller Category List'),
            'icon_class' => 'icon-info-sign',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
                'default' => ''
            )
        );
        return $inputs;
    }

    public function prepareFontContent($assign, $module = null)
    {   
        $this->context = Context::getContext();
        $lang = $this->context->language->id;
        $category = new Category((int)Configuration::get('PS_HOME_CATEGORY'), $lang);
		
		$idCustomer = $this->context->customer->id;
        $shopLinkRewrite = Tools::getValue('mp_shop_name');
		
		if( $shopLinkRewrite ){
			$mpSeller = WkMpSeller::getSellerByLinkRewrite($shopLinkRewrite, $this->context->language->id);
			if ($mpSeller) {
				$idSeller = $mpSeller['id_seller'];
				$seller_category_ids = $this->getSellerCategories($idSeller,[13]);
			}
		}else{
			$seller_category_ids = $this->getSellerCategories(0);
		}
        // validate module
        unset($module);
        $form_atts = $assign['formAtts'];
		
		
		
		if( $seller_category_ids ){
			$seller_category_ids = explode(',',$seller_category_ids);
		}else{
			$seller_category_ids = [];
		}
        $assign['seller_categories'] = [];
		if( $seller_category_ids && count($seller_category_ids) ){
			foreach ($seller_category_ids as $key => $id_cat)
			{
				
				$category = new Category($id_cat, $lang);

				$assign['seller_categories'][$id_cat] = $category;
			}
		}
        $assign['link'] = $this->context->link;
        return $assign;
    }

   public function getSellerCategories($id_seller, $exl_cats = array(), $as_group = true)
    {
        $sql = 'SELECT '.($as_group ? 'GROUP_CONCAT(`id_category`) as seller_cats' : '*').'
            FROM `'._DB_PREFIX_.'wk_mp_seller_product`
            WHERE `id_seller` = '.(int)$id_seller
            .(count($exl_cats) && is_array($exl_cats) ? ' AND id_category <> '. implode(',', array_map('intval', $exl_cats)) : '');
        if ($as_group)
            return Db::getInstance()->getValue($sql);
        return Db::getInstance()->executeS($sql);
    }
}
