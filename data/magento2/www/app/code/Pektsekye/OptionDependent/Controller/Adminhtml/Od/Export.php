<?php

namespace Pektsekye\OptionDependent\Controller\Adminhtml\Od;

use Magento\Backend\App\Action;

abstract class Export extends \Magento\Backend\App\AbstractAction
{


    protected $_productResource;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,        
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_productResource = $productResource;    
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
    
    
}
