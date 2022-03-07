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

namespace Webkul\Quotesystem\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\View\Element\Template;

class Render extends \Magento\Catalog\Pricing\Render
{

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;
    public $helper;
    public $context;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\Quotesystem\Helper\Data $helper
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->context = $context;
        parent::__construct($context, $registry);
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function _toHtml()
    {
        $product = $this->getProduct();
        $handle = $this->getLayout()->getUpdate()->getHandles();
        $showPrice = (int)$this->helper->getConfigShowPrice();
        $status = $product->getQuoteStatus();
        if (!($status == 1)) {
            $product = $this->helper->getProductById($product->getId());
            $status = $product->getQuoteStatus();
        }
        /**
 * @var PricingRender $priceRender
*/
        $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
        if ($priceRender instanceof PricingRender) {
            if ($product instanceof SaleableInterface) {
                $arguments = $this->getData();
                $arguments['render_block'] = $this;
                $html = $priceRender->render($this->getPriceTypeCode(), $product, $arguments);
                if (($status == 1) && !$showPrice) {
                    if (strlen($html) > 1) {
                        return $this->helper->removePriceInfo($html);
                    }
                }
                return $html;
            }
        }
        return parent::_toHtml();
    }
}
