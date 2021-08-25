<?php

 /**

 * Prestashop Modules & Themen End User License Agreement
 *
 * This End User License Agreement ("EULA") is a legal agreement between you and Presta-Apps ltd.
 * ( here in referred to as "we" or "us" ) with regard to Prestashop Modules & Themes
 * (herein referred to as "Software Product" or "Software").
 * By installing or using the Software Product you agree to be bound by the terms of this EULA.
 *
 * 1. Eligible Licensees. This Software is available for license solely to Software Owners,
 * with no right of duplication or further distribution, licensing, or sub-licensing.
 * A Software Owner is someone who legally obtained a copy of the Software Product via Prestashop Store.
 *
 * 2. License Grant. We grant you a personal/one commercial, non-transferable and non-exclusive right to use the copy
 * of the Software obtained via Prestashop Store. Modifying, translating, renting, copying, transferring or assigning
 * all or part of the Software, or any rights granted hereunder, to any other persons and removing any proprietary
 * notices, labels or marks from the Software is strictly prohibited. Furthermore, you hereby agree not to create
 * derivative works based on the Software. You may not transfer this Software.
 *
 * 3. Copyright. The Software is licensed, not sold. You acknowledge that no title to the intellectual property in the
 * Software is transferred to you. You further acknowledge that title and full ownership rights to the Software will
 * remain the exclusive property of Presta-Apps Mobile, and you will not acquire any rights to the Software,
 * except as expressly set forth above.
 *
 * 4. Reverse Engineering. You agree that you will not attempt, and if you are a corporation,
 * you will use your best efforts to prevent your employees and contractors from attempting to reverse compile, modify,
 * translate or disassemble the Software in whole or in part. Any failure to comply with the above or any other terms
 * and conditions contained herein will result in the automatic termination of this license.
 *
 * 5. Disclaimer of Warranty. The Software is provided "AS IS" without warranty of any kind. We disclaim and make no
 * express or implied warranties and specifically disclaim the warranties of merchantability, fitness for a particular
 * purpose and non-infringement of third-party rights. The entire risk as to the quality and performance of the Software
 * is with you. We do not warrant that the functions contained in the Software will meet your requirements or that the
 * operation of the Software will be error-free.
 *
 * 6. Limitation of Liability. Our entire liability and your exclusive remedy under this EULA shall not exceed the price
 * paid for the Software, if any. In no event shall we be liable to you for any consequential, special, incidental or
 * indirect damages of any kind arising out of the use or inability to use the software.
 *
 * 7. Rental. You may not loan, rent, or lease the Software.
 *
 * 8. Updates and Upgrades. All updates and upgrades of the Software from a previously released version are governed by
 * the terms and conditions of this EULA.
 *
 * 9. Support. Support for the Software Product is provided by Presta-Apps ltd. For product support, please send an
 * email to support at info@iniweb.de
 *
 * 10. No Liability for Consequential Damages. In no event shall we be liable for any damages whatsoever
 * (including, without limitation, incidental, direct, indirect special and consequential damages, damages for loss
 * of business profits, business interruption, loss of business information, or other pecuniary loss) arising out of
 * the use or inability to use the Software Product. Because some states/countries do not allow the exclusion or
 * limitation of liability for consequential or incidental damages, the above limitation may not apply to you.
 *
 * 11. Indemnification by You. You agree to indemnify, hold harmless and defend us from and against any claims or
 * lawsuits, including attorney's fees that arise or result from the use or distribution of the Software in violation
 * of this Agreement.
 *
 * @author    Presta-Apps Limited
 * @website   www.presta-apps.com
 * @contact   info@presta-apps.com
 * @copyright 2009-2016 Presta-Apps Ltd.
 * @license   Proprietary

 */


