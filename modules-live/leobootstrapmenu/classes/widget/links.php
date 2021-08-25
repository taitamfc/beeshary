<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoWidgetLinks extends LeoWidgetBase
{
    public $name = 'link';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Block Links'), 'explain' => $this->l('Create List Block Links'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        $id_lang = Context::getContext()->language->id;
        unset($args);
        $helper = $this->getFormHelper();
        
        $languages = Language::getLanguages();
        $list_id_lang = array();
        foreach ($languages as $languages_val) {
            array_push($list_id_lang, $languages_val['id_lang']);
        }
        
        $categories = LeoBtmegamenuHelper::getCategories();
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang, true);
        $suppliers = Supplier::getSuppliers(false, $id_lang, true);
        $cmss = CMS::listCms($id_lang, false, true);
        $page_controller = array();
        foreach (Meta::getPages() as $page) {
            if (strpos($page, 'module') === false) {
                $array_tmp = array();
                $array_tmp['link'] = $page;
                $array_tmp['name'] = $page;
                array_push($page_controller, $array_tmp);
            }
        }
        
        //get list link id
        if (isset($data['params']['list_id_link'])) {
            $list_id_link = array_filter(explode(',', $data['params']['list_id_link']));
        } else {
            $list_id_link = array();
        }
            
        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Widget Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Text Link'),
                    'name' => 'text_link_0',
                    'default' => '',
                    'lang' => true,
                    'class' => 'tmp',
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Link Type'),
                    'name' => 'link_type_0',
                    'id' => 'link_type_0',
                    'desc' => $this->l('Select a link type and fill data for following input'),
                    'options' => array('query' => array(
                            array('id' => 'url', 'name' => $this->l('Url')),
                            array('id' => 'category', 'name' => $this->l('Category')),
                            array('id' => 'product', 'name' => $this->l('Product')),
                            array('id' => 'manufacture', 'name' => $this->l('Manufacture')),
                            array('id' => 'supplier', 'name' => $this->l('Supplier')),
                            array('id' => 'cms', 'name' => $this->l('Cms')),
                            array('id' => 'controller', 'name' => $this->l('Page Controller'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => 'url',
                    'class' => 'tmp',
                ),
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Product ID'),
                    'name' => 'product_type_0',
                    'id' => 'product_type_0',
                    'class' => 'link_type_group tmp',
                    'default' => '',
                
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS Type'),
                    'name' => 'cms_type_0',
                    'id' => 'cms_type_0',
                    'options' => array('query' => $cmss,
                        'id' => 'id_cms',
                        'name' => 'meta_title'),
                    'default' => '',
                    'class' => 'link_type_group tmp',
                    
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url_type_0',
                    'id' => 'url_type_0',
                    'lang' => true,
                    'class' => 'url-type-group-lang tmp',
                    'default' => '#',
                    
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Type'),
                    'name' => 'category_type_0',
                    'id' => 'category_type_0',
                    'options' => array('query' => $categories,
                        'id' => 'id_category',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group tmp',
                    
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manufacture Type'),
                    'name' => 'manufacture_type_0',
                    'id' => 'manufacture_type_0',
                    'options' => array('query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group tmp',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Supplier Type'),
                    'name' => 'supplier_type_0',
                    'id' => 'supplier_type_0',
                    'options' => array('query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group tmp',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('List Page Controller'),
                    'name' => 'controller_type_0',
                    'id' => 'controller_type_0',
                    'options' => array('query' => $page_controller,
                        'id' => 'link',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group tmp',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Parameter of page controller'),
                    'name' => 'controller_type_parameter_0',
                    'id' => 'controller_type_parameter_0',
                    'default' => '',
                    'lang' => true,
                    'class' => 'controller-type-group-lang tmp',
                    'desc' => 'Eg: ?a=1&b=2',
                ),
                array(
                    'type' => 'html',
                    'name' => 'add-new-link',
                    'html_content' => '<button class="add-new-link btn btn-default">
                                            <i class="process-icon-new"></i>
                                            <span>'.$this->l('Add New Link').'</span>
                                        </button>',
                    'default' => '',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'total_link',
                    'default' => count($list_id_link),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'list_field',
                    'default' => '',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'list_field_lang',
                    'default' => '',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'list_id_link',
                    'default' => '',
                ),
                
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => "<script>var list_id_lang = '".Tools::jsonEncode($list_id_lang)."';
                                                var copy_lang_button_text = '".$this->l('Copy to other languages')."';
                                                var copy_lang_button_text_done = '".$this->l('Copy to other languages ... DONE')."';
                                                var remove_button_text = '".$this->l('Remove Link')."';
                                                var duplicate_button_text = '".$this->l('Duplicate Link')."';
                                                var id_lang = ".$id_lang.";
                                    </script>",
                    'default' => '',
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Save And Stay'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'saveandstayleowidget'
                ),
                array(
                    'title' => $this->l('Save'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'saveleowidget'
                ),
            )
        );
        if (count($list_id_link) > 0) {
            foreach ($list_id_link as $list_id_link_val) {
                $text_link = array(
                    'type' => 'text',
                    'label' => $this->l('Text Link'),
                    'name' => 'text_link_'.$list_id_link_val,
                    'default' => '',
                    'lang' => true,
                    'class' => 'tmp element element-lang',
                    'required' => true,
                );
                $link_type = array(
                    'type' => 'select',
                    'label' => $this->l('Link Type'),
                    'name' => 'link_type_'.$list_id_link_val,
                    'id' => 'link_type_'.$list_id_link_val,
                    'desc' => $this->l('Select a link type and fill data for following input'),
                    'options' => array(
                        'query' => array(
                            array('id' => 'url', 'name' => $this->l('Url')),
                            array('id' => 'category', 'name' => $this->l('Category')),
                            array('id' => 'product', 'name' => $this->l('Product')),
                            array('id' => 'manufacture', 'name' => $this->l('Manufacture')),
                            array('id' => 'supplier', 'name' => $this->l('Supplier')),
                            array('id' => 'cms', 'name' => $this->l('Cms')),
                            array('id' => 'controller', 'name' => $this->l('Page Controller'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'tmp element',
                );
                $product_type = array(
                    'type' => 'text',
                    'label' => $this->l('Product ID'),
                    'name' => 'product_type_'.$list_id_link_val,
                    'id' => 'product_type_'.$list_id_link_val,
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                    'default' => '',
                );
                $cms_type = array(
                    'type' => 'select',
                    'label' => $this->l('CMS Type'),
                    'name' => 'cms_type_'.$list_id_link_val,
                    'id' => 'cms_type_'.$list_id_link_val,
                    'options' => array('query' => $cmss,
                        'id' => 'id_cms',
                        'name' => 'meta_title'),
                    'default' => '',
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                );
                $url_type = array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url_type_'.$list_id_link_val,
                    'id' => 'url_type_'.$list_id_link_val,
                    'lang' => true,
                    'class' => 'url-type-group-lang tmp element element-lang',
                    'default' => '#',
                );
                $category_type = array(
                    'type' => 'select',
                    'label' => $this->l('Category Type'),
                    'name' => 'category_type_'.$list_id_link_val,
                    'id' => 'category_type_'.$list_id_link_val,
                    'options' => array(
                        'query' => $categories,
                        'id' => 'id_category',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                );
                $manufacture_type = array(
                    'type' => 'select',
                    'label' => $this->l('Manufacture Type'),
                    'name' => 'manufacture_type_'.$list_id_link_val,
                    'id' => 'manufacture_type_'.$list_id_link_val,
                    'options' => array(
                        'query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                );
                $supplier_type = array(
                    'type' => 'select',
                    'label' => $this->l('Supplier Type'),
                    'name' => 'supplier_type_'.$list_id_link_val,
                    'id' => 'supplier_type_'.$list_id_link_val,
                    'options' => array(
                        'query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                );
                $controller_type = array(
                    'type' => 'select',
                    'label' => $this->l('List Page Controller'),
                    'name' => 'controller_type_'.$list_id_link_val,
                    'id' => 'controller_type_'.$list_id_link_val,
                    'options' => array(
                        'query' => $page_controller,
                        'id' => 'link',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'link_type_group_'.$list_id_link_val.' tmp element',
                );
                $controller_type_parameter = array(
                    'type' => 'text',
                    'label' => $this->l('Parameter of page controller'),
                    'name' => 'controller_type_parameter_'.$list_id_link_val,
                    'id' => 'controller_type_parameter_'.$list_id_link_val,
                    'default' => '',
                    'lang' => true,
                    'class' => 'controller-type-group-lang tmp element element-lang',
                    'desc' => 'Eg: ?a=1&b=2',
                );
                array_push($this->fields_form[1]['form']['input'], $text_link, $link_type, $product_type, $cms_type, $url_type, $category_type, $manufacture_type, $supplier_type, $controller_type, $controller_type_parameter);
            }
        }
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );
        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        # validate module
        unset($args);
        $t = array(
            'name' => '',
            'html' => '',
        );

        $setting = array_merge($t, $setting);
        $ac = array();
        $languageID = Context::getContext()->language->id;
        $text_link = $link = '';

        // for ($i = 1; $i <= 10; $i++) {
            // if (isset($setting['text_link_'.$i.'_'.$languageID]) && trim($setting['text_link_'.$i.'_'.$languageID])) {
                // $text_link = isset($setting['text_link_'.$i.'_'.$languageID]) ? html_entity_decode($setting['text_link_'.$i.'_'.$languageID], ENT_QUOTES, 'UTF-8') : 'No Link Title';

                // if (isset($setting['link_'.$i.'_'.$languageID])) {
                    // $link = trim($setting['link_'.$i.'_'.$languageID]);
                // } else {
                    // $link = trim($setting['link_'.$i]);
                // }

                // $ac[] = array('text' => Tools::stripslashes($text_link), 'link' => $link);
            // }
        // }
        $list_id_link = array_filter(explode(',', $setting['list_id_link']));
        if (count($list_id_link) > 0) {
            foreach ($list_id_link as $list_id_link_val) {
                if (isset($setting['text_link_'.$list_id_link_val.'_'.$languageID]) && trim($setting['text_link_'.$list_id_link_val.'_'.$languageID])) {
                    $text_link = isset($setting['text_link_'.$list_id_link_val.'_'.$languageID]) ? html_entity_decode($setting['text_link_'.$list_id_link_val.'_'.$languageID], ENT_QUOTES, 'UTF-8') : 'No Link Title';

                    // if (isset($setting['link_'.$i.'_'.$languageID])) {
                        // $link = trim($setting['link_'.$i.'_'.$languageID]);
                    // } else {
                        // $link = trim($setting['link_'.$i]);
                    // }
                    $link = $this->getLink($setting, $list_id_link_val);
                    $ac[] = array('text' => Tools::stripslashes($text_link), 'link' => $link);
                }
            }
        }
        $setting['id'] = rand();
        $setting['links'] = $ac;

        $output = array('type' => 'links', 'data' => $setting);

        return $output;
    }
    
    //get link from link type
    public function getLink($setting, $id_link = null)
    {
        // $value = (int)$menu['item'];
        $result = '';
        $link = new Link();
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        switch ($setting['link_type_'.$id_link]) {
            case 'product':
                $value = $setting['product_type_'.$id_link];
                if (Validate::isLoadedObject($obj_pro = new Product($value, true, $id_lang))) {
                    # validate module
                    $result = $link->getProductLink((int)$obj_pro->id, $obj_pro->link_rewrite, null, null, $id_lang, null, (int)Product::getDefaultAttribute((int)$obj_pro->id), false, false, true);
                }
                break;
            case 'category':
                $value = $setting['category_type_'.$id_link];
                if (Validate::isLoadedObject($obj_cate = new Category($value, $id_lang))) {
                    # validate module
                    $result = $link->getCategoryLink((int)$obj_cate->id, $obj_cate->link_rewrite, $id_lang);
                }
                break;
            case 'cms':
                $value = $setting['cms_type_'.$id_link];
                if (Validate::isLoadedObject($obj_cms = new CMS($value, $id_lang))) {
                    # validate module
                    $result = $link->getCMSLink((int)$obj_cms->id, $obj_cms->link_rewrite, $id_lang);
                }
                break;
            case 'url':
                // MENU TYPE : URL
                if (preg_match('/http:\/\//', $setting['url_type_'.$id_link.'_'.$id_lang]) || preg_match('/https:\/\//', $setting['url_type_'.$id_link.'_'.$id_lang])) {
                    // ABSOLUTE LINK : default
                } else {
                    // RELATIVE LINK : auto insert host
                    $host_name = LeoBtmegamenuHelper::getBaseLink().LeoBtmegamenuHelper::getLangLink();
                    $setting['url_type_'.$id_link.'_'.$id_lang] = $host_name.$setting['url_type_'.$id_link.'_'.$id_lang];
                }

                $value = $setting['url_type_'.$id_link.'_'.$id_lang];
                $regex = '((https?|ftp)\:\/\/)?'; // SCHEME
                $regex .= '([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?'; // User and Pass
                $regex .= '([a-z0-9-.]*)\.([a-z]{2,3})'; // Host or IP
                $regex .= '(\:[0-9]{2,5})?'; // Port
                $regex .= '(\/([a-z0-9+\$_-]\.?)+)*\/?'; // Path
                $regex .= '(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?'; // GET Query
                $regex .= '(#[a-z_.-][a-z0-9+\$_.-]*)?'; // Anchor
                if ($value == 'index' || $value == 'index.php') {
                    $result = $link->getPageLink('index.php', false, $id_lang);
                    break;
                } elseif ($value == '#' || preg_match("/^$regex$/", $value)) {
                    $result = $value;
                    break;
                } else {
                    $result = $value;
                }
                break;
            case 'manufacture':
                $value = $setting['manufacture_type_'.$id_link];
                if (Validate::isLoadedObject($obj_manu = new Manufacturer($value, $id_lang))) {
                    # validate module
                    $result = $link->getManufacturerLink((int)$obj_manu->id, $obj_manu->link_rewrite, $id_lang);
                }
                break;
            case 'supplier':
                $value = $setting['supplier_type_'.$id_link];
                if (Validate::isLoadedObject($obj_supp = new Supplier($value, $id_lang))) {
                    # validate module
                    $result = $link->getSupplierLink((int)$obj_supp->id, $obj_supp->link_rewrite, $id_lang);
                }
                break;
            case 'controller':
                //getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop);
                $value = $setting['controller_type_'.$id_link];
                $result = $link->getPageLink($value, null, $id_lang, null, false, $id_shop);
                if ($setting['controller_type_parameter_'.$id_link.'_'.$id_lang] != '') {
                    $result .= $setting['controller_type_parameter_'.$id_link.'_'.$id_lang];
                }
                break;
            default:
                $result = '#';
                break;
        }
        return $result;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        if ($multi_lang == 0) {
            return array(
            );
        } elseif ($multi_lang == 1) {
            // $number_html = 10;
            // $array = array();
            // for ($i = 1; $i <= $number_html; $i++) {
                // $array[] = 'text_link_'.$i;
                // $array[] = 'link_'.$i;
            // }
            // return $array;
            return array(
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
