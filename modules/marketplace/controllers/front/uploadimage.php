<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

require_once( _PS_MODULE_DIR_.'marketplace/classes/CustomUpload.php' );
class MarketplaceUploadImageModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;

        if (Tools::getValue('action') == 'uploadimage') {
            // Upload image
            if (Tools::getValue('actionIdForUpload')) {
                $actionIdForUpload = Tools::getValue('actionIdForUpload'); //it will be Product Id OR Seller Id
                $adminupload = Tools::getValue('adminupload'); //if uploaded by Admin from backend

                $finalData = WkMpSellerProductImage::uploadImage($_FILES, $actionIdForUpload, $adminupload);
				
				if( $finalData['status'] == 'success' && $finalData['action_type'] == 'sellerprofileimage' ){
					
					$is_partner = false;
					if (isset($this->context->customer->id)) {
						$customer	= $this->context->customer;
						if( $customer->optin == 1 ){
							$is_partner = true;
						}
					}
					if( $is_partner ){
						/*
						$handle = new CustomUpload( _PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$finalData['file_name'] );
						if ($handle->uploaded) {
							$handle->file_overwrite 	= true ;
							$handle->image_watermark 	= _PS_MODULE_DIR_.'marketplace/views/img/watermark.png' ;
						}
						$handle->process( _PS_MODULE_DIR_.'marketplace/views/img/seller_img/' );
						*/
					}
				}
                echo Tools::jsonEncode($finalData);
            }
        } else if (Tools::getValue('action') == 'deleteimage' && Tools::getValue('actionpage') == 'product') {
            //Delete image (This action works only on Product page)
            $imageName = Tools::getValue('image_name');
            if ($imageName) {
                WkMpSellerProductImage::deleteProductImage($imageName);
            }
        }

        die; //ajax close
    }
}
