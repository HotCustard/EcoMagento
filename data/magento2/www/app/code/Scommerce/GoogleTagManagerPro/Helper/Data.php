<?php
/**
 * Google Tag Manager Data Helper
 *
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleTagManagerPro\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Admin configuration paths
     *
     */
    const XML_PATH_ENABLED 					= 'googletagmanagerpro/general/active';
    const XML_PATH_LICENSE_KEY 				= 'googletagmanagerpro/general/license_key';
    const XML_PATH_ACCOUNT_ID 				= 'googletagmanagerpro/general/account_id';
    const XML_PATH_BASE 					= 'googletagmanagerpro/general/base';
    const XML_PATH_ENHANCED_ECOMMERCE 		= 'googletagmanagerpro/general/enhanced_ecommerce_enabled';
    const XML_PATH_CAT_AJAX_ENABLED			= 'googletagmanagerpro/general/category_ajax_enabled';
	const XML_PATH_ENHANCED_SIOS			= 'googletagmanagerpro/general/send_impression_on_scroll';
	const XML_PATH_ENHANCED_PIC_TEXT		= 'googletagmanagerpro/general/product_item_class';
    const XML_PATH_ENHANCED_BRAND_DROPDOWN  = 'googletagmanagerpro/general/brand_dropdown';
    const XML_PATH_ENHANCED_BRAND_TEXT      = 'googletagmanagerpro/general/brand_text';
    const XML_PATH_ENABLE_DYNAMIC           = 'googletagmanagerpro/general/enable_dynamic';
	const XML_PATH_ENABLE_OTHER_SITES 		= 'googletagmanagerpro/general/enable_other_sites';
    const XML_PATH_ATTRIBUTE_KEY            = 'googletagmanagerpro/general/attribute_key';
	const XML_PATH_AJAX_ENABLED             = 'googletagmanagerpro/general/ajax_enabled';
	const XML_PATH_GDPR_COOKIE_ENABLED		= 'googletagmanagerpro/general/gdpr_cookie_enabled';
	const XML_PATH_GDPR_FORCE_DECLINE 		= 'googletagmanagerpro/general/force_decline';
	const XML_PATH_GDPR_COOKIE_KEY			= 'googletagmanagerpro/general/gdpr_cookie_key';
	

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Scommerce\Core\Helper\Data
     */
    protected $_data;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

	/** 
	* @var \Magento\Framework\Stdlib\CookieManagerInterface 
	*/
    protected $_cookieManager;
	
    /**
     * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Registry $registry
     * @param \Scommerce\Core\Helper\Data $data
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Registry $registry,
        \Scommerce\Core\Helper\Data $data,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
		$this->_data = $data;
        $this->_productHelper = $productHelper;
        $this->_product = $product;
        $this->_objectManager = $objectManager;
		$this->_cookieManager = $cookieManager;
        $this->_storeManager = $storeManager;
        $this->_productRepository = $productRepository;
    }


    /**
     * returns whether module is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) && $this->isLicenseValid() && $this->getAccountId() && $this->hasCookie();
    }
    
    /**
     * returns account id
     * @return string
     */
    public function getAccountId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCOUNT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns current store currency code
     * @return string
     */
    public function getCurrencyCode(){
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * returns formatted produce price
     * @param Magento/Catalog/Model/Product
     * @return float
     */
    public function productPrice($product)
    {
        $price=0;
        if ($this->_productHelper->getFinalPrice($product)>0){
            $price = $this->_productHelper->getFinalPrice($product);
        }
        elseif($this->_productHelper->getPrice($product)>0){
            $price = $this->_productHelper->getPrice($product);
        }
        return number_format($price,2);
    }

    /**
     * returns product category name
     *
     * @return string
     */
    public function getProductCategoryName($_product)
    {
        $_cats = $_product->getCategoryIds();
        $_categoryId = array_pop($_cats);

        $_cat = $this->_objectManager->create('\Magento\Catalog\Model\Category')->load($_categoryId);
        return $_cat->getName();
    }

    /**
     * returns category name
     * @param $quoteItem \Magento\Quote\Model\Quote\Item
     * @return string
     */
    public function getQuoteCategoryName($quoteItem)
    {
        if ($_catName = $quoteItem->getCategory())
        {
            return $_catName;
        }

        $_product = $quoteItem->getProduct();

        if (!($_product)) $_product = $this->_product->load($quoteItem->getProductId());

        return $this->getProductCategoryName($_product);
    }

    /**
     * returns whether enhanced ecommerce is enabled or not
     * @return string
     */
    public function isEnhancedEcommerceEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENHANCED_ECOMMERCE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * returns whether send impression on scroll is enabled or not
     * @return boolean
     */
    public function isSIOSEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENHANCED_SIOS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns if category ajax is enabled or not
     * @return boolean
     */
    public function isCategoryAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CAT_AJAX_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

	 /**
     * returns product item class static text
     * @return string
     */
    public function getPICText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_PIC_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns whether base order data is enabled or not
     * @return boolean
     */
    public function sendBaseData()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BASE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns attribute id of brand
     * @return string
     */
    public function getBrandDropdown()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_BRAND_DROPDOWN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns brand static text
     * @return string
     */
    public function getBrandText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_BRAND_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * returns brand value using product or text
     * @param $product Mage_Catalog_Product
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrand($product)
    {
		$_product = $this->_productRepository->getById($product->getId());
        if ($attribute = $this->getBrandDropdown()){
            $data = $_product->getAttributeText($attribute);
			if (is_array($data)) $data = end($data);
            if (strlen($data)==0){
                $data = $_product->getData($attribute);
            }
            return $data;
        }
        return $this->getBrandText();
    }

    /**
     * checks to see if the extension is enabled for advanced tagging in admin
     *
     * @return boolean
     */
    public function getDynamicRemarketingEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_DYNAMIC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * checks to see if the other site variable is enabled or not
     *
     * @return boolean
     */
    public function isOtherSiteEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_OTHER_SITES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns product attribute key
     *
     * @return string
     */
    public function getProductAttributeKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ATTRIBUTE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve domain url without www or subdomain
     *
     * @return string
     */
    public function getDomain()
    {
        $host = $this->_request->getHttpHost();
        if (substr_count($host,'.')>1){
            return substr($host,strpos($host,'.')+1);
        }
        return $host;
    }
	
	/**
     * Retrieve page as display mode
     *
     * @return string
     */
    public function getCMDisplayMode()
    {
        return 'PAGE';
    }
	
	/**
     * returns whether ajax add to basket is enabled or not
     * @return string
     */
    public function isAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AJAX_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * returns whether GDPR cookie check is enabled or not
     *
     * @return boolean
     */
    public function isGDPRCookieEnabled() {
		return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GDPR_COOKIE_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	
	/**
     * returns force decline is on or not
     *
     * @return boolean
     */
    public function isGDPRCookieForceDeclined() {
		return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GDPR_FORCE_DECLINE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	
	/**
     * Get cookie key to check accepted cookie policy
     *
     * @return string
     */
    protected function getCookieKey($storeId = null)
    {
		return $this->scopeConfig->getValue(
            self::XML_PATH_GDPR_COOKIE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * Check if has cookie with accepted cookie policy
     *
     * @return bool
     */
    protected function hasCookie()
    {
		$cookieKey = $this->getCookieKey();
		if (!$this->isGDPRCookieEnabled() || strlen($cookieKey)==0) return true;
		$cookie = (string)$this->_cookieManager->getCookie($cookieKey);
		if (!$this->isGDPRCookieForceDeclined()){
			if ($cookie=="0"){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			if ($cookie=="1"){
				return true;
			}
			else{
				return false;
			}
		}
    }
	
	/**
     * returns license key administration configuration option
     *
     * @return string
     */
    public function getLicenseKey(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_LICENSE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

	/**
     * returns whether license key is valid or not
     *
     * @return bool
     */
    public function isLicenseValid(){
		$sku = strtolower(str_replace('\\Helper\\Data','',str_replace('Scommerce\\','',get_class($this))));
		return $this->_data->isLicenseValid($this->getLicenseKey(),$sku);
	}
}