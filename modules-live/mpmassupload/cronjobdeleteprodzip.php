<?php
/**
 * 2010-2018 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through this link for complete license : https://store.webkul.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
 *
 *  @author    Webkul IN <support@webkul.com>
 *  @copyright 2010-2018 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

include_once dirname(__FILE__).'/../../config/config.inc.php';
include_once 'mpmassupload.php';

class CronJobDeleteProdZip
{
    /**
     * InitContent function.
     */
    public function __construct()
    {
        $zip_file_path = _PS_MODULE_DIR_.'mpmassupload/views/export_csv/';
        MarketplaceMassUpload::recursiveRemove($zip_file_path);
    }
}
new CronJobDeleteProdZip();
