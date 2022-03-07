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
namespace FME\Productattachments\Model\Image;

class Config extends \Magento\Framework\ObjectManager\ObjectManager
{
    /**---Functions---*/
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl('') . 'productattachments/files' ;
    }
    public function getBaseMediaPath()
    {
        return BP . DS . 'media' . DS . 'productattachments/files' . DS;
    }
    public function getMediaUrl($file)
    {
        $aryfile = explode("/", $file);
        
        return $this->_storeManager->getStore()->getBaseUrl('') . 'productattachments' . DS . 'files' . DS . $file;
    }
    public function getMediaPath($file)
    {
        $aryfile = explode("/", $file);
        return BP . DS . 'media' . DS . 'productattachments' . DS . 'files' . DS . $file;
    }
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config
    ) {    
        parent::__construct($factory, $config);
        $this->_objectManager = $objectManager;
    }
}
