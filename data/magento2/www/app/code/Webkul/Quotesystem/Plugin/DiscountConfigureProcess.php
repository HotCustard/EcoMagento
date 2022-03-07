<?php
/**
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Plugin;

/**
 * Class DiscountConfigureProcess
 *
 * Removes discount block when wallet amount product is in cart.
 */
class DiscountConfigureProcess
{
    /**
     * @var \Webkul\Quotesystem\Helper\Data
     */
    private $quoteHelper;

    /**
     * @param \Webkul\Quotesystem\Helper\Data $quoteHelper
     */
    public function __construct(
        \Webkul\Quotesystem\Helper\Data $quoteHelper
    ) {
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * Checkout LayoutProcessor before process plugin.
     *
     * @param                                         \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param                                         array                                            $jsLayout
     * @return                                        array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $LayoutProcessor,
        callable $proceed,
        $jsLayout
    ) {
        $jsLayout = $proceed($jsLayout);
        if (!$this->quoteHelper->getDiscountEnable() && $this->quoteHelper->checkQuoteProductIsInCart()) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']
            );
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['afterMethods']['children']['reward_amount']
            );
        }
        return $jsLayout;
    }
}
