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

class NewAction extends \FME\Productattachments\Controller\Adminhtml\Productcats
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
         $this->_forward('edit');
    }//end execute()
}//end class
