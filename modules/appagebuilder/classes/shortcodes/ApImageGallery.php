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

class ApImageGallery extends ApShortCodeBase
{
    public $name = 'ApImageGallery';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Image Gallery'),
            'position' => 7,
            'desc' => $this->l('Create Images Mini Gallery From A Folder'),
            'icon_class' => 'icon-th',
            'tag' => 'content');
    }
    
    private function addCustomJS()
    {
        return Context::getContext()->smarty->fetch(apPageHelper::getShortcodeTemplatePath($this->name.'.tpl'));
    }

    public function getConfigList()
    {
        $path_info = 'Example: themes/'._THEME_NAME_.'/assets/img/modules/appagebuilder/images/yourFolderImage';
        $path = 'themes/'._THEME_NAME_.'/assets/img/modules/appagebuilder/images/';
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'path',
                'label' => $this->l('Path'),
                'desc' => $this->trans($path_info),
                'form_group_class' => 'aprow_general',
                'default' => $path,
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<input class="btn btn-primary btn-create-folder" type="button" value="'.$this->l('AJAX Create Folder').'" name="create_folder">' . $this->addCustomJS(),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit'),
                'name' => 'limit',
                'default' => '12',
                'desc' => $this->l('Enter a number')
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Columns'),
                'name' => 'columns',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('1 Column')),
                        array('id' => '2', 'name' => $this->l('2 Columns')),
                        array('id' => '3', 'name' => $this->l('3 Columns')),
                        array('id' => '4', 'name' => $this->l('4 Columns')),
                        array('id' => '5', 'name' => $this->l('5 Columns')),
                        array('id' => '6', 'name' => $this->l('6 Columns')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'default' => '4',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Load more'),
                'name' => 'load_more',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'default' => '0',
                'desc' => $this->l('Show button load more image. Auto hide the button if all images is showed'),
            )
        );
        return $inputs;
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $form_atts = $assign['formAtts'];
        $form_atts['path'] = ltrim($form_atts['path'], '/');
        $form_atts['path'] = rtrim($form_atts['path'], '/');

        $limit = (int)$form_atts['limit'];
        $images = array();
        $link = new Link();
        $current_link = $link->getPageLink('', null, Context::getContext()->language->id);
        $path = _PS_ROOT_DIR_.'/'.str_replace($current_link, '', isset($form_atts['path']) ? $form_atts['path'] : '');
        $arr_exten = array('jpg', 'jpge', 'gif', 'png');

        $count = 0;
        if ($path && is_dir($path)) {
            if ($handle = scandir($path)) {
                
                if (($key = array_search('.', $handle)) !== false) {
                    unset($handle[$key]);
                }
                if (($key = array_search('..', $handle)) !== false) {
                    unset($handle[$key]);
                }
        
                foreach ($handle as $entry) {
                    if ($entry != '.' && $entry != '..' && is_file($path.'/'.$entry)) {
                        $ext = Tools::strtolower(pathinfo($path.'/'.$entry, PATHINFO_EXTENSION));
                        if (in_array($ext, $arr_exten)) {
                            # FIX 1.7
                            $images[] = __PS_BASE_URI__.str_replace($current_link, '', $form_atts['path']).'/'.$entry;
                            $count++;
                            if ($count == $limit) {
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        $total = count($handle);
        $total_nerver_show = (int)( $total - $count );
        
        if( isset($form_atts['load_more']) && (int) $form_atts['load_more'] == 1 && $total_nerver_show)
        {
            # SHOW BUTTON SHOW_MORE
            $assign['show_number'] = $count;
            $assign['show_more_btn'] = 1;
        }
        
        
        $c = (int)$form_atts['columns'];
        $assign['columns'] = $c > 0 ? $c : 4;
        $assign['images'] = $images;
        if( isset($assign['tpl_dir'])) {
            unset($assign['tpl_dir']);
        }
        $assign['assign'] = Tools::jsonEncode($assign);
        
        return $assign;
    }
    
    /**
     * AJAX : create folder image follow user type url
     */
    public function ajaxCallBackCreateDir()
    {
        $path = Tools::getValue('path');
        
//        $domain = strpos($url, _PS_BASE_URL_.__PS_BASE_URI__ );
//        if ($domain === false)
//        {
//            # CHECK NOT SAME DOMAIN
//            # http://localhost/prestashop/ps_1700_RC03_local     !=     http://prestashop/ps_1700_RC03_local
//            die(Tools::jsonEncode(array(
//                'hasError' => true,
//                'error' => $this->l('Domain is incorrect. Please type this at first: '),
//                'img_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
//            )));
//        }
        
        $img_dir = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, '', $path);
        $img_dir = _PS_ROOT_DIR_.'/'.$img_dir;
        $img_dir = str_replace('\\', '/', $img_dir);
        $img_dir = str_replace('//', '/', $img_dir);
        
        if (file_exists($img_dir)) {
            # CHECK FOLDER EXIST
            die(Tools::jsonEncode(array(
                'hasError' => true,
                'error' => $this->l('Folder is exist'),
                'img_dir' => $img_dir,
            )));
        }
        
        try {
            $result = mkdir($img_dir, 0755, true);
            if ($result) {
                die(Tools::jsonEncode(array(
                    'success' => true,
                    'information' => $this->l('Create folder successful'),
                    'img_dir' => $img_dir,
                )));
            }
        } catch (Exception $ex) {
            die(Tools::jsonEncode(array(
                'hasError' => true,
                'error' => $this->l('Can NOT create folder'),
                'img_dir' => $img_dir,
            )));
        }
    }
}
