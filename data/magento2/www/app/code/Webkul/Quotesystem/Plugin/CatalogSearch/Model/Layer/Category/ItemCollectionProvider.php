<?php
/**
 * Webkul
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Plugin\CatalogSearch\Model\Layer\Category;

class ItemCollectionProvider
{
    public function afterGetCollection(
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $subject,
        $result
    ) {
        $collection = $result->addAttributeToSelect('quote_status');
        return $collection;
    }
}
