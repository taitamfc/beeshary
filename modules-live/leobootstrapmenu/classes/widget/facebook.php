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

class LeoWidgetFacebook extends LeoWidgetBase
{
    public $name = 'facebook';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Facebook'), 'explain' => 'Facebook Like Box');
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();
        $soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        );
        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Widget Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Page URL'),
                    'name' => 'page_url',
                    'default' => 'https://www.facebook.com/LeoTheme',
                ),
                // array(
                    // 'type' => 'switch',
                    // 'label' => $this->l('Is Border'),
                    // 'name' => 'border',
                    // 'values' => $soption,
                    // 'default' => '1',
                // ),
                // array(
                    // 'type' => 'select',
                    // 'label' => $this->l('Color'),
                    // 'name' => 'target',
                    // 'options' => array('query' => array(
                            // array('id' => 'dark', 'name' => $this->l('Dark')),
                            // array('id' => 'light', 'name' => $this->l('Light')),
                        // ),
                        // 'id' => 'id',
                        // 'name' => 'name'),
                    // 'default' => '_self',
                // ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Tab Display'),
                    'name' => 'tabdisplay',
                    'multiple' => true,
                    'values' => array(
                        'query' => array(
                            array('key' => 'timeline', 'name' => $this->l('Timeline')),
                            array('key' => 'events', 'name' => $this->l('Events')),
                            array('key' => 'messages', 'name' => $this->l('Messages')),
                        ),
                        'id' => 'key',
                        'name' => 'name'
                    ),
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'name' => 'width',
                    'default' => '340',
                    'desc' => $this->l('Min: 180 and Max: 500. Default: 340')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                    'default' => '500',
                    'desc' => $this->l('Min: 70. Default: 500')
                ),
                // array(
                    // 'type' => 'switch',
                    // 'label' => $this->l('Show Stream'),
                    // 'name' => 'show_stream',
                    // 'values' => $soption,
                    // 'default' => '0',
                // ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Faces'),
                    'name' => 'show_faces',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Hide Cover'),
                    'name' => 'hide_cover',
                    'values' => $soption,
                    'default' => '0',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Small Header'),
                    'name' => 'small_header',
                    'values' => $soption,
                    'default' => '0',
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
        $tabdisplay = array();
        //update for new plugin facebook like
        if ($setting['tabdisplay_timeline'] != '') {
            array_push($tabdisplay, 'timeline');
        }
        //update for new plugin facebook like
        if ($setting['tabdisplay_events'] != '') {
            array_push($tabdisplay, 'events');
        }
        //update for new plugin facebook like
        if ($setting['tabdisplay_messages'] != '') {
            array_push($tabdisplay, 'messages');
        }
        $tabdisplay = implode(",", $tabdisplay);
        # validate module
        unset($args);
        $t = array(
            'name' => '',
            'application_id' => '',
            'page_url' => 'https://www.facebook.com/LeoTheme',
            // 'border' => 0,
            // 'color' => 'light',
            // 'tabdisplay_timeline',
            // 'tabdisplay_events',
            // 'tabdisplay_messages',
            'tabdisplay' => $tabdisplay,
            'width' => 290,
            'height' => 200,
            // 'show_stream' => 0,
            'show_faces' => 1,
            'hide_cover' => 0,
            'small_header' => 0,
            'displaylanguage' => 'en_US'
        );
        $setting = array_merge($t, $setting);

        $output = array('type' => 'facebook', 'data' => $setting);
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
                'page_url',
                // 'border',
                // 'target',
                'tabdisplay_timeline',
                'tabdisplay_events',
                'tabdisplay_messages',
                'width',
                'height',
                // 'show_stream',
                'show_faces',
                'hide_cover',
                'small_header',
            );
        } elseif ($multi_lang == 1) {
            return array(
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
