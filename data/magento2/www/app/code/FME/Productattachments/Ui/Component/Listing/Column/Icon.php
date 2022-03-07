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

class Icon extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';


    /**
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image   $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder    = $urlBuilder;
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
        $media_url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $image = 'productattachments/no_image.png';
                if ($item['icon'] !== '') {
                    $image = $item['icon'];
                }
                $item[$fieldName.'_src']  = $media_url.$image;
                $item[$fieldName.'_alt']  = $item['type'];
                $item[$fieldName.'_link'] = $this->urlBuilder->getUrl(
                    'productattachmentsadmin/extensions/edit',
                    [
                     'extension_id' => $item['extension_id'],
                     'store'       => $this->context->getRequestParam('store'),
                    ]
                );
                $item[$fieldName.'_orig_src'] = $media_url.$item['icon'];
            }
        }//end if
        return $dataSource;
    }//end prepareDataSource()
}//end class
