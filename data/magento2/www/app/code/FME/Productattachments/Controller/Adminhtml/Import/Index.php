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
namespace FME\Productattachments\Controller\Adminhtml\Import;

use \Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use FME\Productattachments\Model\ProductattachmentsFactory;

class Index extends Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * News model factory
     *
     * @var \FME\Productattachments\Model\ProductattachmentsFactory
     */
    protected $_productattachmentsFactory;
    /**
     * @param Context                   $context
     * @param Registry                  $coreRegistry
     * @param PageFactory               $resultPageFactory
     * @param ProductattachmentsFactory $productattachmentsFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ProductattachmentsFactory $productattachmentsFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry              = $coreRegistry;
        $this->_resultPageFactory         = $resultPageFactory;
        $this->_productattachmentsFactory = $productattachmentsFactory;
    }//end __construct()
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        /*
            @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productattachments::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Attachments'));

        return $resultPage;
    }//end execute()
    /**
     * News access rights checking
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Productattachments::fmeextensions_productattachments_import');
    }//end _isAllowed()
}//end class
