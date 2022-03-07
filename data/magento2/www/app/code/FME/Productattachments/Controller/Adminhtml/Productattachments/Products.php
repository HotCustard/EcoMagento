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

class Products extends \FME\Productattachments\Controller\Adminhtml\Productattachments
{
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $this->_initProductAttachments();
        $resultLayout->getLayout()->getBlock('productattachments.edit.tab.products')
                ->setRelatedProducts($this->getRequest()->getPost('products_related', null));
        return $resultLayout;
    }
}
