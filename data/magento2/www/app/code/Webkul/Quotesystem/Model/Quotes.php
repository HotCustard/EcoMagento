<?php
/**
 * Quotes Model
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Model;

use Webkul\Quotesystem\Api\Data\QuoteInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Quotes extends AbstractModel implements QuoteInterface, IdentityInterface
{
    const CACHE_TAG = 'quotesystem_quotes';
    const STATUS_UNAPPROVED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_DECLINE = 3;
    const STATUS_SOLD = 4;

    /**
     * @var string
     */
    protected $_cacheTag = 'quotesystem_quotes';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'quotesystem_quotes';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Quotesystem\Model\ResourceModel\Quotes ::class);
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITYID);
    }
    /**
     * Set ID
     *
     * @return \Webkul\Quotesystem\Model\Quotes
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITYID, $entityId);
    }

    /**
     * Get attachments
     *
     * @return string|null
     */
    public function getAttachments()
    {
        return $this->getData(self::ATTACHMENTS);
    }
    
    /**
     * set attachments
     *
     * @param string $attachments
     *
     * @return \Webkul\Quotesystem\Api\Data\QuoteInterface
     */
    public function setAttachments($attachments)
    {
        return $this->setData(self::ATTACHMENTS, $attachments);
    }
}
