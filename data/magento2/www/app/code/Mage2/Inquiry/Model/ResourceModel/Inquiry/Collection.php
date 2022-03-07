<?php
namespace Mage2\Inquiry\Model\ResourceModel\Inquiry;

use Mage2\Inquiry\Model\Inquiry;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = Inquiry::INQUIRY_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage2\Inquiry\Model\Inquiry', 'Mage2\Inquiry\Model\ResourceModel\Inquiry');
    }
}
