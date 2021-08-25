<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

abstract class Controller extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();

        if (!class_exists('BackwardCompatibility')) {
            include_once _PS_ROOT_DIR_ . '/modules/rediscache/classes/BackwardCompatibility.php';
        }
    }

    /**
     * This is an override for PS.1.6 to store cached markup into Redis,
     * since hookActionOutputHTMLBefore is not available.
     *
     * @param array $content
     * @return string
     */
    protected function smartyOutputContent($content)
    {
        if (!BackwardCompatibility::versionCheck('1.6.0', '1.6.9')) {
            return parent::smartyOutputContent($content);
        }

        $rediscache = Module::getInstanceByName('rediscache');

        $this->context->cookie->write();
        if (is_array($content)) {
            foreach ($content as $tpl) {
                $html = $this->context->smarty->fetch($tpl);
            }
        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);
        if ($this->controller_type == 'front' && !empty($html)) {
            $dom_available = extension_loaded('dom') ? true : false;
            if ($dom_available) {
                $html = Media::deferInlineScripts($html);
            }

            $html = trim(str_replace(array('</body>', '</html>'), '', $html)) . "\n";
            $this->context->smarty->assign(array(
                'js_def' => Media::getJsDef(),
                'js_files' => array_unique($this->js_files),
                'js_inline' => $dom_available ? Media::getInlineScript() : array(),
            ));
            $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_ . 'javascript.tpl');

            /**
             * Cache output into redis.
             */
            $markup = $html . $javascript . "\t</body>\n</html>";
            if (Module::isEnabled('rediscache') && $rediscache) {
                $data = array('html' => $markup);
                $rediscache->hookActionOutputHTMLBefore($data);
            }
            echo $markup;
        } else {
            /**
             * Cache output into redis.
             */
            if (Module::isEnabled('rediscache') && $rediscache) {
                $data = array('html' => $html);
                $rediscache->hookActionOutputHTMLBefore($data);
            }
            echo $html;
        }
    }
}
