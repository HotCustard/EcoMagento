<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 20/2/19
 * Time: 5:59 PM
 */

namespace Mage2\Inquiry\Model;

use Exception;
use Mage2\Inquiry\Api\Data\InquiryInterface;
use Mage2\Inquiry\Api\Data\InquiryInterfaceFactory;
use Mage2\Inquiry\Api\Data\InquirySearchResultsInterfaceFactory;
use Mage2\Inquiry\Api\InquiryRepositoryInterface;
use Mage2\Inquiry\Model\ResourceModel\Inquiry as ResourceInquiry;
use Mage2\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory as InquiryCollectionFactory;
use Magento\Cms\Api\Data\BlockSearchResultsInterface;
use Magento\Cms\Model\ResourceModel\Block\Collection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InquiryRepository implements InquiryRepositoryInterface
{
    /**
     * @var ResourceInquiry
     */
    protected $resource;

    /**
     * @var InquiryFactory
     */
    protected $inquiryFactory;

    /**
     * @var InquiryCollectionFactory
     */
    protected $inquiryCollectionFactory;

    /**
     * @var Data\BlockSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Api\Data\InquiryInterface
     */
    protected $dataInquiryFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * InquiryRepository constructor.
     * @param ResourceInquiry $resource
     * @param InquiryFactory $inquiryFactory
     * @param InquiryInterfaceFactory $dataInquiryFactory
     * @param InquiryCollectionFactory $inquiryCollectionFactory
     * @param InquirySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceInquiry $resource,
        InquiryFactory $inquiryFactory,
        InquiryInterfaceFactory $dataInquiryFactory,
        InquiryCollectionFactory $inquiryCollectionFactory,
        InquirySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->inquiryFactory = $inquiryFactory;
        $this->dataInquiryFactory = $dataInquiryFactory;
        $this->inquiryCollectionFactory = $inquiryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @param InquiryInterface $inquiry
     * @return InquiryInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(InquiryInterface $inquiry)
    {
        if (empty($inquiry->getStoreId())) {
            $inquiry->setStoreId($this->storeManager->getStore()->getId());
        }

        try {
            $this->resource->save($inquiry);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $inquiry;
    }

    /**
     * Load Block data by given Block Identity
     *
     * @param string $inquiryId
     * @return Block
     * @throws NoSuchEntityException
     */
    public function getById($inquiryId)
    {
        $inquiry = $this->inquiryFactory->create();
        $this->resource->load($inquiry, $inquiryId);
        if (!$inquiry->getId()) {
            throw new NoSuchEntityException(__('The Inquiry with the "%1" ID doesn\'t exist.', $inquiryId));
        }
        return $inquiry;
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $criteria
     * @return BlockSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var Collection $collection */
        $collection = $this->inquiryCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\BlockSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Block
     *
     * @param InquiryInterface $inquiry
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(InquiryInterface $inquiry)
    {
        try {
            $this->resource->delete($inquiry);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Inquiry by given Inquiry Identity
     *
     * @param string $inquiryId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($inquiryId)
    {
        return $this->delete($this->getById($inquiryId));
    }
}
