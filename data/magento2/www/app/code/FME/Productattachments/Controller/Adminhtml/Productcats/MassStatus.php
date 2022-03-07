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

class MassStatus extends \FME\Productattachments\Controller\Adminhtml\Productcats
{
    public function execute()
    {
        $productcatsIds = $this->getRequest()->getParam('productcats');
        if (!is_array($productcatsIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($productcatsIds as $productcatsId) {
                    $productcats = $this->_objectManager->get('FME\Productattachments\Model\Productcats')
                            ->load($productcatsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %d record(s) were successfully updated', count($productcatsIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
