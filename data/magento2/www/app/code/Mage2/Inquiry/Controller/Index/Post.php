<?php

namespace Mage2\Inquiry\Controller\Index;

use Exception;
use Mage2\Inquiry\Api\InquiryRepositoryInterface;
use Mage2\Inquiry\Helper\Data as HelperData;
use Mage2\Inquiry\Model\InquiryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Post extends Action
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var InquiryFactory
     */
    protected $inquiryFactory;

    /**
     * @var HelperData $helperData
     */
    protected $helperData;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Post constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param InquiryFactory $inquiryFactory
     * @param InquiryRepositoryInterface $inquiryRepository
     * @param HelperData $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        InquiryFactory $inquiryFactory,
        LoggerInterface $logger,
        InquiryRepositoryInterface $inquiryRepository,
        HelperData $helperData
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inquiryFactory = $inquiryFactory;
        $this->inquiryRepository = $inquiryRepository;
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $product = $this->helperData->getProductBySku($post['sku']);
        $inquiry = $this->inquiryFactory->create();

        if (!$post && !$product->getId()) {
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        }

        try {
            $post ['status'] = 1;

            $inquiry->setData($post);
            $this->inquiryRepository->save($inquiry);

            try {
                $this->helperData->sendCustomerEmail($post);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }

            if ($this->helperData->isEmailSendToAdmin()) {
                try {
                    $this->helperData->sendAdminEmail($post);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
            $this->messageManager->addSuccess(__('Thank you for Enquiry , Our team will contact you soon. '));
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        } catch (Exception $e) {
            $this->messageManager->addError(__('We can\'t process your inquiry right now. Sorry, please contact support team.'));
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        }
    }
}
