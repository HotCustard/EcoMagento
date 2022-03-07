<?php
/**
 * Quote Edit controller, admin panel.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Controller\Adminhtml\Managequotes;

use Magento\Backend\App\Action;
use Webkul\Quotesystem\Model\QuotesFactory;
use Webkul\Quotesystem\Api\QuoteRepositoryInterface;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var Webkul\Quotesystem\Model\QuotesFactory
     */
    protected $_quotesFactory;
    /**
     * @var Webkul\Quotesystem\Api\QuoteRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param QuotesFactory                              $quotesFactory
     * @param QuoteRepositoryInterface                   $quoteRepository
     * @param Magento\Backend\Model\Session              $adminSession
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        QuotesFactory $quotesFactory,
        QuoteRepositoryInterface $quoteRepository,
        \Magento\Backend\Model\Session $adminSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_quotesFactory = $quotesFactory;
        $this->_quoteRepository = $quoteRepository;
        $this->adminSession = $adminSession;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Webkul_Quotesystem::quotes'
        );
    }
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Quotesystem::quotes')
            ->addBreadcrumb(__('Manage Quotes'), __('Manage Quotes'));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->_quotesFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getEntityId()) {
                $this->messageManager->addError(
                    __('This quote no longer exists.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->adminSession->getFormData(true);
        if (!empty($data) || $id) {
            $model->setData($data);
            $this->_coreRegistry->register('quote_data', $model);
            $resultPage = $this->_initAction();
            if ($this->quoteStatusIsNotSold($model->getStatus())) {
                $resultPage->addBreadcrumb(__('Edit Quote'), __('Edit Quote'));
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Quote'));
            } else {
                $resultPage->addBreadcrumb(__('View Quote'), __('View Quote'));
                $resultPage->getConfig()->getTitle()->prepend(__('View Quote'));
            }
            $resultPage->addContent(
                $resultPage->getLayout()->createBlock(
                    \Webkul\Quotesystem\Block\Adminhtml\Managequotes\Edit ::class
                )
            );
            $resultPage->addLeft(
                $resultPage->getLayout()->createBlock(
                    \Webkul\Quotesystem\Block\Adminhtml\Managequotes\Edit\Tabs ::class
                )
            );
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('quotesystem/managequotes/index');
        }
    }
    public function quoteStatusIsNotSold($quoteStatus)
    {
        if ($quoteStatus!=\Webkul\Quotesystem\Model\Quotes::STATUS_SOLD) {
            return true;
        }
        return false;
    }
}
