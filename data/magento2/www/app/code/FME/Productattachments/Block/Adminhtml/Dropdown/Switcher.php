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

namespace FME\Productattachments\Block\Adminhtml\Dropdown;

/**
 * Store switcher block
 */
class Switcher extends \Magento\Backend\Block\Template
{
    /**
     * URL for store switcher hint
     */
    

    /**
     * Name of website variable
     *
     * @var string
     */
    protected $_defaultWebsiteVarName = 'website';

    /**
     * Name of store group variable
     *
     * @var string
     */
    protected $_defaultStoreGroupVarName = 'group';

    /**
     * Name of store variable
     *
     * @var string
     */
    protected $_defaultStoreVarName = 'store';

    /**
     * @var array
     */
    protected $_storeIds;

    /**
     * Url for store switcher hint
     *
     * @var string
     */
    protected $_hintUrl;

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'FME_Productattachments::gridswitcher/switcher.phtml';

    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Store Group Factory
     *
     * @var \Magento\Store\Model\GroupFactory
     */
    protected $_storeGroupFactory;
    protected $urlBuilder;
    /**
     * Store Factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Store\Model\GroupFactory $storeGroupFactory
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Store\Model\GroupFactory $storeGroupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_websiteFactory = $websiteFactory;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

   
    
   
    public function getselectedpage()
    {
        if ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('productattachmentsadmin/productattachments/byproducts', ['_secure' => true])) {
            return "By Product";
        } elseif ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('productattachmentsadmin/productattachments/index', ['_secure' => true])) {
            return "Attachments";
        } elseif ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('productattachmentsadmin/productcats/index', ['_secure' => true])) {
            return "Categories";
        }
    }
    public function getbyproductsurl()
    {
        return $this->getUrl('productattachmentsadmin/productattachments/byproducts', ['_secure' => true]);
    }
    public function getattachmentsurl()
    {
        return $this->getUrl('productattachmentsadmin/productattachments/index', ['_secure' => true]);
    }
    public function getcategoryurl()
    {
        return $this->getUrl('productattachmentsadmin/productcats/index', ['_secure' => true]);
    }
    public function getconfigurl()
    {
        return $this->getUrl('admin/system_config/index', ['_secure' => true]);
    }
}
