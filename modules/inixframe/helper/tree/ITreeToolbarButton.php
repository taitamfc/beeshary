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
 * Interface IInix2TreeToolbarButton
 */
interface IInix2TreeToolbarButton
{
    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function setAttribute($name, $value);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getAttribute($name);

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setAttributes($value);

    /**
     * @return mixed
     */
    public function getAttributes();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setClass($value);

    /**
     * @return mixed
     */
    public function getClass();

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
    public function setId($value);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setLabel($value);

    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setName($value);

    /**
     * @return mixed
     */
    public function getName();

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
     * @param $name
     *
     * @return mixed
     */
    public function hasAttribute($name);

    /**
     * @return mixed
     */
    public function render();
}
