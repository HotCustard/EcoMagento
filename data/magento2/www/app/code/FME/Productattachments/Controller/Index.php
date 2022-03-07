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
namespace FME\Productattachments\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

abstract class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;
    protected $customerSession;
    protected $urlBuilder;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @param Context     $context
     * @param Session     $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $resultPageFactory
    ) {
         $this->customerSession = $customerSession;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }//end __construct()
    public function dispatch(RequestInterface $request)
    {
        $login_before_download = $this->_objectManager->create('FME\Productattachments\Helper\Data')->loginToDownload();
        if ($login_before_download) {
             if (!$this->customerSession->isLoggedIn()) 
            {
                 $this->messageManager->addError('You Must Login Before View or Download Files');
             //$this->customerSession->setAfterAuthUrl($this->urlBuilder->getCurrentUrl());
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
            }
        }
        $result = parent::dispatch($request);
        return $result;
    }//end dispatch()
}//end class
