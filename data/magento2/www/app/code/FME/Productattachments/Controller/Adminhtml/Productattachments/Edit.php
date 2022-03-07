<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_Productattachments
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
namespace FME\Productattachments\Controller\Adminhtml\Productattachments;

class Edit extends \FME\Productattachments\Controller\Adminhtml\Productattachments
{  
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $id     = $this->getRequest()->getParam('id');
        $model  = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->load($id);
        if ($model->getId() || $id == 0) {
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $this->_coreRegistry->register('productattachments_data', $model);
            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Attachment') : __('New Attachment'),
                $id ? __('Edit Attachment') : __('New Attachment')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Productattachments'));
            $resultPage->getConfig()->getTitle()
            ->prepend($model->getProductattachmentsId() ? $model->getTitle() : __('New Attachment'));

            return $resultPage;
        } else {
            $this->messageManager->addError($this->_objectManager->get('FME\Productattachments\Helper\Data')->__('File does not exist'));
            $this->_redirect('*/*/');
        }
    }
}
