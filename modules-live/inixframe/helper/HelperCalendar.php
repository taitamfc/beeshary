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

/**
 * Class Inix2HelperCalendar
 */
class Inix2HelperCalendar extends Inix2Helper
{
    /**
     *
     */
    const DEFAULT_DATE_FORMAT = 'Y-mm-dd';
    /**
     *
     */
    const DEFAULT_COMPARE_OPTION = 1;

    /**
     * @var
     */
    private $_actions;
    /**
     * @var
     */
    private $_compare_actions;
    /**
     * @var
     */
    private $_compare_date_from;
    /**
     * @var
     */
    private $_compare_date_to;
    /**
     * @var
     */
    private $_compare_date_option;
    /**
     * @var
     */
    private $_date_format;
    /**
     * @var
     */
    private $_date_from;
    /**
     * @var
     */
    private $_date_to;
    /**
     * @var
     */
    private $_rtl;

    /**
     *
     */
    public function __construct()
    {
        $this->base_folder = 'helpers/calendar/';
        $this->base_tpl    = 'calendar.tpl';
        parent::__construct();
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setActions($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Actions value must be an traversable array');
        }

        $this->_actions = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        if (!isset($this->_actions)) {
            $this->_actions = array();
        }

        return $this->_actions;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setCompareActions($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Actions value must be an traversable array');
        }

        $this->_compare_actions = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getCompareActions()
    {
        if (!isset($this->_compare_actions)) {
            $this->_compare_actions = array();
        }

        return $this->_compare_actions;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCompareDateFrom($value)
    {
        $this->_compare_date_from = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompareDateFrom()
    {
        return $this->_compare_date_from;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCompareDateTo($value)
    {
        $this->_compare_date_to = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompareDateTo()
    {
        return $this->_compare_date_to;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCompareOption($value)
    {
        $this->_compare_date_option = (int) $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompareOption()
    {
        if (!isset($this->_compare_date_option)) {
            $this->_compare_date_option = self::DEFAULT_COMPARE_OPTION;
        }

        return $this->_compare_date_option;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setDateFormat($value)
    {
        if (!is_string($value)) {
            throw new PrestaShopException('Date format must be a string');
        }

        $this->_date_format = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        if (!isset($this->_date_format)) {
            $this->_date_format = self::DEFAULT_DATE_FORMAT;
        }

        return $this->_date_format;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setDateFrom($value)
    {
        if (!isset($value) || $value == '') {
            $value = date('Y-m-d', strtotime("-31 days"));
        }

        if (!is_string($value)) {
            throw new PrestaShopException('Date must be a string');
        }

        $this->_date_from = $value;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getDateFrom()
    {
        if (!isset($this->_date_from)) {
            $this->_date_from = date('Y-m-d', strtotime("-31 days"));
        }

        return $this->_date_from;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setDateTo($value)
    {
        if (!isset($value) || $value == '') {
            $value = date('Y-m-d');
        }

        if (!is_string($value)) {
            throw new PrestaShopException('Date must be a string');
        }

        $this->_date_to = $value;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getDateTo()
    {
        if (!isset($this->_date_to)) {
            $this->_date_to = date('Y-m-d');
        }

        return $this->_date_to;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRTL($value)
    {
        $this->_rtl = (bool) $value;

        return $this;
    }

    /**
     * @param $action
     *
     * @return $this
     */
    public function addAction($action)
    {
        if (!isset($this->_actions)) {
            $this->_actions = array();
        }

        $this->_actions[] = $action;

        return $this;
    }

    /**
     * @param $action
     *
     * @return $this
     */
    public function addCompareAction($action)
    {
        if (!isset($this->_compare_actions)) {
            $this->_compare_actions = array();
        }

        $this->_compare_actions[] = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $context       = Context::getContext();
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);
        $bo_theme      = ((Validate::isLoadedObject($context->employee)
                           && $context->employee->bo_theme) ? $context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR
                         . 'template')
        ) {
            $bo_theme = 'default';
        }

        if ($context->controller->ajax) {
            $html = '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'modules/inixframe/js/date-range-picker.js"></script>';
            $html = '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'modules/inixframe/js/calendar.js"></script>';
        } else {
            $html = '';
            $context->controller->addJs($this->module->getFramePathUri() . 'js/date-range-picker.js');
            $context->controller->addJs($this->module->getFramePathUri() . 'js/calendar.js');
        }

        $this->tpl = $this->createTemplate($this->base_tpl);
        $this->tpl->assign(array(
            'date_format'       => $this->getDateFormat(),
            'date_from'         => $this->getDateFrom(),
            'date_to'           => $this->getDateTo(),
            'compare_date_from' => $this->getCompareDateFrom(),
            'compare_date_to'   => $this->getCompareDateTo(),
            'actions'           => $this->getActions(),
            'compare_actions'   => $this->getCompareActions(),
            'compare_option'    => $this->getCompareOption(),
            'is_rtl'            => $this->isRTL()
        ));

        $html .= parent::generate();

        return $html;
    }

    /**
     * @return bool
     */
    public function isRTL()
    {
        if (!isset($this->_rtl)) {
            $this->_rtl = Context::getContext()->language->is_rtl;
        }

        return $this->_rtl;
    }
}
