<?php
/**
 * Define Tabs in admin.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Block\Adminhtml\Managequotes\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;
use Webkul\Quotesystem\Helper\Data as Helper;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        Helper $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('managequotes_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Quotes Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            "form_section",
            [
                "label"     =>  __("Quote Manager"),
                "alt"       =>  __("Quote Manager"),
                "content"   =>  $this->getLayout()
                    ->createBlock(\Webkul\Quotesystem\Block\Adminhtml\EditQuotes ::class)
                    ->setTemplate("Webkul_Quotesystem::form.phtml")->toHtml()
            ]
        );
        $this->addTab(
            'conversation',
            [
                'label' => __('Quote Conversation'),
                'url'   => $this->getUrl(
                    'quotesystem/*/grid',
                    ['_current' => true]
                ),
                'class' => 'ajax',
            ]
        );
        return parent::_beforeToHtml();
    }

    /**
     * Get Helper
     *
     * @return Webkul\QuoteSystem\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
