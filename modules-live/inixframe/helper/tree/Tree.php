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
require_once dirname(__FILE__) . '/ITreeToolbar.php';
require_once dirname(__FILE__) . '/ITreeToolbarButton.php';

/**
 * Class Inix2Tree
 */
class Inix2Tree
{
    /**
     *
     */
    const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/tree';
    /**
     *
     */
    const DEFAULT_TEMPLATE = 'tree.tpl';
    /**
     *
     */
    const DEFAULT_HEADER_TEMPLATE = 'tree_header.tpl';
    /**
     *
     */
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder.tpl';
    /**
     *
     */
    const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item.tpl';

    /**
     * @var
     */
    protected $_attributes;
    /**
     * @var
     */
    private $_context;
    /**
     * @var
     */
    protected $_data;
    /**
     * @var
     */
    protected $_headerTemplate;
    /**
     * @var
     */
    private $_id;
    /**
     * @var
     */
    protected $_node_folder_template;
    /**
     * @var
     */
    protected $_node_item_template;
    /**
     * @var
     */
    protected $_template;
    /**
     * @var
     */
    private $_template_directory;
    /**
     * @var
     */
    private $_title;
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
     * @param      $id
     * @param null $data
     *
     * @throws PrestaShopException
     */
    public function __construct($id, $data = null)
    {
        $this->setId($id);

        if (isset($data)) {
            $this->setData($data);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setActions($value)
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new Inix2TreeToolbar());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->setActions($value)
             ->setModule($this->getModule());

        return $this;
    }

    /**
     * @return mixed
     * @throws PrestaShopException
     */
    public function getActions()
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new Inix2TreeToolbar());
        }

        return $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->getActions()
                    ->setModule($this->getModule());
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
     * @return $this
     * @throws PrestaShopException
     */
    public function setData($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_data = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!isset($this->_data)) {
            $this->_data = array();
        }

        return $this->_data;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setHeaderTemplate($value)
    {
        $this->_headerTemplate = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaderTemplate()
    {
        if (!isset($this->_headerTemplate)) {
            $this->setHeaderTemplate(self::DEFAULT_HEADER_TEMPLATE);
        }

        return $this->_headerTemplate;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setId($value)
    {
        $this->_id = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setNodeFolderTemplate($value)
    {
        $this->_node_folder_template = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setNodeItemTemplate($value)
    {
        $this->_node_item_template = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
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
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

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
            $this->_template_directory = $this->_normalizeDirectory(
                self::DEFAULT_TEMPLATE_DIRECTORY
            );
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
     * @param $value
     *
     * @return $this
     */
    public function setTitle($value)
    {
        $this->_title = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function setToolbar($value)
    {
        if (!is_object($value)) {
            throw new PrestaShopException('Toolbar must be a class object');
        }

        $reflection = new ReflectionClass($value);

        if (!$reflection->implementsInterface('IInix2TreeToolbar')) {
            throw new PrestaShopException('Toolbar class must implements ITreeToolbarCore interface');
        }

        $this->_toolbar = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToolbar()
    {
        if (isset($this->_toolbar)) {
            $this->_toolbar->setData($this->getData());
        }

        return $this->_toolbar;
    }

    /**
     * @param $action
     *
     * @return $this
     * @throws PrestaShopException
     */
    public function addAction($action)
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new Inix2TreeToolbar());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->addAction($action)
             ->setModule($this->getModule());

        return $this;
    }

    /**
     * @return $this
     * @throws PrestaShopException
     */
    public function removeActions()
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new Inix2TreeToolbar());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->removeActions()
             ->setModule($this->getModule());

        return $this;
    }

    /**
     * @param null $data
     *
     * @return string
     * @throws PrestaShopException
     */
    public function render($data = null)
    {
        //Adding tree.js
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);
        $bo_theme      = ((Validate::isLoadedObject($this->getContext()->employee)
                           && $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR . 'template')) {
            $bo_theme = 'default';
        }

        $js_path = __PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/tree.js';
        if ($this->getContext()->controller->ajax) {
            $html = '<script type="text/javascript" src="' . $js_path . '"></script>';
        } else {
            $this->getContext()->controller->addJs($js_path);
        }

        //Create Tree Template
        $template = $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        );

        if (trim($this->getTitle()) != '' || $this->useToolbar()) {
            //Create Tree Header Template
            $headerTemplate = $this->getContext()->smarty->createTemplate(
                $this->getTemplateFile($this->getHeaderTemplate()),
                $this->getContext()->smarty
            );
            $headerTemplate->assign($this->getAttributes())
                           ->assign(array(
                               'title'   => $this->getTitle(),
                               'toolbar' => $this->useToolbar() ? $this->renderToolbar() : null
                           ));
            $template->assign('header', $headerTemplate->fetch());
        }

        //Assign Tree nodes
        $template->assign($this->getAttributes())->assign(array(
            'id'    => $this->getId(),
            'nodes' => $this->renderNodes($data)
        ));

        return (isset($html) ? $html : '') . $template->fetch();
    }

    /**
     * @param null $data
     *
     * @return string
     * @throws PrestaShopException
     */
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
                    'children' => $this->renderNodes($item['children']),
                    'node'     => $item
                ))->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'node' => $item
                ))->fetch();
            }
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function renderToolbar()
    {
        return $this->getToolbar()->render();
    }

    /**
     * @return bool
     */
    public function useInput()
    {
        return isset($this->_input_type);
    }

    /**
     * @return bool
     */
    public function useToolbar()
    {
        return isset($this->_toolbar);
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
