<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 20/2/19
 * Time: 12:51 PM
 */
namespace Mage2\Inquiry\Api;

use Mage2\Inquiry\Api\Data\InquiryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Command to load the block data by specified identifier
 * @api
 */
interface GetInquiryByIdentifierInterface
{
    /**
     * Load inquiry data by given block identifier.
     *
     * @param string $identifier
     * @param int $storeId
     * @throws NoSuchEntityException
     * @return InquiryInterface
     */
    public function execute(string $identifier, int $storeId) : InquiryInterface;
}
