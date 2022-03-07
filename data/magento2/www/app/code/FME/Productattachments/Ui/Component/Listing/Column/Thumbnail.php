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
namespace FME\Productattachments\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';


    /**
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image   $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder    = $urlBuilder;
        $this->assetRepo = $assetRepo;
        $this->_storeManager = $storeManager;
    }//end __construct()


    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        /*$media_url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );*/
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                /*$image = 'productattachments/no_image.png';
                if ($item['category_image'] !== '') {
                    $image = $item['category_image'];
                }*/
                $url = '';
                if ($item['category_image'] != '') {
                    if ($item['category_image'] == 'no_image.png') {
                        $url = $this->assetRepo->getUrl('FME_Productattachments::images/no_image.png');
                    } else{
                        $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ).$item['category_image'];
                    }
                    
                } else {
                    $url = $this->assetRepo->getUrl('FME_Productattachments::images/no_image.png');
                }

                $item[$fieldName.'_src']  = $url;
                $item[$fieldName.'_alt']  = $item['category_name'];
                $item[$fieldName.'_link'] = $this->urlBuilder->getUrl(
                    'productattachmentsadmin/productcats/edit',
                    [
                     'category_id' => $item['category_id'],
                     'store'       => $this->context->getRequestParam('store'),
                    ]
                );

                $item[$fieldName.'_orig_src'] = $url;
            }
        }//end if

        return $dataSource;
    }//end prepareDataSource()
}//end class
