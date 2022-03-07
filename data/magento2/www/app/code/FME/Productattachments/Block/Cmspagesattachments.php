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

class Cmspagesattachments extends \Magento\Framework\View\Element\Template
{
    const DISPLAY_CONTROLS = 'productattachments/cmspagesattachments/enabled';
    public $_storeManager;
    public $_config;
    public $_scopeConfig;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnectionFactory $coreresourceFactory,
        \Magento\Framework\App\ResourceConnection $coreresource,
        \FME\Productattachments\Model\ProductattachmentsFactory $productattachmentsproductattachmentsFactory,
        \FME\Productattachments\Model\Productattachments $productattachmentsproductattachments,
        \FME\Productattachments\Helper\Data $myModuleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreresourceFactory = $coreresourceFactory;
        $this->_coreresource = $coreresource;
        $this->_mymoduleHelper = $myModuleHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_config = $context->getScopeConfig();
        $this->_productattachmentsproductattachmentsFactory = $productattachmentsproductattachmentsFactory;
        $this->_productattachmentsproductattachments = $productattachmentsproductattachments;
    }

    protected function _tohtml()
    {
        if (!$this->_scopeConfig->getValue(self::DISPLAY_CONTROLS)) {
            return parent::_toHtml();
        }
        $this->setTemplate("FME_Productattachments::cms_attachments.phtml");
        return parent::_toHtml();
    }

    public function iscms_pdflink()
    {
        return $this->_mymoduleHelper->iscms_pdflink();
    }

    public function getCmsPageRelatedAttachments($categoryId = null)
    {
        return $this->_productattachmentsproductattachments->getCmsPageRelatedAttachments($categoryId);
    }
    public function getcats()
    {
        return $this->_productattachmentsproductattachments->getcats();
    }
    public function getpageid()
    {
        return $this->_productattachmentsproductattachments->getpageid();
    }
    public function getSkinUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'productattachments/' . $url;
    }
    public function getnewicon($type)
    {
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $conn = $objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');       
        $productsTable = $connection->getTableName('productattachments_extensions');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.status = 1');
        $result = $conn->fetchAll($select);
        foreach ($result as $value) {
             $ext_arr[] = strtolower($value['type']);
              $icons_arr[] = $baseurl.$value['icon'];
        }
        if (!isset($ext_arr)) {
            $ext_arr[] ='';
            $icons_arr[] = '';
        }

        $key = array_search($type, $ext_arr);
          
        if ($key !== false) {
            return '<img src="' . $icons_arr[$key].'" alt="' . $type . '" />';
        } else {
            return 0;
        }
    }
}
