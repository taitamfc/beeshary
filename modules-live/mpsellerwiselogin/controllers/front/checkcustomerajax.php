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

class MpSellerWiseLoginCheckCustomerAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;

        $case = Tools::getValue('case');

        if ($case == 'checkEmailRegister') {
            $email = Tools::getValue('user_email');
            $customer_id = Customer::customerExists($email, true);
            $result = false;

            if ($customer_id) {
                $objMpSeller = new WkMpSeller();
                $idSeller = (int)Tools::getValue('id_seller');
                $sellerDetail = $objMpSeller->getSellerDetailByCustomerId((int)$customer_id);
                if ($sellerDetail) {
                    $idSeller = $sellerDetail['id_seller'];
                } else {
                    $idSeller = 0;
                }

                $result = [
                    'idSeller' => $idSeller,
                    'idCustomer' => $customer_id,
                ];
				
				/* custom data */
				if ($this->context->cookie->logged) {
					$result = false;
				}
            }

            die(Tools::jsonEncode($result));
        } elseif ($case == 'checkUniqueShopName') {
            WkMpSeller::validateSellerUniqueShopName();
        } elseif ($case == 'getSellerState') {
            WkMpSeller::displayStateByCountryId();
        } elseif ($case == 'getCityByPostCode') {
            $res = WkMpSeller::getCityByPostCode(Tools::getValue('post_code'));
            if ($res) {
                die(Tools::jsonEncode(['city' => $res]));
            }
            die(Tools::jsonEncode([false]));
        }
    }
}
