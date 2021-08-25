<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpSellerWs extends WkMpWebservice
{
    /**
     * Get Seller Information
     *
     * @api sellerinfo
     * @return json
     */
    public function sellerInfo()
    {
        $seller = WkMpSeller::getSeller($this->idSeller, $this->context->language->id);
        if ($seller) {
            $seller = $this->getImageFullPath($seller);
            return $seller;
        }
        return array();
    }

    /**
     * Get images with full download URL
     * @param  array $seller
     * @return array
     */
    public function getImageFullPath($seller)
    {
        $shopURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        if (!empty($seller['profile_image'])) {
            $seller['profile_image'] = $shopURL.'modules/marketplace/views/img/seller_img/'
            .$seller['profile_image'];
        }
        if (!empty($seller['profile_banner'])) {
            $seller['profile_banner'] = $shopURL.'modules/marketplace/views/img/seller_banner/'
            .$seller['profile_banner'];
        }
        if (!empty($seller['shop_image'])) {
            $seller['shop_image'] = $shopURL.'modules/marketplace/views/img/shop_img/'
            .$seller['shop_image'];
        }
        if (!empty($seller['shop_banner'])) {
            $seller['shop_banner'] = $shopURL.'modules/marketplace/views/img/shop_banner/'
            .$seller['shop_banner'];
        }
        return $seller;
    }

    /**
     * Add a new Seller,
     * @todo I think addseller API should not be in API, only for admin
     */
    public function addSeller()
    {
        //@TODO
    }
}
