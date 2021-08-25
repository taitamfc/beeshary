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

class ApBlockLink extends ApShortCodeBase
{
    public $name = 'ApBlockLink';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Block Link'),
            'position' => 5,
            'desc' => $this->l('Create List Block Links'),
            'icon_class' => 'icon icon-list',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        $types = array(
            array(
                'value' => 'link-url',
                'text' => $this->l('URL')
            ),
            array(
                'value' => 'link-category',
                'text' => $this->l('Category')
            ),
            array(
                'value' => 'link-product',
                'text' => $this->l('Product')
            ),
            array(
                'value' => 'link-manufacture',
                'text' => $this->l('Manufacture')
            ),
            array(
                'value' => 'link-supplier',
                'text' => $this->l('Supplier')
            ),
            array(
                'value' => 'link-cms',
                'text' => $this->l('CMS')
            ),
            array(
                'value' => 'link-page',
                'text' => $this->l('Page Controller')
            ),
        );

        $target = array(
            array(
                'value' => '_blank',
                'text' => $this->l('Blank ( New Tab)')
            ),
            array(
                'value' => '_self',
                'text' => $this->l('Self ( Same Tab)')
            ),
            array(
                'value' => '_parent',
                'text' => $this->l('Parent')
            ),
            array(
                'value' => '_top',
                'text' => $this->l('Top')
            ),
        );
        $accordion_type = array(
            array(
                'value' => 'full',
                'text' => $this->l('Always Full')
            ),
            array(
                'value' => 'accordion',
                'text' => $this->l('Always Accordion')
            ),
            array(
                'value' => 'accordion_small_screen',
                'text' => $this->l('Accordion at small screen')
            ),
        );
        $page_controller = array();
        foreach (Meta::getPages() as $page) {
            if (strpos($page, 'module') === false) {
                $array_tmp = array();
                $array_tmp['link'] = $page;
                $array_tmp['name'] = $page;
                array_push($page_controller, $array_tmp);
            }
        }
        # $sqlFilter : NOT SHOW ROOT CATEOGRY, ONLY SHOW FROM HOME
        $list_categories = Category::getAllCategoriesName(null, Context::getContext()->language->id, true, null, true, ' AND c.id_parent != 0 ');
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        
        $languages = Language::getLanguages();
        $list_id_lang = array();
        foreach ($languages as $languages_val) {
            array_push($list_id_lang, $languages_val['id_lang']);
        }
        
        // Get value with keys special
        $languages = Language::getLanguages(false);
        $config_val = array();
        
