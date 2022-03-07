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
namespace FME\Productattachments\Controller\Adminhtml\Productcats;

class Delete extends \FME\Productattachments\Controller\Adminhtml\Productcats
{
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('category_id')) {
            try {
                $msg = $this->_objectManager->create('FME\Productattachments\Model\Productcats')->deleteCategory($id);
                if ($msg != null) {
                    $this->messageManager->addError($msg);
                } else {
                    $this->messageManager->addSuccess(__('Category was successfully deleted'));
                }
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->_objectManager->create('Magento\Framework\Logger')->logException($e);
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['category_id' => $id]);
            }
        }
        $this->_redirect('*/*/');
    }//end execute()
}//end class
