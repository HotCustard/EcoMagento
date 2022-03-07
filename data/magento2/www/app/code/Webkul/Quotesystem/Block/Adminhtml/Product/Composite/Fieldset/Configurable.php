<?php
/**
 * Block for Quote list at admin end.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Block\Adminhtml\Product\Composite\Fieldset;

use \Magento\ConfigurableProduct\Block\Adminhtml\Product\Composite\Fieldset\Configurable as Conf;

class Configurable extends Conf
{
    
    /**
     * Get Helper
     *
     * @return\Magento\Catalog\Helper\Product
     */
    public function getHelper()
    {
        return $this->catalogProduct;
    }

    /**
     * Get Json Helper
     *
     * @return \Magento\Framework\Json\EncoderInterface
     */
    public function getJsonHelper()
    {
        return $this->jsonEncoder;
    }
}
