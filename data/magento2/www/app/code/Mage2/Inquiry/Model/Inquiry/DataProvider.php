<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 14/2/19
 * Time: 4:56 PM
 */
namespace Mage2\Inquiry\Model\Inquiry;

use Mage2\Inquiry\Model\Inquiry;
use Mage2\Inquiry\Model\ResourceModel\Inquiry\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $inquiryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $inquiryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $inquiryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Inquiry $inquiry */
        foreach ($items as $inquiry) {
            $this->loadedData[$inquiry->getId()] = $inquiry->getData();
        }

        $data = $this->dataPersistor->get('mage2_inquiry');
        if (!empty($data)) {
            $inquiry = $this->collection->getNewEmptyItem();
            $inquiry->setData($data);
            $this->loadedData[$inquiry->getId()] = $inquiry->getData();
            $this->dataPersistor->clear('mage2_inquiry');
        }

        return $this->loadedData;
    }
}
