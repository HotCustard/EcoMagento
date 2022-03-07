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
namespace Webkul\Quotesystem\Plugin\Product;

class ListProduct
{

    protected $quoteHelper;

    private $_productInfo;

    /**
     * @param \Webkul\Quotesystem\Helper\Data $quoteHelper
     */
    public function __construct(
        \Webkul\Quotesystem\Helper\Data $quoteHelper
    ) {
        $this->quoteHelper = $quoteHelper;
    }

    public function afterGetProductPrice(
        \Webkul\Quotesystem\Block\Product\ListProduct $subject,
        $result
    ) {
        $showPrice = (int)$this->quoteHelper->getConfigShowPrice();
        $quoteStatus = $this->_productInfo->getQuoteStatus();
        if (($quoteStatus == 1) && !$showPrice) {
            return $this->quoteHelper->removePriceInfo($result);
        }
        return $result;
    }

    /**
     * beforeGetProductPrice plugin to assign the product model to a variable
     *
     * @param  \Magento\Checkout\Block\Cart\Crosssell $crosssell
     * @param  \Magento\Catalog\Model\Product         $product
     * @return \Magento\Catalog\Model\Product
     */
    public function beforeGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->_productInfo = $product ;
    }
}
