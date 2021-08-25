<?php

class BlockCategoriesi extends Module
{
	function __construct()
	{
		$this->name = 'blockcategoriesi';
		if(_PS_VERSION_ > "1.4.0.0"){
		$this->tab = 'front_office_features';
		$this->author = 'RSI';
		$this->need_instance = 1;
		}else
		{
		$this->tab = 'Blocks';
		}
		$this->version = 2.2;

		parent::__construct();

		$this->displayName = $this->l('Categories block image');
		$this->description = $this->l('Adds a block featuring product categories - www.catalogo-onlienrsi.com.ar');
	}

	function install()
	{
		if(_PS_VERSION_ < "1.4.0.0"){
			if (parent::install() == false
			OR $this->registerHook('leftColumn') == false
			OR Configuration::updateValue('BLOCK_CATEGI_MAX_DEPTH', 3) == false
			OR Configuration::updateValue('BLOCK_CATEGI_DHTML', 1) == false
			OR Configuration::updateValue('BLOCK_CATEGI_IMAGE', 40) == false)
			return false;
			
		return true;
			
			}
			else{
		if (!parent::install() OR
			!$this->registerHook('leftColumn') OR
			!$this->registerHook('header') OR
			// Temporary hooks. Do NOT hook any module on it. Some CRUD hook will replace them as soon as possible.
			!$this->registerHook('categoryAddition') OR
			!$this->registerHook('categoryUpdate') OR
			!$this->registerHook('categoryDeletion') OR
			!Configuration::updateValue('BLOCK_CATEGI_MAX_DEPTH', 3) OR
			!Configuration::updateValue('BLOCK_CATEGI_DHTML', 1)OR
			!Configuration::updateValue('BLOCK_CATEGI_IMAGE', 40))
			return false;	
		return true;
			}
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockCategoriesi'))
		{
			$maxDepth = intval(Tools::getValue('maxDepth'));
			$dhtml = Tools::getValue('dhtml');
			$image = Tools::getValue('image');
			if ($maxDepth < 0)
				$output .= '<div class="alert error">'.$this->l('Maximum depth: Invalid number.').'</div>';
			elseif ($dhtml != 0 AND $dhtml != 1)
				$output .= '<div class="alert error">'.$this->l('Dynamic HTML: Invalid choice.').'</div>';
			else
			{
				Configuration::updateValue('BLOCK_CATEGI_MAX_DEPTH', intval($maxDepth));
				Configuration::updateValue('BLOCK_CATEGI_DHTML', intval($dhtml));
				Configuration::updateValue('BLOCK_CATEGI_IMAGE', $image);
				$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Maximum depth').'</label>
				<div class="margin-form">
					<input type="text" name="maxDepth" value="'.Configuration::get('BLOCK_CATEGI_MAX_DEPTH').'" />
					<p class="clear">'.$this->l('Set the maximum depth of sublevels displayed in this block (0 = infinite)').'</p>
				</div>
				
				<label>'.$this->l('Image size').'</label>
				<div class="margin-form">
					<input type="text" name="image" value="'.Configuration::get('BLOCK_CATEGI_IMAGE').'" />
				
				</div>
				<label>'.$this->l('Dynamic').'</label>

				<div class="margin-form">
					<input type="radio" name="dhtml" id="dhtml_on" value="1" '.(Tools::getValue('dhtml', Configuration::get('BLOCK_CATEGI_DHTML')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="dhtml_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="dhtml" id="dhtml_off" value="0" '.(!Tools::getValue('dhtml', Configuration::get('BLOCK_CATEGI_DHTML')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="dhtml_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Activate dynamic (animated) mode for sublevels').'</p>
				</div>
					<center><a href="../modules/blockcategoriesi/moduleinstall.pdf">README</a></center><br/>
					<center><a href="../modules/blockcategoriesi/termsandconditions.pdf">TERMS</a></center><br/>
				<center><input type="submit" name="submitBlockCategoriesi" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Contribute').'</legend>
				<p class="clear">'.$this->l('You can contribute with a donation if our free modules and themes are usefull for you. Clic on the link and support us!').'</p>
				<p class="clear">'.$this->l('For more modules & themes visit: www.catalogo-onlinersi.com.ar').'</p>
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="HMBZNQAHN9UMJ">
<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
	</fieldset>
</form>';
	}
	public function getTree2($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
	
			
		
					
					
							if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();

		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree2($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (!isset($resultIds[$id_category]))
			return false;
		$return = array('id' => $id_category, 'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
					 'name' => $resultIds[$id_category]['name'], 'desc'=> $resultIds[$id_category]['description'],
					 'children' => $children);
		return $return;
						
	}
	public function getTree($resultParents, $resultIds, $maxDepth, $id_category = 1, $currentDepth = 0)
	{
		if ( _PS_VERSION_ < "1.4.0.0"){
						global $link;
		
		$children = array();
		if (isset($resultParents[$id_category]) AND sizeof($resultParents[$id_category]) AND ($maxDepth == 0 OR $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (!isset($resultIds[$id_category]))
			return false;
		return array('id' => $id_category, 'link' => $link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
					 'name' => Category::hideCategoryPosition($resultIds[$id_category]['name']), 'desc'=> $resultIds[$id_category]['description'],
					 'children' => $children);
				}
				if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0"){
		global $link;

		$children = array();
		if (isset($resultParents[$id_category]) AND sizeof($resultParents[$id_category]) AND ($maxDepth == 0 OR $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (!isset($resultIds[$id_category]))
			return false;
		return array('id' => $id_category, 'link' => $link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
					 'name' => $resultIds[$id_category]['name'], 'desc'=> $resultIds[$id_category]['description'],
					 'children' => $children);
				}
	}

	function hookLeftColumn($params)
	{
		global $smarty, $cookie, $category;
		$psversion = _PS_VERSION_;
		if (_PS_VERSION_ < "1.4.0.0"){
		/*  ONLY FOR THEME OLDER THAN v1.0 */
		global $link;
		$image = Configuration::get('BLOCK_CATEGI_IMAGE');
		$smarty->assign(array(
			'categories' => Category::getHomeCategories(intval($params['cookie']->id_lang), true),
			'image' => 	$image,
			'link' => $link
		));
		/* ELSE */
		$id_customer = intval($params['cookie']->id_customer);
		$maxdepth = Configuration::get('BLOCK_CATEGI_MAX_DEPTH');
		
		if (!$result = Db::getInstance()->ExecuteS('
		SELECT DISTINCT c.*, cl.*
		FROM `'._DB_PREFIX_.'category` c 
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.intval($params['cookie']->id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'category_group` ctg ON (ctg.`id_category` = c.`id_category`)
		'.($id_customer ? 'INNER JOIN `'._DB_PREFIX_.'customer_group` cg ON (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.intval($id_customer).')' : '' ).'
		WHERE 1'
		.(intval($maxdepth) != 0 ? ' AND `level_depth` <= '.intval($maxdepth) : '').'
		AND (c.`active` = 1 OR c.`id_category`= 1)
		'.(!$id_customer ? 'AND ctg.`id_group` = 1' : '' ).'
		ORDER BY `level_depth` ASC, cl.`name` ASC'))
			return;
		$resultParents = array();
		$resultIds = array();

		foreach ($result as $row)
		{
			$$row['name'] = Category::hideCategoryPosition($row['name']);
			$resultParents[$row['id_parent']][] = $row;
			$resultIds[$row['id_category']] = $row;
		}
		$blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEGI_MAX_DEPTH'));
		$isDhtml = (Configuration::get('BLOCK_CATEGI_DHTML') == 1 ? true : false);

		if (isset($_GET['id_category']))
		{
			$cookie->last_visited_category = intval($_GET['id_category']);
			$smarty->assign('currentCategoryId', intval($_GET['id_category']));	
		}
		if (isset($_GET['id_product']))
		{			
			if (!isset($cookie->last_visited_category) OR !Product::idIsOnCategoryId(intval($_GET['id_product']), array('0' => array('id_category' => $cookie->last_visited_category))))
			{
				$product = new Product(intval($_GET['id_product']));
				if (isset($product) AND Validate::isLoadedObject($product))
					$cookie->last_visited_category = intval($product->id_category_default);
			}
			$smarty->assign('currentCategoryId', intval($cookie->last_visited_category));
		}	
		$smarty->assign('blockCategTree', $blockCategTree);
		
		if (file_exists(_PS_THEME_DIR_.'modules/blockcategoriesi/blockcategoriesi.tpl'))
			$smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategoriesi/category-tree-branch.tpl');
		else
			$smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategoriesi/category-tree-branch.tpl');
		$smarty->assign('isDhtml', $isDhtml);
		/* /ONLY FOR THEME OLDER THAN v1.0 */
		$smarty->assign('psversion', $psversion);
		return $this->display(__FILE__, 'blockcategoriesi.tpl');
		}
		
		/*ps 1.4*/
		if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0")
		{
			$image = Configuration::get('BLOCK_CATEGI_IMAGE');
				$images = 	$image - 10;
$id_customer = (int)($params['cookie']->id_customer);
		// Get all groups for this customer and concatenate them as a string: "1,2,3..."
		// It is necessary to keep the group query separate from the main select query because it is used for the cache
		$groups = $id_customer ? implode(', ', Customer::getGroupsStatic($id_customer)) : _PS_DEFAULT_CUSTOMER_GROUP_;
		$id_product = (int)(Tools::getValue('id_product', 0));
		$id_category = (int)(Tools::getValue('id_category', 0));
		$id_lang = (int)($params['cookie']->id_lang);
		$smartyCacheId = 'blockcategoriesi|'.$groups.'_'.$id_lang.'_'.$id_product.'_'.$id_category;

		$smarty->cache_lifetime = 31536000; // 1 Year
		Tools::enableCache();
		if (!$this->isCached('blockcategoriesi.tpl', $smartyCacheId))
		{
			$maxdepth = Configuration::get('BLOCK_CATEGI_MAX_DEPTH');
			if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
				WHERE (c.`active` = 1 OR c.`id_category` = 1)
				'.((int)($maxdepth) != 0 ? ' AND `level_depth` <= '.(int)($maxdepth) : '').'
				AND cg.`id_group` IN ('.pSQL($groups).')
				GROUP BY id_category
				ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'c.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'))
			)
				return;
				
			$resultParents = array();
			$resultIds = array();

			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}

			$blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEGI_MAX_DEPTH'));
			unset($resultParents);
			unset($resultIds);
			$isDhtml = (Configuration::get('BLOCK_CATEG_DHTML') == 1 ? true : false);

			if (Tools::isSubmit('id_category'))
			{
				$cookie->last_visited_category = $id_category;
				$smarty->assign('currentCategoryId', $cookie->last_visited_category);
			}
			if (Tools::isSubmit('id_product'))
			{
				if (!isset($cookie->last_visited_category) OR !Product::idIsOnCategoryId($id_product, array('0' => array('id_category' => $cookie->last_visited_category))))
				{
					$product = new Product($id_product);
					if (isset($product) AND Validate::isLoadedObject($product))
						$cookie->last_visited_category = (int)($product->id_category_default);
				}
				$smarty->assign('currentCategoryId', (int)($cookie->last_visited_category));
			}
			$smarty->assign('blockCategTree', $blockCategTree);
			
			if (file_exists(_PS_THEME_DIR_.'modules/blockcategoriesi/blockcategoriesi.tpl'))
				$smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategoriesi/category-tree-branch.tpl');
			else
				$smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategoriesi/category-tree-branch.tpl');
			$smarty->assign('isDhtml', $isDhtml);
		}
		$smarty->assign('image', $image);
		$smarty->assign('images', $images);
		$smarty->assign('psversion', $psversion);
		$smarty->cache_lifetime = 31536000; // 1 Year
	
		
		$display = $this->display(__FILE__, 'blockcategoriesi.tpl', $smartyCacheId);
		Tools::restoreCacheSettings();
		return $display;
			}
			/*ps1.5*/
			if(_PS_VERSION_ > "1.5.0.0")
			
			{
				$image = Configuration::get('BLOCK_CATEGI_IMAGE');
				$images = 	$image - 10;
			$id_customer = (int)$params['cookie']->id_customer;
		// Get all groups for this customer and concatenate them as a string: "1,2,3..."
		// It is necessary to keep the group query separate from the main select query because it is used for the cache
		$groups = $id_customer ? implode(', ', Customer::getGroupsStatic($id_customer)) : Configuration::get('PS_UNIDENTIFIED_GROUP');
		$id_product = (int)Tools::getValue('id_product', 0);
		$id_category = (int)Tools::getValue('id_category', 0);
		$id_lang = (int)$params['cookie']->id_lang;
		$smartyCacheId = 'blockcategoriesi|'.$this->context->shop->id.'_'.$groups.'_'.$id_lang.'_'.$id_product.'_'.$id_category;
		$this->context->smarty->cache_lifetime = 31536000; // 1 Year
		Tools::enableCache();
		if (!$this->isCached('blockcategoriesi.tpl', $smartyCacheId))
		{
			$maxdepth = Configuration::get('BLOCK_CATEGI_MAX_DEPTH');
			if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
				FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
				WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
				AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
				'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
				AND c.id_category IN (SELECT id_category FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` IN ('.pSQL($groups).'))
				ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC')))
				return Tools::restoreCacheSettings();

			$resultParents = array();
			$resultIds = array();

			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}

			$blockCategTree = $this->getTree2($resultParents, $resultIds, Configuration::get('BLOCK_CATEGI_MAX_DEPTH'));
			unset($resultParents, $resultIds);

			$isDhtml = (Configuration::get('BLOCK_CATEGI_DHTML') == 1 ? true : false);
			if (Tools::isSubmit('id_category'))
			{
				$this->context->cookie->last_visited_category = $id_category;
				$this->smarty->assign('currentCategoryId', $this->context->cookie->last_visited_category);
			}
			if (Tools::isSubmit('id_product'))
			{
				if (!isset($this->context->cookie->last_visited_category)
					|| !Product::idIsOnCategoryId($id_product, array('0' => array('id_category' => $this->context->cookie->last_visited_category)))
					|| !Category::inShopStatic($this->context->cookie->last_visited_category, $this->context->shop))
				{
					$product = new Product($id_product);
					if (isset($product) && Validate::isLoadedObject($product))
						$this->context->cookie->last_visited_category = (int)$product->id_category_default;
				}
				$this->smarty->assign('currentCategoryId', (int)$this->context->cookie->last_visited_category);
			}
			$this->smarty->assign('blockCategTree', $blockCategTree);

			if (file_exists(_PS_THEME_DIR_.'modules/blockcategoriesi/blockcategoriesi.tpl'))
				$this->smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategoriesi/category-tree-branch.tpl');
			else
				$this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategoriesi/category-tree-branch.tpl');
			$this->smarty->assign('isDhtml', $isDhtml);
		}
			$smarty->assign('image', $image);
		$smarty->assign('images', $images);
		$smarty->assign('psversion', $psversion);
		$smarty->cache_lifetime = 31536000; // 1 Year
		$display = $this->display(__FILE__, 'blockcategoriesi.tpl', $smartyCacheId);
		Tools::restoreCacheSettings();
		return $display;
			}
			
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	public function hookHeader()
	{
		if(_PS_VERSION_ < "1.5.0.0"){
		Tools::addJS(_THEME_JS_DIR_.'tools/treeManagement.js');
		Tools::addCSS(($this->_path).'blockcategories.css', 'all');
		Tools::addCSS(($this->_path).'blockcategoriesi.css', 'all');
		}
		else
		{
		$this->context->controller->addJS(_THEME_JS_DIR_.'tools/treeManagement.js');
		$this->context->controller->addCSS(($this->_path).'blockcategories.css', 'all');
		$this->context->controller->addCSS(($this->_path).'blockcategoriesi.css', 'all');
		}
	}

	private function _clearBlockcategoriesCache()
	{
		if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0")
		{
				$this->_clearCache('blockcategoriesi.tpl');
		$this->_clearCache('blockcategories_footer.tpl');		
		Tools::restoreCacheSettings();	
		}
		if(_PS_VERSION_ < "1.4.0.0")
		{
		$this->_clearCache(NULL, 'blockcategoriesi');
		Tools::restoreCacheSettings();
		}
		if(_PS_VERSION_ > "1.5.0.0")
		{
		$this->_clearCache('blockcategoriesi.tpl');
		}
	}

	public function hookCategoryAddition($params)
	{
		$this->_clearBlockcategoriesCache();
	}

	public function hookCategoryUpdate($params)
	{
		$this->_clearBlockcategoriesCache();
	}

	public function hookCategoryDeletion($params)
	{
		$this->_clearBlockcategoriesCache();
	}
		public function hookActionAdminMetaControllerUpdate_optionsBefore($params)
	{
		$this->_clearBlockcategoriesCache();
	}
}

?>
