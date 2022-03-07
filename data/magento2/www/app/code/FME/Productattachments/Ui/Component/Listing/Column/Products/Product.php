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
namespace FME\Productattachments\Ui\Component\Listing\Column\Products;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Product
 */
class Product extends Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $_productsModel;
    /**
     * @var \Sample\News\Model\Uploader
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Sample\News\Model\Uploader $imageModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \FME\Productattachments\Model\Products $productModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $components = [],
        array $data = []
    ) {
       $this->_productFactory = $productFactory;
        $this->urlBuilder = $urlBuilder;
        $this->_productsModel = $productModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($dataSource['data']['items'])) {
            $url = $this->urlBuilder->getUrl('productattachmentsadmin/productattachments/filters');

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                // $item[$fieldName] =  count($this->_answersModel->loadAnswersCount($item['faq_id']));
                
                $suburl = $url.'?id='.$item['product_id'];
                //$product = $objectManager->get('Magento\Catalog\Model\Product')->load($item['product_id']);
                $product=$this->_productFactory->create()->load($item['product_id']);
              //  $item[$fieldName] =  $product['name'];
                 $item[$fieldName] =  ("<a  onclick=\"window.location='$suburl'\" href='$suburl' >".$product['name']."</a>");
                $item['sku'] = $product['sku'];
                $item['attachments'] = count($this->_productsModel->CountAttachments($item['product_id']));

                $item['visibleattachments'] = count($this->_productsModel->CountVisibleAttachments($item['product_id']));
            }
        }
   
        return $dataSource;
    }
}
