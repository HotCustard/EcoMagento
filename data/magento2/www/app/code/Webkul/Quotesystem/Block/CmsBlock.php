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

namespace Webkul\Quotesystem\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class CmsBlock extends Template
{
    /**
     * Webkul\Quotesystem\Helper\Data
     *
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $pixelHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Webkul\Quotesystem\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * get Helper
     *
     * @return Webkul\Quotesystem\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Get Json Helper
     *
     * @return Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }
}
