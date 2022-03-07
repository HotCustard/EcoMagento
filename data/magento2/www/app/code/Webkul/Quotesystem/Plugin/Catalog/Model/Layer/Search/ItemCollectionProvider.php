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

namespace Webkul\Quotesystem\Plugin\Catalog\Model\Layer\Search;

class ItemCollectionProvider
{

    public function afterGetCollection(
        \Magento\Catalog\Model\Layer\Search\ItemCollectionProvider $subject,
        $result
    ) {
        return $result->addAttributeToSelect('quote_status');
    }
}
