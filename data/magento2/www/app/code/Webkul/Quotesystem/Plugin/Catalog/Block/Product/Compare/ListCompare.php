<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Plugin\Catalog\Block\Product\Compare;

use Magento\Catalog\Block\Product\Compare\ListCompare as Compare;

class ListCompare
{
    /**
     * @var \Webkul\Quotesystem\Helper\Data
     */
    private $_quotesystemHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Webkul\Preorder\Helper\Data $preorderHelper
     */
    public function __construct(
        \Webkul\Quotesystem\Helper\Data $helper
    ) {
        $this->_quotesystemHelper = $helper;
    }

    public function aroundGetProductPrice(
        Compare $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $idSuffix
    ) {
        $result = $proceed($product, $idSuffix);
        $isQuoted = $product->getQuoteStatus();
        if ($isQuoted == 1) {
            $showPrice = (int)$this->_quotesystemHelper->getConfigShowPrice();
            $button = "<div class='actions-primary quote_button'>
                            <a href='".$product->getProductUrl()."'>
                                <span title='Add Quote' class='action toquote primary'>
                                    <span>Add Quote</span>
                                </span>
                            </a>
                        </div>";
            if (!$showPrice) {
                return $button;
            }
            $result = $result.$button;
        }
        return $result;
    }
}
