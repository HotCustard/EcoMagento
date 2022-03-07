<?php
/**
 * Copyright © Mage2 Developer, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mage2\Inquiry\Controller\Adminhtml\inquiry;

use Exception;
use Mage2\Inquiry\Model\InquiryRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var ResourceInquiry
     */
    protected $inquiryRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param InquiryRepository $inquiryRepository
     */
    public function __construct(
        Context $context,
        inquiryRepository $inquiryRepository
    ) {
        $this->inquiryRepository = $inquiryRepository;
        parent::__construct($context);
    }
    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('inquiry_id');
        if ($id) {
            try {
                $this->inquiryRepository->deleteById($id);
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['inquiry_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
