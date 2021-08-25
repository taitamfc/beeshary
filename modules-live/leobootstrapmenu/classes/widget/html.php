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

class LeoWidgetHtml extends LeoWidgetBase
{
    public $name = 'html';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('HTML'), 'explain' => $this->l('Create HTML With multiple Language'));
    }

    public function renderForm($args, $data)
    {
        #validate module
        unset($args);
        $helper = $this->getFormHelper();

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Widget Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'htmlcontent',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true,
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true,
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

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        #validate module
        unset($args);
        $t = array(
            'name' => '',
            'html' => '',
        );
        $setting = array_merge($t, $setting);
        $languageID = Context::getContext()->language->id;
        $setting['html'] = isset($setting['htmlcontent_'.$languageID]) ? Tools::stripslashes($setting['htmlcontent_'.$languageID]) : '';
    //update dynamic url
        if (strpos($setting['html'], '_AP_IMG_DIR') !== false) {
            // validate module
            $setting['html'] = str_replace('_AP_IMG_DIR/', $this->theme_img_module, $setting['html']);
        }
        $output = array('type' => 'html', 'data' => $setting);

        return $output;
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
            return array(
                'htmlcontent',
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
