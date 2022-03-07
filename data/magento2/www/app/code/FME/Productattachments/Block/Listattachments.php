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

class Listattachments extends \Magento\Framework\View\Element\Template
{
    public $_storeManager;
    public $_scopeConfig;
    public $filterProvider;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \FME\Productattachments\Helper\Data $helper,
        \FME\Productattachments\Model\ProductcatsFactory $productattachmentsproductcatsFactory,
        \FME\Productattachments\Model\Productcats $productattachmentsproductcats,
        \FME\Productattachments\Model\ProductattachmentsFactory $productattachmentsproductattachmentsFactory,
        \FME\Productattachments\Model\Productattachments $productattachmentsproductattachments,
        array $data = []
    ) {
        $this->_productattachmentsproductcatsFactory = $productattachmentsproductcatsFactory;
        $this->_productattachmentsproductcats = $productattachmentsproductcats;
        $this->_productattachmentsproductattachmentsFactory = $productattachmentsproductattachmentsFactory;
        $this->_productattachmentsproductattachments = $productattachmentsproductattachments;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $context->getStoreManager();
        $this->pageConfig = $context->getPageConfig();
        $this->_helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->filterProvider = $filterProvider; 
        parent::__construct($context, $data);
    }
    public function _prepareLayout()
    {
        $page_layout = $this->_helper->getLayout();
        //echo $page_layout; exit;
        $this->pageConfig->setPageLayout($page_layout);
        //$this->pageConfig->getTitle()->set(__('Product Attachments'));
        $_helper = $this->_objectManager->get('FME\Productattachments\Helper\Data');
        $data = $this->view(); //echo '<pre>';print_r($currentCat);echo '</pre>';exit;
        $currentCat = $data['current_category']->getData();
        $currentItem = isset($data['current_item']) ? $data['current_item'] : [];
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()]);
        $breadcrumbs->addCrumb('productattachments', [
            'label' => __('Product Attachments'),
            'title' => __('Product Attachments'),
            //'link' => (!empty($currentCat) ? $this->_storeManager->getStore()->getUrl('*/*/attachmentslist') : false),
            'link' => (!empty($currentCat) ? $this->_storeManager->getStore()->getUrl($this->getFrontUrl()) : false),
            'last' => (!empty($currentCat) ? false : true),
        ]);
        if (!empty($currentCat) && isset($currentCat['category_id'])) {
            $parentCat = $_helper->checkParent($currentCat['parent_category_id']);
            if (is_object($parentCat) && $parentCat->getData()) {
                $breadcrumbs->addCrumb('productattachments_par_cat', [
                    'label' => $parentCat->getCategoryName(),
                    'title' => $parentCat->getCategoryName(),
                    //'link' => (!empty($currentCat) ? $this->_storeManager->getStore()->getUrl('*/*/view', array('u' => $parentCat->getCategoryUrlKey())) : false),
                    'link' => (!empty($currentCat) ? $this->_storeManager->getStore()->getUrl($this->getFrontUrl()) . $parentCat->getCategoryUrlKey() : false),
                    'last' => (!empty($currentCat) ? false : true),
                ]);
            }
            $breadcrumbs->addCrumb('productattachments_cat', [
                'label' => $currentCat['category_name'],
                'title' => $currentCat['category_name'],
                //'link' => (!empty($currentItem) ? $this->_storeManager->getStore()->getUrl('*/*/view', array('u' => $currentCat['category_url_key'])) : false),
                'link' => (!empty($currentItem) ? $this->_storeManager->getStore()->getUrl($this->getFrontUrl()) . $currentCat['category_url_key'] : false),
                'last' => (!empty($currentItem) ? false : true),
            ]);
        }
        if (!empty($currentItem)) {
            $breadcrumbs->addCrumb('productattachments_att', [
                'label' => $currentItem->getTitle(),
                'title' => $currentItem->getTitle(),
                'link' => false,
                'last' => true,
            ]);
        }
        return parent::_prepareLayout();
    }
    public function countAttachments($id)
    {
        $count = $this->_productattachmentsproductattachments
                ->getCollection()
                ->addFieldToFilter('main_table.cat_id', $id)
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addEnableFilter(true);

        return $count->count();
    }
    public function attachments()
    {
        $param = $this->getRequest()->getParam('v');
        $data = $this->_productattachmentsproductattachments->load($param);
        if ($data->getId() && !$data->getStatus()) {
            return [];
        }
        return $data->getData();
    }
    public function view()
    {
        $param = $this->getRequest()->getParam('u');
        $paramV = $this->getRequest()->getParam('v'); // on attachment
        $data = [];
        $data['current_category'] = $this->_productattachmentsproductcats->load($param, 'category_url_key');
        if (isset($paramV)) {
            $item = $this->_productattachmentsproductattachments->load($paramV);
            $data['current_category'] = $this->_productattachmentsproductcats->load($item->getCatId());
            $data['current_item'] = $item;
        }
        return $data;
    }
    public function countSubCategories($cat, $front = false, $prod = false)
    {
        $countCollection = $this->_productattachmentsproductcatsFactory->create()
                ->getCollection()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('main_table.parent_category_id', $cat)
                ->addStatusFilter(true);
        if ($front) {
            $countCollection->addFieldToFilter('main_table.is_visible_front', $front);
        }
        if ($prod) {
            $countCollection->addFieldToFilter('main_table.is_visible_prod', $prod);
        }
      //  echo (string)$countCollection->getSelect();exit;
        return $countCollection->count();
    }
    public function getSkinUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'productattachments/' . $url;
    }
    public function iscms_pdflink()
    {
        return $this->_helper->iscms_pdflink();
    }
    public function getFrontUrl()
    {
        return $this->_helper->getFrontName();
    }
}
