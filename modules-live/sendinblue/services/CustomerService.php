<?php
/**
 * 2007-2021 Sendinblue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@sendinblue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sendinblue <contact@sendinblue.com>
 * @copyright 2007-2021 Sendinblue
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Sendinblue
 */

namespace Sendinblue\Services;

class CustomerService
{
    /**
     * @param array $filter
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getCustomers($filter, $limit, $offset)
    {
        $sqlAnd = '';
        if (is_array($filter) && !empty($filter)) {
            foreach ($filter as $field => $value) {
                $sqlAnd .= " AND c.`$field` = $value";
            }
        }

        return ['customers' => \Db::getInstance()->executeS('
            SELECT DISTINCT 
                c.`id_customer` AS id, 
                c.`email`, 
                c.`firstname`, 
                c.`lastname`, 
                c.`id_default_group`, 
                c.`id_lang`, 
                c.`id_gender`, 
                c.`newsletter`, 
                c.`newsletter_date_add`, 
                c.`ip_registration_newsletter`, 
                c.`birthday`, 
                c.`optin`, 
                c.`website`, 
                c.`company`, 
                c.`date_add`, 
                c.`date_upd`, 
                a.phone AS phone
            FROM `' . _DB_PREFIX_ . 'customer` c
            LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON (c.`id_customer` = a.`id_customer`)
            WHERE c.`active` = 1 ' . $sqlAnd . '
            ORDER BY c.`id_customer` ASC
            LIMIT ' . $limit . '
            OFFSET ' . $offset . '
        ')];
    }
}
