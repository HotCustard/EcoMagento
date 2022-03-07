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
namespace FME\Productattachments\Controller\Index;

class Counter extends \Magento\Framework\App\Action\Action
{  
    protected $resultJsonFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
		parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
    }
        
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id               = $this->getRequest()->getParam('id');
        $model            = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->load($id);
        $counter = $model['downloads'];
        $limit = $model['limit_downloads'];
        $result = $this->resultJsonFactory->create();
	   return $result->setData(['counter' => $counter, 'limit' => $limit]);
    }//end execute()
}//end class
