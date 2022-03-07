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
namespace Webkul\Quotesystem\Plugin\Checkout\Model;

use Magento\Framework\Exception\LocalizedException;

class Cart
{
    protected $quoteHelper;

    public function __construct(
        \Webkul\Quotesystem\Helper\Data $quoteHelper
    ) {
        $this->quoteHelper = $quoteHelper;
    }

    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        $quoteStatus = $productInfo->getQuoteStatus();
        $allowAddToCart = (int)$this->quoteHelper->getConfigAddToCart();
        if (($quoteStatus == 1) && !$allowAddToCart) {
            if (array_key_exists('quote_id', $requestInfo)) {
                return [$productInfo, $requestInfo];
            }
            throw new LocalizedException(__('Add to cart for this product is not allowed'));
        }
        return [$productInfo, $requestInfo];
    }
}
