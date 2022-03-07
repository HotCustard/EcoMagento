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

namespace Webkul\Quotesystem\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlockForDiscount implements ObserverInterface
{
    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $quoteHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Quotesystem\Helper\Data $quoteHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->quoteHelper = $quoteHelper;
    }

    public function execute(Observer $observer)
    {
        /**
 * @var \Magento\Framework\View\Layout $layout
*/
        $layout = $observer->getLayout();
        $block = $layout->getBlock('checkout.cart.coupon');

        if ($block) {
            if (!$this->quoteHelper->getDiscountEnable() && $this->quoteHelper->checkQuoteProductIsInCart()) {
                $layout->unsetElement('checkout.cart.coupon');
            }
        }
        return $this;
    }
}
