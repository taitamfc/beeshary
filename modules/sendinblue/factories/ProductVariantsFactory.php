<?php
/**
 * 2007-2021 Sendinblue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@sendinblue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sendinblue <contact@sendinblue.com>
 * @copyright 2007-2021 Sendinblue
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Sendinblue
 */

namespace Sendinblue\Factories;

use Emarketing\Service\Products\Variants;
use Sendinblue\Models\ProductVariant;
use Sendinblue\Services\ConfigService;

class ProductVariantsFactory
{
    /**
     * @param string $productId
     * @param mixed $languageId
     * @return array
     */
    public static function create($productId, $languageId)
    {
        $preparedVariants = [];
        try {
            $variants = (new Variants())
                ->buildVariantInformation(
                    new \ProductCore($productId),
                    $languageId,
                    \Configuration::get('PS_CURRENCY_DEFAULT')
                );

            foreach ($variants as $variant) {
                $preparedVariants[] = self::createVariant($variant);
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), ConfigService::ERROR_LEVEL);
        }

        return $preparedVariants;
    }

    /**
     * @param array $variant
     * @return array
     */
    private static function createVariant($variant)
    {
        $model = new ProductVariant();
        $model->id = $variant['id_product_attribute'];
        $model->name = $variant['attribute_designation'];
        $model->productNumber = $variant['reference'];
        $model->price = $variant['plugin_sale_price'];
        $model->oldPrice = $variant['plugin_price'];
        $model->url = $variant['url'];
        $model->discount = $variant['plugin_sale_price']
            ? $variant['plugin_price'] - $variant['plugin_sale_price']
            : $variant['plugin_price'];

        return $model->toArray();
    }
}
