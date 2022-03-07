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

namespace Webkul\Quotesystem\Plugin\Catalog\Block\Product;

class ListProduct
{

    protected $quoteHelper;

    private $_productInfo;

    /**
     * @param \Webkul\Quotesystem\Helper\Data $quoteHelper
     */
    public function __construct(
        \Webkul\Quotesystem\Helper\Data $quoteHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->quoteHelper = $quoteHelper;
        $this->repository = $productRepository;
    }
    
    public function afterGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        $result
    ) {
        $showPrice = (int)$this->quoteHelper->getConfigShowPrice();
        $id = $this->_productInfo->getEntityId();
        $quoteStatus = $this->repository->getById($id)->getQuoteStatus();
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
