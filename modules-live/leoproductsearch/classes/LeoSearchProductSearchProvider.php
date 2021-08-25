<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Quick product search by category block
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Symfony\Component\Translation\TranslatorInterface;

// use Search;
// use Tools;
if (file_exists(_PS_MODULE_DIR_ . 'leoproductsearch/classes/ProductSearch.php')) {
    require_once(_PS_MODULE_DIR_ . 'leoproductsearch/classes/ProductSearch.php');
}

// use ProductSearch\ProductSearch;
// if (file_exists(_PS_MODULE_DIR_.'leoproductsearch/classes/ProductSearch.php')) {
// require_once(_PS_MODULE_DIR_.'leoproductsearch/classes/ProductSearch.php');
// }

class LeoSearchProductSearchProvider implements ProductSearchProviderInterface
{

    private $translator;
    private $category;
    private $sortOrderFactory;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query)
    {
        $products = array();
        $count = 0;

        if (($string = $query->getSearchString())) {
            // $class = new ProductSearch;
            // echo '<pre>';
            // print_r();die();
            $queryString = Tools::replaceAccentedChars(urldecode($string));
            $result = ProductSearch::find($context->getIdLang(), $queryString, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), false, false, null, $query->getIdCategory());
            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,
                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        } elseif (($tag = $query->getSearchTag())) {
            // $class = new ProductSearch;
            $queryString = urldecode($tag);

            $products = ProductSearch::searchTag($context->getIdLang(), $queryString, false, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(true), $query->getSortOrder()->toLegacyOrderWay(), false, null);

            $count = ProductSearch::searchTag($context->getIdLang(), $queryString, true, $query->getPage(), $query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(true), $query->getSortOrder()->toLegacyOrderWay(), false, null);

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,
                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        }

        $result = new ProductSearchResult();
        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                $this->sortOrderFactory->getDefaultSortOrders()
            );
        }

        return $result;
    }
}
