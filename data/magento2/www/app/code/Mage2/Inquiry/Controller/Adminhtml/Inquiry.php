<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 14/2/19
 * Time: 12:43 PM
 */

namespace Mage2\Inquiry\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Inquiry extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mage2_Inquiry::inquiry';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(Context $context, Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mage2_Inquiry::inquiry')
            ->addBreadcrumb(__('Mage2'), __('Mage2'))
            ->addBreadcrumb(__('Manage Inquiry'), __('Manage Inquiry'));
        return $resultPage;
    }
}
