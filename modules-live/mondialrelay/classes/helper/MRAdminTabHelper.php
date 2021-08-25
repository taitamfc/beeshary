<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

//namespace MRHelper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MRAdminTabHelper
{
    /**
     * Function to delete admin tabs from a menu with the module name
     * @param  string $name name of the module to delete
     * @return bool
     */
    public static function deleteAdminTabs($name)
    {
        MondialRelay::debug('deleteAdminTabs called');
        // Get collection from module if tab exists
        $tabs = Tab::getCollectionFromModule($name);
        // Initialize result
        $result = true;
        // Check tabs
        if ($tabs && count($tabs)) {
            // Loop tabs for delete
            foreach ($tabs as $tab) {
                $result &= $tab->delete();
            }
        }

        return $result;
    }

    /**
     * Add admin tabs in the menu
     * @param array $data
     *              array[
     *              array[
     *              id_parent => 0 || void
     *              className => Controller to link to
     *              module => modulename to easily delete when uninstalling
     *              name => name to display
     *              position => position
     *              ]
     *              ]
     * @return bool|int|array
     */
    public static function addAdminTab($data)
    {
        if (is_array(current($data))) {
            $ids = array();

            foreach ($data as $tab) {
                $ids[] = MRAdminTabHelper::addAdminTab($tab);
            }

            return $ids;
        }

        // Get ID Parent
        if (isset($data['id_parent'])) {
            $id_parent = $data['id_parent'];
        } else {
            $id_parent = (int)Tab::getIdFromClassName($data['classNameParent']);
        }

        // Tab
        $tab = Tab::getInstanceFromClassName($data['className']);

        $tab->id_parent = (int)$id_parent;
        $tab->class_name = $data['className'];
        $tab->module = $data['module'];
        $tab->position = Tab::getNewLastPosition((int)$id_parent);
        $tab->active = 1;

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $tab->name[(int)$lang['id_lang']] = $data['name'];
        }

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }
}
