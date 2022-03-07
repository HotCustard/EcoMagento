<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_Productattachments
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/

namespace FME\Productattachments\Block;

use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

/**
 * Main contact form block
 */
class Message extends \Magento\Framework\View\Element\Messages
{
   /**
    * @param \Magento\Framework\View\Element\Template\Context $context
    * @param \Magento\Framework\Message\Factory               $messageFactory
    * @param \Magento\Framework\Message\CollectionFactory     $collectionFactory
    * @param \Magento\Framework\Message\ManagerInterface      $messageManager
    * @param InterpretationStrategyInterface                  $interpretationStrategy
    * @param array                                            $data
    * @codeCoverageIgnore
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
    }//end __construct()
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        return parent::_prepareLayout();
    }//end _prepareLayout()
}//end class
