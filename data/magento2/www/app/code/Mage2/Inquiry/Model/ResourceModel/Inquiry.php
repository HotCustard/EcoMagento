<?php
namespace  Mage2\Inquiry\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Inquiry post mysql resource
 */
class Inquiry extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('mage2_inquiry', 'inquiry_id');
    }
}
