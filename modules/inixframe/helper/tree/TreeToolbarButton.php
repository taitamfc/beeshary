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
 * Class Inix2TreeToolbarButton
 */
abstract class Inix2TreeToolbarButton
{
    /**
     *
     */
    const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/tree';

    /**
     * @var
     */
    protected $_attributes;
    /**
     * @var
     */
    private $_class;
    /**
     * @var
     */
    private $_context;
    /**
     * @var
     */
    private $_id;
    /**
     * @var
     */
    private $_label;
    /**
     * @var
     */
    private $_name;
    /**
     * @var
     */
    protected $_template;
    /**
     * @var
     */
    protected $_template_directory;
    /**
     * @var Inix2Module
     */
    private $module;

    /**
     * @param Inix2Module $module
     */
    public function setModule(Inix2Module $module)
    {
        $this->module = $module;
    }

    /**
     * @return Inix2Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param      $label
     * @param null $id
     * @param null $name
     * @param null $class
     */
    public function __construct($label, $id = null, $name = null, $class = null)
    {
        $this->setLabel($label);
        $this->setId($id);
        $this->setName($name);
        $this->setClass($class);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = array();
        }

        $this->_attributes[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return null
     */
    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->_attributes[$name] : null;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setAttributes($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_attributes = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = array();
        }

        return $this->_attributes;
    }

    /**
     * @param $value
     *
     * @return Inix2TreeToolbarButton
     */
    public function setClass($value)
    {
        return $this->setAttribute('class', $value);
    }

    /**
     * @return null
     */
    public function getClass()
    {
        return $this->getAttribute('class');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setContext($value)
    {
        $this->_context = $value;

        return $this;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        if (!isset($this->_context)) {
            $this->_context = Context::getContext();
        }

        return $this->_context;
    }

    /**
     * @param $value
     *
     * @return Inix2TreeToolbarButton
     */
    public function setId($value)
    {
        return $this->setAttribute('id', $value);
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @param $value
     *
     * @return Inix2TreeToolbarButton
     */
    public function setLabel($value)
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->getAttribute('label');
    }

    /**
     * @param $value
     *
     * @return Inix2TreeToolbarButton
     */
    public function setName($value)
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTemplate($value)
    {
        $this->_template = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTemplateDirectory($value)
    {
        $this->_template_directory = $this->_normalizeDirectory($value);

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateDirectory()
    {
        if (!isset($this->_template_directory)) {
            $this->_template_directory = $this->_normalizeDirectory(self::DEFAULT_TEMPLATE_DIRECTORY);
        }

        return $this->_template_directory;
    }

    /**
     * @param $template
     *
     * @return string
     */
    public function getTemplateFile($template)
    {
        if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== false) {
            $controllerName = strtolower($matches[0][1]);
        }

        if ($this->getContext()->controller instanceof Inix2AdminController and file_exists(
            $this->_normalizeDirectory(
                $this->getContext()->controller->getTemplatePath()
            ) .
            $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template
        )
        ) {
            return $this->_normalizeDirectory(
                $this->getContext()->controller->getTemplatePath()
            ) .
                   $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template;

        } elseif (file_exists(
            $this->_normalizeDirectory(
                $this->module->getLocalPath() . 'views/templates/admin/inixframe/'
            ) . $this->getTemplateDirectory() . $template
        )) {
            return $this->_normalizeDirectory(
                $this->module->getLocalPath() . 'views/templates/admin/inixframe/'
            ) . $this->getTemplateDirectory() . $template;

        } elseif (file_exists(
            $this->_normalizeDirectory(
                $this->module->getLocalPath() . 'views/templates/inixframe/'
            ) . $this->getTemplateDirectory() . $template
        )) {
            return $this->_normalizeDirectory(
                $this->module->getLocalPath() . 'views/templates/inixframe/'
            ) . $this->getTemplateDirectory() . $template;

        } else {
            return $this->_normalizeDirectory(
                $this->module->getFrameLocalPath() . 'template/'
            ) . $this->getTemplateDirectory() . $template;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return (isset($this->_attributes)
                && array_key_exists($name, $this->_attributes));
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        )->assign($this->getAttributes())->fetch();
    }

    /**
     * @param $directory
     *
     * @return string
     */
    private function _normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }
}
