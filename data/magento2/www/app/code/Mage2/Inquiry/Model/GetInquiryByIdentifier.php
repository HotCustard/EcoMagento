<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 20/2/19
 * Time: 1:04 PM
 */
namespace Mage2\Inquiry\Model;

use Mage2\Inquiry\Api\GetInquiryByIdentifierInterface;
use Mage2\Inquiry\Api\Data\InquiryInterface;
use Mage2\Inquiry\Model\InquiryFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetBlockByIdentifier
 */
class GetInquiryByIdentifier implements GetInquiryByIdentifierInterface
{
    /**
     * @var InquiryFactory
     */
    private $inquiryFactory;

    /**
     * @var ResourceModel\Inquiry
     */
    private $inquiryResource;

    /**
     * @param inquiryFactory $inquiryFactory
     * @param ResourceModel\Inquiry $inquiryResource
     */
    public function __construct(
        InquiryFactory $inquiryFactory,
        \Mage2\Inquiry\Model\ResourceModel\Inquiry $inquiryResource
    ) {
        $this->inquiryFactory = $inquiryFactory;
        $this->inquiryResource = $inquiryResource;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $identifier, int $storeId) : InquiryInterface
    {
        $inquiry = $this->inquiryFactory->create();
        $inquiry->setStoreId($storeId);
        $this->inquiryResource->load($inquiry, $identifier, InquiryInterface::IDENTIFIER);

        if (!$inquiry->getId()) {
            throw new NoSuchEntityException(__('The Inquiry with the "%1" ID doesn\'t exist.', $identifier));
        }

        return $inquiry;
    }
}
