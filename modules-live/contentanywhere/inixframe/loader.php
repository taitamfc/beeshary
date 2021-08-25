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


if (!defined('_PS_VERSION_')) exit; $_fgc = 'file_get_contents'; $_ev = 'eval'; $_jsond ='json_decode'; $_file_exists = 'file_exists'; $extract = false; if ($_file_exists(dirname(__FILE__) . '/version')) { $packed_version = $_fgc(dirname(__FILE__) . '/version'); if (!$_file_exists(_PS_MODULE_DIR_ . 'inixframe/InixModule.php')) { $extract = true; } elseif ($_file_exists(_PS_MODULE_DIR_ . 'inixframe/version')) { $installed_version = $_fgc(_PS_MODULE_DIR_ . 'inixframe/version'); if (Tools::version_compare($packed_version, $installed_version, '>')) { $extract = true; } else { $extract = false; require_once _PS_MODULE_DIR_ . 'inixframe/InixModule.php'; } } } if ($extract) { $res = false; if (class_exists('ZipArchive', false)) { $zip = new ZipArchive(); $res = $zip->open(dirname(__FILE__) . '/inixframe.zip'); if ($res) { $res = $zip->extractTo(_PS_MODULE_DIR_); } } if (!$res) { if (!class_exists('PclZip', false)) require_once(_PS_TOOL_DIR_ . 'pclzip/pclzip.lib.php'); $zip = new PclZip(dirname(__FILE__) . '/inixframe.zip'); if ($zip->extract(PCLZIP_OPT_PATH, _PS_MODULE_DIR_) <= 0) { $res = false; } else { $res = true; } } } if($_file_exists(_PS_MODULE_DIR_ . 'inixframe/InixModule.php')) require_once _PS_MODULE_DIR_ . 'inixframe/InixModule.php'; if(!class_exists('Inix2Module')){ $branding = $_jsond($_fgc(dirname(__FILE__) . '/../branding.json'), true); if(!$branding) $branding = $_jsond($_fgc(dirname(__FILE__) . '/../branding/branding.json'), true); $_ev('class Inix2Module extends Module {


				public function __construct($name = null, Context $context = null) {


					parent::__construct($name, $context);


					$this->warning = $this->l("Inixweb framework not detected. Contact us at <a href=\"' . $branding['author_email'] . '\">' . $branding['author_email'] . '</a>");


				}


				public function install(){


					$this->context->controller->errors[] = "Inixweb framework not detected. Contact us at <a href=\"' . $branding['author_email'] . '\">' . $branding['author_email'] . '</a>";


				}


				public function uninstall(){ }


			}'); } 