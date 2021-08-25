<?php
class Hook extends HookCore
{
    public static function getHookModuleExecList($hook_name = null)
    {
        include_once dirname(__FILE__).'/../../modules/marketplace/classes/WkMpRequiredClasses.php';
        include_once dirname(__FILE__).'/../../modules/mpsellermembership/classes/MembershipClassInclude.php';
        include_once dirname(__FILE__).'/../../modules/mpsellermembership/mpsellermembership.php';
        $list = parent::getHookModuleExecList($hook_name);

        if ($hook_name == 'paymentOptions') {
            $context = Context::getContext();
            $cart_product_list = $context->cart->getProducts();
            if ($cart_product_list) {
                $membershipProducts = 0;
                foreach ($cart_product_list as $cart_product) {
                    if (MarketplaceSellerplan::getPlanByIdProduct($cart_product['id_product'])) {
                        if ($cart_product['cart_quantity'] > 1) {
                            return false;
                        }
                        $membershipProducts++;
                    }
                }
            }

            if ($membershipProducts > 1) {
                return false;
            }
        }
        return $list;
    }
}
