<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


require_once dirname(__FILE__) . '/tree/Tree.php';

class InixHelper2TreeCategories extends Inix2Tree
{
    const DEFAULT_TEMPLATE = 'tree_categories.tpl';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item_radio.tpl';

    private $_disabled_categories;
    private $_input_name;
    private $_lang;
    private $_root_category;
    private $_selected_categories;
    private $_shop;
    private $_use_checkbox;
    private $_use_search;
    private $_use_shop_restriction;

    public function __construct(
        $id,
        $title = null,
        $root_category = null,
        $lang = null,
        $use_shop_restriction = true
    ) {
        parent::__construct($id);

        if (isset($title)) {
            $this->setTitle($title);
        }

        if (isset($root_category)) {
            $this->setRootCategory($root_category);
        }

        $this->setLang($lang);
        $this->setUseShopRestriction($use_shop_restriction);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(self::getNestedCategories(
                $this->getRootCategory(),
                $this->getLang(),
                false,
                null,
                $this->useShopRestriction()
            ));
        }

        return $this->_data;
    }


    public static function getNestedCategories(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }

        $cache_id = 'Category::getNestedCategories_' . md5((int) $root_category . (int) $id_lang . (int) $active . (int) $active
                                                           . (isset($groups) && Group::isFeatureActive() ? implode(
                                                               '',
                                                               $groups
                                                           ) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
				SELECT c.*, cl.*
				FROM `' . _DB_PREFIX_ . 'category` c
				' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int) $root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '
				' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(
    ',',
    $groups
) . ')' : '') . '
				' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
				' . ($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
				' . ($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '') . '
				' . ($sql_limit != '' ? $sql_limit : ''));

            $categories = array();
            $buff       = array();

            if (!isset($root_category)) {
                $root_category = 1;
            }

            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $root_category) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        }

        return Cache::retrieve($cache_id);
    }

    public function setDisabledCategories($value)
    {
        $this->_disabled_categories = $value;

        return $this;
    }

    public function getDisabledCategories()
    {
        return $this->_disabled_categories;
    }

    public function setInputName($value)
    {
        $this->_input_name = $value;

        return $this;
    }

    public function getInputName()
    {
        if (!isset($this->_input_name)) {
            $this->setInputName('categoryBox');
        }

        return $this->_input_name;
    }

    public function setLang($value)
    {
        $this->_lang = $value;

        return $this;
    }

    public function getLang()
    {
        if (!isset($this->_lang)) {
            $this->setLang($this->getContext()->employee->id_lang);
        }

        return $this->_lang;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
    }

    public function setRootCategory($value)
    {
        if (!Validate::isInt($value)) {
            throw new PrestaShopException('Root category must be an integer value');
        }

        $this->_root_category = $value;

        return $this;
    }

    public function getRootCategory()
    {
        return $this->_root_category;
    }

    public function setSelectedCategories($value)
    {
        if (!is_array($value)) {
            throw new PrestaShopException('Selected categories value must be an array');
        }

        $this->_selected_categories = $value;

        return $this;
    }

    public function getSelectedCategories()
    {
        if (!isset($this->_selected_categories)) {
            $this->_selected_categories = array();
        }

        return $this->_selected_categories;
    }

    public function setShop($value)
    {
        $this->_shop = $value;

        return $this;
    }

    public function getShop()
    {
        if (!isset($this->_shop)) {
            if (Tools::isSubmit('id_shop')) {
                $this->setShop(new Shop(Tools::getValue('id_shop')));
            } elseif ($this->getContext()->shop->id) {
                $this->setShop(new Shop($this->getContext()->shop->id));
            } elseif (!Shop::isFeatureActive()) {
                $this->setShop(new Shop(Configuration::get('PS_SHOP_DEFAULT')));
            } else {
                $this->setShop(new Shop(0));
            }
        }

        return $this->_shop;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    public function setUseCheckBox($value)
    {
        $this->_use_checkbox = (bool) $value;

        return $this;
    }

    public function setUseSearch($value)
    {
        $this->_use_search = (bool) $value;

        return $this;
    }

    public function setUseShopRestriction($value)
    {
        $this->_use_shop_restriction = (bool) $value;

        return $this;
    }

    public function useCheckBox()
    {
        return (isset($this->_use_checkbox) && $this->_use_checkbox);
    }

    public function useSearch()
    {
        return (isset($this->_use_search) && $this->_use_search);
    }

    public function useShopRestriction()
    {
        return (isset($this->_use_shop_restriction) && $this->_use_shop_restriction);
    }

    public function render($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (isset($this->_disabled_categories)
            && !empty($this->_disabled_categories)
        ) {
            $this->_disableCategories($data, $this->getDisabledCategories());
        }

        if (isset($this->_selected_categories)
            && !empty($this->_selected_categories)
        ) {
            $this->_getSelectedChildNumbers($data, $this->getSelectedCategories());
        }

        //Default bootstrap style of search is push-right, so we add this button first
        if ($this->useSearch()) {
            $this->getContext()->controller->addJS($this->getModule()
                                                        ->getFramePathUri() . 'js/vendor/typeahead.min.js');
            $search_categories = new Inix2TreeToolbarSearchCategories(
                'Find a category:',
                $this->getId() . '-categories-search'
            );
            $search_categories->setModule($this->getModule());
            $this->addAction($search_categories);
            $this->setAttribute('use_search', $this->useSearch());
        }

        $collapse_all = new Inix2TreeToolbarLink(
            'Collapse All',
            '#',
            '$(\'#' . $this->getId() . '\').tree(\'collapseAll\');$(\'#collapse-all-' . $this->getId() . '\').hide();$(\'#expand-all-' . $this->getId() . '\').show(); return false;',
            'icon-collapse-alt'
        );
        $collapse_all->setAttribute('id', 'collapse-all-' . $this->getId())->setModule($this->getModule());
        $expand_all = new Inix2TreeToolbarLink(
            'Expand All',
            '#',
            '$(\'#' . $this->getId() . '\').tree(\'expandAll\');$(\'#collapse-all-' . $this->getId() . '\').show();$(\'#expand-all-' . $this->getId() . '\').hide(); return false;',
            'icon-expand-alt'
        );
        $expand_all->setAttribute('id', 'expand-all-' . $this->getId())->setModule($this->getModule());
        $this->addAction($collapse_all);
        $this->addAction($expand_all);

        if ($this->useCheckBox()) {
            $check_all = new Inix2TreeToolbarLink(
                'Check All',
                '#',
                'checkAllAssociatedCategories($(\'#' . $this->getId() . '\')); return false;',
                'icon-check-sign'
            );
            $check_all->setAttribute('id', 'check-all-' . $this->getId())->setModule($this->getModule());
            $uncheck_all = new Inix2TreeToolbarLink(
                'Uncheck All',
                '#',
                'uncheckAllAssociatedCategories($(\'#' . $this->getId() . '\')); return false;',
                'icon-check-empty'
            );
            $uncheck_all->setAttribute('id', 'uncheck-all-' . $this->getId())->setModule($this->getModule());
            $this->addAction($check_all);
            $this->addAction($uncheck_all);
            $this->setNodeFolderTemplate('tree_node_folder_checkbox.tpl');
            $this->setNodeItemTemplate('tree_node_item_checkbox.tpl');
            $this->setAttribute('use_checkbox', $this->useCheckBox());
        }

        $this->setAttribute('selected_categories', $this->getSelectedCategories());
        $this->getContext()->smarty->assign('root_category', Configuration::get('PS_ROOT_CATEGORY'));

        return parent::render($data);
    }

    //Override
    public function renderNodes($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (!is_array($data) && !$data instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $html = '';
        foreach ($data as $item) {
            if (array_key_exists('children', $item)
                && !empty($item['children'])
            ) {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => $this->getInputName(),
                    'children'   => $this->renderNodes($item['children']),
                    'node'       => $item
                ))->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => $this->getInputName(),
                    'node'       => $item
                ))->fetch();
            }
        }

        return $html;
    }

    private function _disableCategories(&$categories, $disabled_categories = null)
    {
        foreach ($categories as &$category) {
            if (!isset($disabled_categories) || in_array($category['id_category'], $disabled_categories)) {
                $category['disabled'] = true;
                if (array_key_exists('children', $category) && is_array($category['children'])) {
                    self::_disableCategories($category['children'], $disabled_categories);
                }
            } elseif (array_key_exists('children', $category) && is_array($category['children'])) {
                self::_disableCategories($category['children'], $disabled_categories);
            }
        }
    }

    private function _getSelectedChildNumbers(&$categories, $selected, &$parent = null)
    {
        $selected_childs = 0;

        foreach ($categories as $key => &$category) {
            if (isset($parent) && in_array($category['id_category'], $selected)) {
                $selected_childs ++;
            }

            if (isset($category['children']) && !empty($category['children'])) {
                $selected_childs += $this->_getSelectedChildNumbers($category['children'], $selected, $category);
            }
        }

        if (!isset($parent['selected_childs'])) {
            $parent['selected_childs'] = 0;
        }

        $parent['selected_childs'] = $selected_childs;

        return $selected_childs;
    }
}
