<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpSellerProductImage extends ObjectModel
{
    public $seller_product_id;
    public $seller_product_image_name;
    public $id_ps_image;
    public $active;

    public static $definition = array(
        'table' => 'wk_mp_seller_product_image',
        'primary' => 'id_mp_product_image',
        'fields' => array(
            'seller_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'seller_product_image_name' => array('type' => self::TYPE_STRING),
            'id_ps_image' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool'),
        ),
    );

    /**
     * Get Seller Product Images By Using Seller Product ID.
     *
     * @param int $idMpProduct Seller Product Id
     * @return array/boolean
     */
    public function getProductImageBySellerIdProduct($idMpProduct)
    {
        $productImage = Db::getInstance()->executeS('SELECT  * FROM '._DB_PREFIX_.'wk_mp_seller_product_image WHERE `seller_product_id` = '.(int) $idMpProduct);

        if (!empty($productImage)) {
            return $productImage;
        }

        return false;
    }

    /**
     * Get Seller Product Images by using Prestashop Image ID.
     *
     * @param int $idPSImage Prestashop Image ID
     * @return array/boolean
     */
    public function getProductImageByPsIdImage($idPSImage)
    {
        return Db::getInstance()->getRow('SELECT  * FROM '._DB_PREFIX_.'wk_mp_seller_product_image
                WHERE `id_ps_image` = '.(int) $idPSImage);
    }

    /**
     * Upload product images.
     *
     * @param int    $mpIdProduct  Seller Product ID
     * @param string $imageNewName Image name
     * @return bool
     */
    public static function uploadProductImage($mpIdProduct, $imageNewName)
    {
        $objMpImage = new self();
        $objMpImage->seller_product_id = $mpIdProduct;
        $objMpImage->seller_product_image_name = $imageNewName;
        if ($objMpImage->save()) {
            //if product is active then check admin configure value that product after update need to approved by admin or not
            WkMpSellerProduct::deactivateProductAfterUpdate($mpIdProduct);

            $mpProductDetails = WkMpSellerProduct::getSellerProductByIdProduct($mpIdProduct);
            if ($mpProductDetails && $mpProductDetails['active'] && $mpProductDetails['id_ps_product']) {
                //Upload images in ps product
                $objSellerProduct = new WkMpSellerProduct($mpIdProduct);
                $objSellerProduct->updatePsProductImage($mpIdProduct, $mpProductDetails['id_ps_product']);
            }

            return true;
        }

        return false;
    }

    /**
     * Delete Product images by using Image name
     * Delete image from ps product if product is active
     * If cover image deleting, make first image as a cover.
     *
     * @param string $imageName Image Name
     * @return Boolean
     */
    public static function deleteProductImage($imageName)
    {
        $objMpImage = new self();
        $imageData = $objMpImage->getProductImageByImageName($imageName);
        if ($imageData) {
            $objMpImage = new self($imageData['id_mp_product_image']);

            $mpProductDetails = WkMpSellerProduct::getSellerProductByIdProduct($imageData['seller_product_id']);

            //Delete image from ps product if product is active
            if ($objMpImage->active && $objMpImage->id_ps_image
                && $mpProductDetails && $mpProductDetails['id_ps_product']) {
                $idPsImage = $objMpImage->id_ps_image;

                $image = new Image($idPsImage);
                $status = $image->delete();

                Product::cleanPositions($idPsImage);
                $delete = Db::getInstance()->delete('image', 'id_image='.(int) $idPsImage.' and id_product='.(int) $idPsImage);
                if ($status || $delete) {
                    WkMpSellerProduct::deleteSellerProductImage(false, false, $idPsImage);

                    // if cover image deleting, make first image as a cover
                    if ($image->cover) {
                        $images = Image::getImages(Context::getContext()->language->id, $mpProductDetails['id_ps_product']);
                        if ($images) {
                            $objImage = new Image($images[0]['id_image']);
                            $objImage->cover = 1;
                            $objImage->save();
                        }
                    }
                }
            }

            if ($objMpImage->delete()) {
                $uploadDirPath = _MODULE_DIR_.'marketplace/views/img/product_img/';
                $imageFile = $uploadDirPath.$imageName;
                if (file_exists($imageFile)) {
                    unlink($imageFile);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Upload seller product imgage, profile image and shop image.
     *
     * @param file   $files             Image data that will get by $_FILES
     * @param int    $actionIdForUpload Product Id or Seller Id
     * @param string $adminupload
     */
    public static function uploadImage($files, $actionIdForUpload, $adminupload)
    {
        $context = Context::getContext();
		$context->smarty->assign(array(
			'module_dir' => _MODULE_DIR_,
			'edit_permission' => true,
			'id_mp_product' => $actionIdForUpload,
		));
        
        if (isset($files['sellerprofileimage'])) { //upload seller profile image
            $dirName = 'seller_img/';
            $imageFiles = $files['sellerprofileimage'];
            $actionType = 'sellerprofileimage';
        } elseif (isset($files['shopimage'])) { //upload shop image
            $dirName = 'shop_img/';
            $imageFiles = $files['shopimage'];
            $actionType = 'shopimage';
        } elseif (isset($files['productimage'])) {//upload seller product image
            $dirName = 'product_img/';
            $imageFiles = $files['productimage'];
            $actionType = 'productimage';
        } elseif (isset($files['profilebannerimage'])) { //upload seller profile Banner
            $dirName = 'seller_banner/';
            $imageFiles = $files['profilebannerimage'];
            $actionType = 'profilebannerimage';
        } elseif (isset($files['shopbannerimage'])) { //upload shop banner
            $dirName = 'shop_banner/';
            $imageFiles = $files['shopbannerimage'];
            $actionType = 'shopbannerimage';
        }

        if ($adminupload) {
            //$dirName = 'product_img/'; //paul
            //$imageFiles = $files; //paul
            //$actionType = 'productimage'; //paul
            $uploadDirPath = '../modules/marketplace/views/img/'.$dirName;

        } else {
            $uploadDirPath = 'modules/marketplace/views/img/'.$dirName;
        }

        $uploader = new WkMpImageUploader();
        $data = $uploader->upload($imageFiles, array(
            'actionType' => $actionType, //Maximum Limit of files. {null, Number}
            'limit' => 10, //Maximum Limit of files. {null, Number}
            'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
            'extensions' => array('jpg', 'png', 'gif', 'jpeg', ''), //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
            //'extensions' => null,
            'required' => false, //Minimum one file is required for upload {Boolean}
            'uploadDir' => $uploadDirPath, //Upload directory {String}
            'title' => array('name'), //New file name {null, String, Array} *please read documentation in README.md
        ));

        $finalData = array();
        $finalResult = false;
        if ($data['hasErrors']) {
            $finalData['status'] = 'fail';
            $finalData['file_name'] = '';
            $finalData['error_message'] = $data['errors'][0];

        } elseif ($data['isComplete']) {
            if ($data['data']['metas'][0]['name']) {
                $imageNewName = $data['data']['metas'][0]['name'];

                if ($actionType == 'productimage') {
                    // $actionIdForUpload is mpIdProduct if it is product image
                    $finalResult = self::uploadProductImage($actionIdForUpload, $imageNewName);

                    self::getProductImageDetails($actionIdForUpload);
                    //$finalData['tpl'] = $context->smarty->fetch(__DIR__.'/../views/templates/front/product/imageedit.tpl');
                    $finalData['tpl'] = $context->smarty->fetch('module:marketplace/views/templates/front/product/imageedit.tpl');
                } elseif ($actionType == 'sellerprofileimage') {
                    // $actionIdForUpload is mpIdSeller if it is seller profile image
                    $objMpSeller = new WkMpSeller($actionIdForUpload);
                    if ($objMpSeller->profile_image) { //delete old seller image if exist
                        $imageFile = $uploadDirPath.$objMpSeller->profile_image;
                        if (file_exists($imageFile)) {
                            unlink($imageFile);
                        }
                    }
                    $objMpSeller->profile_image = $imageNewName;
                    if ($objMpSeller->save()) {
                        $finalResult = true;
                    }
                } elseif ($actionType == 'shopimage') {
                    // $actionIdForUpload is mpIdSeller if it is shop image
                    $objMpSeller = new WkMpSeller($actionIdForUpload);
                    if ($objMpSeller->shop_image) { //delete old shop image if exist
                        $imageFile = $uploadDirPath.$objMpSeller->shop_image;
                        if (file_exists($imageFile)) {
                            unlink($imageFile);
                        }
                    }
                    $objMpSeller->shop_image = $imageNewName;
                    if ($objMpSeller->save()) {
                        $finalResult = true;
                    }
                } elseif ($actionType == 'profilebannerimage') {
                    // $actionIdForUpload is mpIdSeller if it is profile banner
                    $objMpSeller = new WkMpSeller($actionIdForUpload);
                    if ($objMpSeller->profile_banner) { //delete old shop image if exist
                        $imageFile = $uploadDirPath.$objMpSeller->profile_banner;
                        if (file_exists($imageFile)) {
                            unlink($imageFile);
                        }
                    }
                    $objMpSeller->profile_banner = $imageNewName;
                    if ($objMpSeller->save()) {
                        $finalResult = true;
                    }
                } elseif ($actionType == 'shopbannerimage') {
                    // $actionIdForUpload is mpIdSeller if it is shop banner
                    $objMpSeller = new WkMpSeller($actionIdForUpload);
                    if ($objMpSeller->shop_banner) { //delete old shop image if exist
                        $imageFile = $uploadDirPath.$objMpSeller->shop_banner;
                        if (file_exists($imageFile)) {
                            unlink($imageFile);
                        }
                    }
                    $objMpSeller->shop_banner = $imageNewName;
                    if ($objMpSeller->save()) {
                        $finalResult = true;
                    }
                }

                if ($finalResult == true) {
                    $finalData['status'] = 'success';
                    $finalData['file_name'] = $imageNewName;
                    $finalData['error_message'] = '';
                    $finalData['action_type'] = $actionType;
                }
            }
        }

        return $finalData;
    }

    /**
     * Get Product image by using image name from the seller product image table.
     *
     * @param string $imageName Image Name
     * @return array
     */
    public function getProductImageByImageName($imageName)
    {
        return Db::getInstance()->getRow('SELECT  * FROM '._DB_PREFIX_.'wk_mp_seller_product_image
                WHERE `seller_product_image_name` = \''.pSQL($imageName).'\'');
    }

    /**
     * Update id_ps_image and status of seller product name using Seller Product ID.
     *
     * @param int $idMpProduct Seller Product ID
     * @param int $status      1/0
     * @param int $idPSImage   Prestashop Image ID
     * @return bool
     */
    public static function updateStatusBySellerIdProduct($idMpProduct, $status, $idPSImage)
    {
        return Db::getInstance()->update(
            'wk_mp_seller_product_image',
            array(
                'active' => (int) $status,
                'id_ps_image' => (int) $idPSImage,
            ),
            '`seller_product_id` = '.(int) $idMpProduct
        );
    }

    /**
     * Update id_ps_image and status of seller product name using Seller Product ID.
     *
     * @param int $id        primary Id of table
     * @param int $status    1/0
     * @param int $idPSImage Prestashop Image ID
     * @return bool
     */
    public static function updateStatusById($id, $status, $idPSImage)
    {
        return Db::getInstance()->update(
            'wk_mp_seller_product_image',
            array(
                'active' => (int) $status,
                'id_ps_image' => (int) $idPSImage,
            ),
            '`id_mp_product_image` = '.(int) $id
        );
    }

    /**
     * Delete product image according to Mp Product Image Name.
     *
     * @param int    $idImage   primary Id of table
     * @param string $imageName Seller product image name
     * @return bool
     */
    public static function deleteProductImageByMpProductImageName($idImage, $imageName)
    {
        return Db::getInstance()->delete(
            'wk_mp_seller_product_image',
            'id_mp_product_image ='.(int) $idImage.' AND
            `seller_product_image_name` = \''.pSQL($imageName).'\''
        );
    }
    public static function setProductCoverImage($idMpProduct, $idMpImage, $is_cover)
    {
		$objImage = new Image($idMpImage);
		$objImage->cover = $cover;
		$objImage->cover = 1;
		return $objImage->save();
    }
    public static function getProductCoverImage($mpIdProduct)
    {
		$sql = 'SELECT `seller_product_image_name`
            FROM '._DB_PREFIX_.'wk_mp_seller_product_image ';
		$sql.= ' WHERE `seller_product_id` = '.(int) $mpIdProduct;
		$sql.= ' AND cover = 1';
		$productImage = Db::getInstance()->getRow($sql);
		
		if( !$productImage ){
			$sql = 'SELECT `seller_product_image_name`
            FROM '._DB_PREFIX_.'wk_mp_seller_product_image ';
			$sql.= ' WHERE `seller_product_id` = '.(int) $mpIdProduct;
			$productImage = Db::getInstance()->getRow($sql);
		}
		
        return $productImage;
    }

    /**
     * Assign and display product active/inactive images at product page.
     *
     * @param int $mpIdProduct seller product id
     * @return assign
     */
    public static function getProductImageDetails($mpIdProduct){
		$sql = 'SELECT *
            FROM '._DB_PREFIX_.'wk_mp_seller_product_image 
            WHERE `seller_product_id` = '.(int) $mpIdProduct;
		
		$productImages = Db::getInstance()->executeS($sql);
		
		if( isset( $_GET['debug'] ) ){
			echo __METHOD__;
			echo '<pre>';
			print_r( $productImages );
			echo '</pre>';
		}

		$context = Context::getContext();
		$context->smarty->assign(array(
			'image_detail' 	=> $productImages,
		));
        return $productImages;
	}	
	public static function getProductImageDetailsBK($mpIdProduct)
    {
        $context = Context::getContext();
        //Image Configuration
        $mpSellerProduct = new WkMpSellerProduct($mpIdProduct);
        $mpProduct = (array) $mpSellerProduct;
        if ($mpProduct && $mpProduct['id_ps_product']) {
            $idProduct = $mpProduct['id_ps_product'];

            $product = new Product($idProduct, false, $context->language->id);
            $productImage = $product->getImages($context->language->id);
			
			
			
            if ($productImage) {
                $imageType = Tools::getValue('image_type');
                foreach ($productImage as &$image) {
                    $objImage = new Image($image['id_image']);
                    $image['image_path'] = _THEME_PROD_DIR_.$objImage->getExistingImgPath().'.jpg';
                    $image['image_link'] = $context->link->getImageLink($product->link_rewrite, $idProduct.'-'.$image['id_image'], $imageType);
                    $image['product_image'] = $idProduct.'-'.$image['id_image'];
                }
            }
			

            // display inactive images also, if uploaded in deactive products
            if ($unactiveImage = WkMpSellerProduct::getInactiveProductImageByIdProduct($mpIdProduct)) {
                $context->smarty->assign('unactive_image', $unactiveImage);
            }


            $context->smarty->assign(array(
                'product_activated' => 1,
                'link_rewrite' => $product->link_rewrite,
                'image_detail' => $productImage,
                'id_product' => $idProduct,
            ));
        } else {
            if ($unactiveImageOnly = WkMpSellerProduct::getInactiveProductImageByIdProduct($mpIdProduct)) {
                $context->smarty->assign('unactive_image_only', $unactiveImageOnly);
            }
        }
    }

    /**
     * Get single product image by seller id
     *
     * @param int $id_seller seller id
     * @return string/boolean
     */
    public static function getProductImageBySellerIdId($id_seller)
    {
        $productImage = Db::getInstance()->getValue('SELECT `seller_product_image_name`
            FROM '._DB_PREFIX_.'wk_mp_seller_product sp
            INNER JOIN '._DB_PREFIX_.'wk_mp_seller_product_image spm ON (sp.`id_mp_product` = spm.`seller_product_id`)
            WHERE sp.`id_seller` = '.(int) $id_seller);

        return $productImage;
    }
    public static function copyMpProductImages(  $mpIdProduct,
                            $duplicateMpProductId,
                            $active )
    {
        $sql = 'SELECT *
            FROM '._DB_PREFIX_.'wk_mp_seller_product_image 
            WHERE `seller_product_id` = '.(int) $mpIdProduct;
		
		$productImages = Db::getInstance()->executeS($sql);
		return false;
		
    }
}
