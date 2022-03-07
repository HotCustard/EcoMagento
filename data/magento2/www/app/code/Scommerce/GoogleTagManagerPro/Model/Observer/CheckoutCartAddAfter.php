<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleTagManagerPro\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CheckoutCartAddAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Scommerce\GoogleTagManagerPro\Helper\Data
     */
    protected $_helper;
	
	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_coreSession;
	
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Framework\Session\SessionManagerInterface $coresession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Scommerce\GoogleTagManagerPro\Helper\Data $helper
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
                                \Magento\Framework\Session\SessionManagerInterface $coresession,
                                \Magento\Framework\App\Request\Http $request,
                                \Scommerce\GoogleTagManagerPro\Helper\Data $helper)
    {
        $this->_objectManager = $objectmanager;
        $this->_coreSession = $coresession;
        $this->_request = $request;
        $this->_helper = $helper;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->_helper->isEnhancedEcommerceEnabled() && $this->_helper->isEnabled()){
            $productId = $this->_request->getParam('product', 0);
            $qty = $this->_request->getParam('qty', 1);
            if ($productId==0){
                $itemId = $this->_request->getParam('item', 0);
                if ($itemId>0){
                    $wishlist = $this->_objectManager->create('\Magento\Wishlist\Model\Wishlist')->load($itemId);
                    if ($wishlist){
                        $productId = $wishlist->getProductId();
                    }
                }
            }

            $quoteItem = $observer->getEvent()->getQuoteItem();
            $product = null;
            if ($quoteItem->getHasChildren() && $quoteItem->getProductType() == 'configurable') {
                $children = $quoteItem->getChildren();
                if (is_array($children) && count($children)) {
                    $qi = $quoteItem->getChildren()[0];
                    $product = $this->_objectManager->create('\Magento\Catalog\Model\Product')
                        ->load($qi->getProductId());
                }
            }

            if ($product == null) {
                $product = $this->_objectManager->create('\Magento\Catalog\Model\Product')
                    ->load($productId);
            }

            if (!$product->getId()) {
                return;
            }

            $productToBasket = array(
                    'id' => $product->getSku(),
                    'name' => $product->getName(),
                    'category' => $this->_helper->getProductCategoryName($product),
                    'brand' => $this->_helper->getBrand($product),
                    'price' => $product->getFinalPrice(),
                    'qty'=> $qty,
					'currency' => $this->_helper->getCurrencyCode()
            );
			
            $this->_coreSession->setProductToBasket(json_encode($productToBasket));
        }
    }

}