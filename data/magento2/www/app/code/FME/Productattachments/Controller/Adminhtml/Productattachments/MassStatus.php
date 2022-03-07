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

class MassStatus extends \FME\Productattachments\Controller\Adminhtml\Productattachments
{  
    public function execute()
    {       
        $productattachmentsIds = $this->getRequest()->getParam('productattachments');
        if (!is_array($productattachmentsIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($productattachmentsIds as $productattachmentsId) {
                    $productattachments = $this->_objectManager->get('FME\Productattachments\Model\Productattachments')
                        ->load($productattachmentsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %d record(s) were successfully updated', count($productattachmentsIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
