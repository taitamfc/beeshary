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
 * Interface IInix2TreeToolbar
 */
interface IInix2TreeToolbar
{
    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setActions($value);

    /**
     * @return mixed
     */
    public function getActions();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setContext($value);

    /**
     * @return mixed
     */
    public function getContext();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setData($value);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setTemplate($value);

    /**
     * @return mixed
     */
    public function getTemplate();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setTemplateDirectory($value);

    /**
     * @return mixed
     */
    public function getTemplateDirectory();

    /**
     * @param $action
     *
     * @return mixed
     */
    public function addAction($action);

    /**
     * @return mixed
     */
    public function removeActions();

    /**
     * @return mixed
     */
    public function render();
}
