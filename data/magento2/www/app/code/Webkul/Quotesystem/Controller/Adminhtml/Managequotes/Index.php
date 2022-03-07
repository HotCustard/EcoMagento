<?php
/**
 * Quote index action admin panel.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Controller\Adminhtml\Managequotes;

use Webkul\Quotesystem\Controller\Adminhtml\Managequotes as Managequotes;
use Magento\Framework\Controller\ResultFactory;

class Index extends Managequotes
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_Quotesystem::quotes');
        $resultPage->getConfig()->getTitle()->prepend(
            __('Quote Manager')
        );
        $resultPage->addBreadcrumb(
            __('Quote Manager'),
            __('Quote Manager')
        );
        return $resultPage;
    }
}
