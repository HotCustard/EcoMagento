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

class Delete extends \FME\Productattachments\Controller\Adminhtml\Productattachments
{
    public function execute()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = $this->_objectManager->create('FME\Productattachments\Model\Productattachments');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                $this->messageManager->addSuccess(__('File was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }//end execute()
}//end class
