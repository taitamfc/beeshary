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

class LeoWidgetRawhtml extends LeoWidgetBase
{
    public $name = 'raw_html';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Raw HTML'), 'explain' => $this->l('Put Raw HTML Code'));
    }

    public function renderForm($args, $data)
    {
        $helper = $this->getFormHelper();

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Widget Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'raw_html',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true,
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => false,
                    'desc' => $this->l('Enter HTML CODE in here')
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

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );
        unset($args);

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        $t = array(
            'name' => '',
            'raw_html' => '',
        );

        $setting = array_merge($t, $setting);
                
        if (isset($setting['raw_html']) && $setting['raw_html'] != '') {
            //keep backup
            $html = $setting['raw_html'];
            $html = html_entity_decode(Tools::stripslashes($html), ENT_QUOTES, 'UTF-8');
        } else {
            //change raw html to use multi lang
            $languageID = Context::getContext()->language->id;
            $setting['raw_html'] = isset($setting['raw_html_'.$languageID]) ? html_entity_decode(Tools::stripslashes($setting['raw_html_'.$languageID]), ENT_QUOTES, 'UTF-8') : '';
            
            //update dynamic url
            if (strpos($setting['raw_html'], '_AP_IMG_DIR') !== false) {
                // validate module
                $setting['raw_html'] = str_replace('_AP_IMG_DIR/', $this->theme_img_module, $setting['raw_html']);
            }
        }
        
//        $header = '';
//        $content = $html;

        $output = array('type' => 'raw_html', 'data' => $setting);
        unset($args);
        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        //change raw html to use multi lang
        if ($multi_lang == 0) {
            return array(
            );
        } elseif ($multi_lang == 1) {
            return array(
                'raw_html',
            );
        } elseif ($multi_lang == 2) {
            return array();
        }
    }
}
