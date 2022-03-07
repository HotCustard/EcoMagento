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

class Index extends \FME\Productattachments\Controller\Adminhtml\Productattachments
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productattachments::fmeextensions_productattachments_items');
        $resultPage->addBreadcrumb(__('Productattachments'), __('Productattachments'));
        $resultPage->addBreadcrumb(__('Manage Attachments'), __('Manage Attachments'));
        $resultPage->getConfig()->getTitle()->prepend(__('Attachments'));
        return $resultPage;
    }//end execute()
}//end class
