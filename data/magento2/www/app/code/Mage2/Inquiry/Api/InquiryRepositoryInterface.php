<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 12/2/19
 * Time: 6:13 PM
 */
namespace Mage2\Inquiry\Api;

use Mage2\Inquiry\Api\Data\BlockSearchResultsInterface;
use Mage2\Inquiry\Api\Data\InquiryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Inquiry CRUD interface.
 */
interface InquiryRepositoryInterface
{
    /**
     * Save Inquiry.
     *
     * @param InquiryInterface $inquiry
     * @return InquiryInterface
     * @throws LocalizedException
     */
    public function save(InquiryInterface $inquiry);

    /**
     * Retrieve Inquiry.
     *
     * @param int $inquiry_id
     * @return InquiryInterface
     * @throws LocalizedException
     */
    public function getById($inquiry_id);

    /**
     * Retrieve blocks matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return BlockSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete block.
     *
     * @param InquiryInterface $inquiry
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(Data\InquiryInterface $inquiry);

    /**
     * Delete block by ID.
     *
     * @param int $inquiry_id
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($inquiry_id);
}