require dirname(__FILE__).'/inixframe/loader.php'; require dirname(__FILE__).'/classes/Anywhere.php'; class contentanywhere extends Inix2Module { function __construct(){ $this->name = 'contentanywhere'; $this->tab = 'others'; $this->version = '2.0.2'; $this->displayName = 'Content Anywhere'; $this->description = 'Add Rich text content anywhere'; $this->need_instance = 0; $this->ps_versions_compliancy = array('min' => '1.5.1.0', 'max' => '1.7'); parent::__construct(); if(!$this->context->controller instanceof AdminModulesController){ $this->bootstrap = true; return; } } public function getContent() { $this->className= 'Anywhere'; $this->object_table ='anywhere'; $this->object_identifier = 'id_anywhere'; $this->lang = true; $this->fields_list = array( 'id_anywhere'=> array( 'title'=> 'ID', 'width'=> 40, ), 'content'=> array( 'title'=> 'content', 'callback'=>'cleanContent' ), 'active' => array( 'title' => $this->l('Displayed'), 'active' => 'status', 'align' => 'center', 'type' => 'bool', 'width' => 70, 'orderby' => false ) ); $this->addRowAction('edit'); $this->addRowAction('Delete'); return parent::getContent(); } public function cleanContent($v,$tr){ return Tools::substr(Tools::safeOutput($v,false),0,150); } public function install() { $hooks_all = Hook::getHooks(); $hooks= array(); foreach ($hooks_all as $k => $h) { if(substr($h['name'],0,strlen('display')) == 'display' AND( $h['name'] !='displayHeader' OR $h['name'] !='displayBackOfficeHeader' OR substr($h['name'],0,strlen('displayAdminStats')) == 'displayAdminStats') ) $hooks[] = $h['name']; } $this->install_hooks = $hooks; return parent::install(); } public function renderForm() { $hooks_all = Hook::getHooks(); $hooks= array(); foreach ($hooks_all as $k => $h) { if(substr($h['name'],0,strlen('display')) == 'display' AND ( $h['name'] !='displayHeader' OR $h['name'] !='displayBackOfficeHeader' OR substr($h['name'],0,strlen('displayAdminStats')) == 'displayAdminStats') ) { $hooks[] = $h; } } $this->fields_form = array( 'legend' => array( 'title' => Validate::isLoadedObject($this->object)? $this->l('Edit content') : $this->l('Add content'), ), 'input' => array( array( 'type' => 'textarea', 'label' => $this->l('Content'), 'name' => 'content', 'lang' => true, 'size' => 48, 'required' => true, 'autoload_rte' => true ), array( 'label' => $this->l('Select Hook'), 'name' => 'hook', 'type' => 'select', 'required' => true, 'options' => array( 'default' => array('value' => '!!!', 'label' => '--'), 'query' => $hooks, 'id' => 'name', 'name' => 'title' ), ), array( 'type' => 'switch', 'label' => $this->l('Displayed'), 'required' => true, 'name' => 'active', 'class' => 't', 'is_bool' => true, 'values' => array( array( 'id' => '_on', 'value' => 1, 'label' => $this->l('Yes') ), array( 'id' => '_off', 'value' => 0, 'label' => $this->l('No') ) ), 'default_value'=>1, ), ), 'submit' => array( 'title' => $this->l('Save'), ) ); return parent::renderForm(); } function __call($name, $arguments) { if(is_null(self::$contents)){ $contents = DB::getInstance()->executeS('SELECT *


				FROM `'._DB_PREFIX_.'anywhere` a


				LEFT JOIN `'._DB_PREFIX_.'anywhere_lang` al ON (al.id_anywhere  = a.id_anywhere)


				WHERE al.id_lang = '.$this->context->language->id); foreach($contents as $c){ self::$contents[$c['hook']][] = $c; } } $hook_name = str_replace('hook', '', $name); if(isset(self::$contents[$hook_name])){ $this->context->smarty->assign('anywhere_content',self::$contents[$hook_name]); return $this->display(__FILE__,'anywhere.tpl'); } } public static $contents; } 