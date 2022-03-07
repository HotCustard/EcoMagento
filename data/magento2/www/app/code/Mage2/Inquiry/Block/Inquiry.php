<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 5/3/19
 * Time: 5:09 PM
 */
namespace Mage2\Inquiry\Block;

use Mage2\Inquiry\Helper\Data;
use Mage2\Inquiry\Model\ResourceModel\Inquiry\Collection;
use Mage2\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Inquiry
 * @package Mage2\Inquiry\Block
 */
class Inquiry extends AbstractBlock
{
    /**
     * @var CollectionFactory
     */
    protected $inquiryCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * Inquiry constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param CollectionFactory $inquiryCollectionFactory
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        CollectionFactory $inquiryCollectionFactory,
        Data $dataHelper,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->inquiryCollectionFactory = $inquiryCollectionFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('inquiry/index/post/', ['_secure' => true]);
    }

    /**
     * @return mixed
     */
    public function getCurrentProductSku()
    {
        $product = $this->registry->registry('current_product');
        return $product->getSku();
    }

    /**
     * @return Collection
     */
    public function getInquiryCollection()
    {
        $questionDisplayCount = $this->dataHelper->getQuestionCount();

        $collection = $this->inquiryCollectionFactory->create();
        $collection->addFieldToFilter('sku', $this->getCurrentProductSku());
        $collection->addFieldToFilter('display_front', '1');
        $collection->setPageSize($questionDisplayCount);
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getQuestionDisplaySetting()
    {
        return $this->dataHelper->getQuestionDisplaySetting();
    }
}
