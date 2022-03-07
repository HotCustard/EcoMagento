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

class Edit extends \FME\Productattachments\Controller\Adminhtml\Productcats
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $id         = $this->getRequest()->getParam('category_id');
        $data       = $this->_objectManager->create('FME\Productattachments\Model\Productcats')->load($id)->getData();
        $session    = $this->_objectManager->get('Magento\Backend\Model\Session');
        if (isset($data['category_id']) || $id == 0) {
            $sessionData = $session->getKBaseCategoryData(true);
            $session->setKBaseCategoryData(false);
            if (is_array($sessionData)) {
                $data = array_merge($data, $sessionData);
            }
            // for compatibility with previous KB versions
            if (isset($data['category_url_key'])) {
                $data['category_url_key'] = urldecode($data['category_url_key']);
            }
            $this->_objectManager->get('Magento\Framework\Registry')->register('productattachments_productcats', $data);
            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Category') : __('New Category'),
                $id ? __('Edit Category') : __('New Category')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Category'));
            $resultPage->getConfig()->getTitle()->prepend(isset($data['category_id']) ? $data['category_name'] : __('New Category'));
            return $resultPage;
        } else {
            $session->addError(__('Category does not exist'));
            $this->_redirect('*/*/');
        }//end if
    }//end execute()
}//end class
