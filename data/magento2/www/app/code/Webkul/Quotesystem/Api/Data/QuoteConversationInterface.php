<?php
/**
 * Quote Conversation Interface
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Api\Data;

interface QuoteConversationInterface
{
    /**
* #@+
     * Constants for keys of data array.
     */
    const ENTITYID = 'entity_id';
    /**
* #@-
*/

    const ATTACHMENTS = 'attachments';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId();
    
    /**
     * set Entity Id
     *
     * @param int $id [entity id]
     *
     * @return \Webkul\Quotesystem\Api\Data\QuoteInterface
     */
    public function setEntityId($id);

    /**
     * Get attachments
     *
     * @return string|null
     */
    public function getAttachments();
    
    /**
     * set attachments
     *
     * @param string $attachments
     *
     * @return \Webkul\Quotesystem\Api\Data\QuoteInterface
     */
    public function setAttachments($attachments);
}
