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
namespace FME\Productattachments\Controller\Adminhtml\Productcats;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use FME\Productattachments\Model\ResourceModel\Productcats\CollectionFactory;
 
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.​_
     * @var Filter
     */
    protected $_filter;
 
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
 
    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \FME\Productattachments\Model\ResourceModel\Productattachments\CollectionFactory $productattachments
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->productattachments = $productattachments;
        parent::__construct($context);
    }
 
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $collection = $this->_collectionFactory->create()->addFieldToFilter('category_id', ['in' => $selected]);
        $collection1 = $this->_collectionFactory->create()->addFieldToFilter('parent_category_id', ['in' => $selected]);
        $recordDeleted = 0;
        $catId=array();
        $defaultCat=0;
        $otherCat=0;
        foreach ($collection->getItems() as $record) {
            if ($record->getId() == 1) {
                $defaultCat=1;
            } else {
                $catId[] = $record->getId();
                $record->setId($record->getId());
                $record->delete();
                $recordDeleted++;
                $otherCat = 1;
            }
        }
        foreach ($collection1->getItems() as $record) {
            $catId[] = $record->getId();
            $record->setId($record->getId());
            $record->delete();
            $recordDeleted++;
        }
        $collection2 = $this->productattachments->create()->addFieldToFilter('cat_id', ['in' => $catId]);
        foreach ($collection2->getItems() as $record) {
            $record->setId($record->getId());
            $record->delete();
            $recordDeleted++;
        }
        if ($defaultCat==1) {
            $this->messageManager->addError(__("You can't delete Default Category"));
        }
        if ($otherCat == 1) {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}