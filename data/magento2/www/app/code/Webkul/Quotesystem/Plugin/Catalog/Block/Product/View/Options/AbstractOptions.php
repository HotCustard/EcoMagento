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

namespace Webkul\Quotesystem\Plugin\Catalog\Block\Product\View\Options;

class AbstractOptions
{

    protected $quoteHelper;

    /**
     * @param \Webkul\Quotesystem\Helper\Data $quoteHelper
     */
    public function __construct(
        \Webkul\Quotesystem\Helper\Data $quoteHelper
    ) {
        $this->quoteHelper = $quoteHelper;
    }
    
    /**
     * plugin to update format price string
     *
     * @param  \Magento\Catalog\Block\Product\View\Options\AbstractOptions $subject
     * @param  [string]                                                    $result
     * @return string
     */
    public function afterGetFormatedPrice(
        \Magento\Catalog\Block\Product\View\Options\AbstractOptions $subject,
        $result
    ) {
        $showPrice = (int)$this->quoteHelper->getConfigShowPrice();
        $product = $subject->getProduct();
        $quoteStatus = $product->getQuoteStatus();
        if (!$showPrice && ($quoteStatus == 1)) {
            return '';
        }
        return $result;
    }
}