        $list_field = Tools::getValue('list_field');
        $list_field = array_filter(explode(',', $list_field));
        foreach ($list_field as $field) {
            $key = $field;
            $config_val[$key] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($key));
        }
        
        $list_field_lang = Tools::getValue('list_field_lang');
        $list_field_lang = array_filter(explode(',', $list_field_lang));
        foreach ($languages as $lang) {
            foreach ($list_field_lang as $field) {
                $key = $field.'_'.$lang['id_lang'];
                $config_val[$key] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($key));
            }
        }
        
        $inputs = array(
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script>var listData = '.Tools::jsonEncode($config_val).';</script>',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' =>   '<script>
                                        var totalLanguage = "'.count($languages).'";
                                        var list_id_lang = \''.Tools::jsonEncode($list_id_lang).'\';
                                        var remove_button_text = "'.$this->l('Remove Link').'";
                                        var duplicate_button_text = "'.$this->l('Duplicate Link').'";
                                    </script>',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'total_link',
                'default' => '0',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_id_link',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_field',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_field_lang',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'blockLink',
                'name' => 'title',
                'lang' => 'true',
                'label' => $this->l('Title'),
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Widget Title'),
                'hint'  => $this->l('Auto hide if leave it blank'),
                'lang'  => 'true',
                'default' => '',
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Widget Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => '',
            ),
            array(
                'type'       => 'select',
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="button" class="add-new-link btn btn-default">
                                        <i class="process-icon-new"></i> '.$this->l('Add new link').'</button>',
                'form_group_class' => 'frm-add-new-link',
            ),
            array(
                'type'  => 'text',
                'label' => $this->l('Title'),
                'name'  => 'link_title',
                'lang'  => true,
                'default'   => '',
                'class' => 'tmp',
                'form_group_class'  => 'parent-tmp hidden',
            ),
            array(
                'type'       => 'select',
                'label'   => $this->l('Target Type'),
                'name'       => 'target_type',
                'options' => array(
                    'query' => $target,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => '_self',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type'       => 'select',
                'label'   => $this->l('Link Type'),
                'name'       => 'link_type',
                'options' => array(
                    'query' => $types,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'link-url',
                'class' => 'form-action tmp',
                'hint'    => $this->l('Select a link type'),
                'form_group_class' => 'parent-tmp hidden',
            ),
            
            
            
            
            
            
            
            
            array(
                'type' => 'text',
                'label' => $this->l('URL'),
                'name' => 'link_url',
                'lang' => true,
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-url parent-tmp hidden',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('CMS'),
                'name' => 'cmspage_id',
                'options' => array('query' => CMS::listCms(Context::getContext()->language->id, false, true),
                    'id' => 'id_cms',
                    'name' => 'meta_title'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-cms parent-tmp hidden',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Categories'),
                'name' => 'category_id',
                'options' => array('query' => $list_categories,
                    'id' => 'id_category',
                    'name' => 'name'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-category parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Product ID'),
                'name' => 'product_id',
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-product parent-tmp hidden',
                'hint'    => $this->l('Enter a product id'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Manufacture'),
                'name' => 'manufacture_id',
                'options' => array('query' => Manufacturer::getManufacturers(false, Context::getContext()->language->id, true),
                    'id' => 'id_manufacturer',
                    'name' => 'name'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-manufacture parent-tmp hidden',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Supplier'),
                'name' => 'supplier_id',
                'options' => array('query' => Supplier::getSuppliers(false, Context::getContext()->language->id, true),
                    'id' => 'id_supplier',
                    'name' => 'name'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-supplier parent-tmp hidden',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Page'),
                'name' => 'page_id',
                'options' => array('query' => $page_controller,
                    'id' => 'link',
                    'name' => 'name'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-page parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Parameter of page'),
                'name' => 'page_param',
                'default' => '',
                'hint'    => $this->l('Param on URL. Example var=100&var2=110&var3=120'),
                'class' => 'tmp',
                'form_group_class' => 'link_type_sub link_type-link-page parent-tmp hidden',
            ),
        );
        
        return $inputs;
    }
    
    public function getConfigValue()
    {
        $config_val = parent::getConfigValue();
        $config_val['target_type'] = '_self';
        return $config_val;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function prepareFontContent($assign, $module = null)
    {
        unset($module);
        $formAtts = $assign['formAtts'];
        $formAtts['links'] = array();
        
        if (!isset($formAtts['list_id_link'])) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show this Blocklink. Please remove it and create new Blocklink in Appbuilder Profile.';
            return $assign;
        }
        $id_forms = $formAtts['list_id_link'];
        $id_forms = array_filter(explode(',', $id_forms));
        
        foreach ($id_forms as $id_form) {
            $index = '_'.$id_form;
            $indexlang = $index.'_'.Context::getContext()->language->id;
            $link = array();
            switch ($formAtts['link_type'.$index]) {
                case 'link-url':
                    if (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) {
                        $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $formAtts['link_url'.$indexlang];
                        $link['link'] = isset($formAtts['link_url'.$indexlang]) ? $formAtts['link_url'.$indexlang] : '';
                        $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                        $formAtts['links'][] = $link;
                    }
                    break;
                case 'link-category':
                    if(isset($formAtts['category_id'.$index]) && $formAtts['category_id'.$index]){
                        $category = new Category((int)$formAtts['category_id'.$index], Context::getContext()->language->id, Context::getContext()->shop->id);
                        $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $category->name;
                        $link['link'] = Context::getContext()->link->getCategoryLink($category);
                        $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                        $formAtts['links'][] = $link;
                    }
                    break;
                case 'link-product':
                    $product_id = isset($formAtts['product_id'.$index]) ? (int)$formAtts['product_id'.$index] : 0;
                    $product = new Product($product_id, true, Context::getContext()->language->id, Context::getContext()->shop->id);
                    $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $product->name;
                    if ((int)$product->id > 0) {
                        $link['link'] = Context::getContext()->link->getProductLink($product);
                    } else {
                        $link['link'] = '';
                    }
                    $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                    $formAtts['links'][] = $link;
                    break;
                case 'link-manufacture':
                    if(isset($assign['formAtts']['manufacture_id'.$index]) && $assign['formAtts']['manufacture_id'.$index]){
                        $manufacture = new Manufacturer($assign['formAtts']['manufacture_id'.$index], Context::getContext()->language->id);
                        $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $manufacture->name;
                        $link['link'] = Context::getContext()->link->getManufacturerLink((int)$manufacture->id, $manufacture->link_rewrite, Context::getContext()->language->id);
                        $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                        $formAtts['links'][] = $link;
                    }
                    break;
                case 'link-supplier':
                    $supplier = new Supplier($assign['formAtts']['supplier_id'], Context::getContext()->language->id);
                    $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $supplier->name;
                    $link['link'] = Context::getContext()->link->getSupplierLink((int)$supplier->id, $supplier->link_rewrite, Context::getContext()->language->id);
                    $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                    $formAtts['links'][] = $link;
                    break;
                case 'link-cms':
                    if(isset($formAtts['cmspage_id'.$index]) && $formAtts['cmspage_id'.$index]){
                        $cms = new CMS((int)$formAtts['cmspage_id'.$index], Context::getContext()->language->id, Context::getContext()->shop->id);
                        $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : $cms->meta_title;
                        $link['link'] = Context::getContext()->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));
                        $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                        $formAtts['links'][] = $link;
                    }
                    break;
                case 'link-page':
                    $param = isset($formAtts['page_param'.$index]) ? $formAtts['page_param'.$index] : '';
//                    $front_link = $this->getFronTLink($formAtts['page_id'.$index]);
//                    if ($front_link != false) {
//                        $formAtts['page_id'.$index] = $front_link;
//                    }
                    $link['link'] = Context::getContext()->link->getPageLink($formAtts['page_id'.$index], null, Context::getContext()->language->id, $param);
                    $link['title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : Tools::ucfirst($formAtts['page_id'.$index]);
                    $link['target_type'] = isset($formAtts['target_type'.$index]) ? $formAtts['target_type'.$index] : '_self';
                    $formAtts['links'][] = $link;
                    break;
            }
        }
        
        $assign['formAtts']['links'] = $formAtts['links'];
        return $assign;
    }
    
    public function getFronTLink($controller_param)
    {
        $controllers = Dispatcher::getControllers(array(_PS_FRONT_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/front/'));
        $controllers['index'] = 'IndexController';
        if (isset($controllers['auth'])) {
            $controllers['authentication'] = $controllers['auth'];
        }
        if (isset($controllers['contact'])) {
            $controllers['contactform'] = $controllers['contact'];
        }
        if (!isset($controllers[Tools::strtolower($controller_param)])) {
            return false;
        }
        $controller_class = $controllers[Tools::strtolower($controller_param)];
        $controller_obj = Controller::getController($controller_class);
        return $controller_obj->php_self;
    }
}
