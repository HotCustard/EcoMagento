<?php
/**
 * Column field for sender name in quote conversation grid
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Block\Adminhtml\Managequotes\Grid;

use Webkul\Quotesystem\Helper\Data;
use Webkul\Quotesystem\Api\QuoteRepositoryInterface;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class RendererSenderName extends AbstractRenderer
{
    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = [];
    /**
     * @var Webkul\Quotesystem\Helper\Data
     */
    protected $_quoteHelper;
    /**
     * @var Webkul\Quotesystem\Api\QuoteRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param Data                           $quoteHelper
     * @param QuoteRepositoryInterface       $quoteRepository
     * @param array                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        Data $quoteHelper,
        QuoteRepositoryInterface $quoteRepository,
        \Magento\Framework\Escaper $escaper,
        array $data = []
    ) {
        $this->_quoteHelper = $quoteHelper;
        $this->_quoteRepository = $quoteRepository;
        $this->escaper = $escaper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_actions = [];
        $actions = [];
        if ($row->getSender() == 0) {
            $actions[] = __('Admin');
        } else {
            $quoteId = $row->getQuoteId();
            $quote = $this->_quoteRepository->getById($quoteId);
            $senderData = $this->_quoteHelper->getCustomerData(
                $row->getSender()
            );
            $actions[] = $senderData->getName();
        }
        $this->addToActions($actions);
        return $this->_actionsToHtml();
    }

    /**
     * Render options array as a HTML string
     *
     * @param  array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions = [])
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();

        if (empty($actions)) {
            $actions = $this->_actions;
        }
        foreach ($actions[0] as $action) {
            $html[] = '<span>' . $action . '</span>';
        }
        return implode('', $html);
    }

    /**
     * Add one action array to all options data storage
     *
     * @param  array $actionArray
     * @return void
     */
    public function addToActions($actionArray)
    {
        $this->_actions[] = $actionArray;
    }
}
