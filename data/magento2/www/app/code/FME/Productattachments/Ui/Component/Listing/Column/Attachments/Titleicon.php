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
namespace FME\Productattachments\Ui\Component\Listing\Column\Attachments;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Titleicon
 */
class Titleicon extends Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $extensions;
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
        \FME\Productattachments\Model\Extensions $extensions,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager   = $storeManager;
        $this->extensions = $extensions;
        $this->urlBuilder = $urlBuilder;
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
        $baseurl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
        $result = $this->extensions->getExtensions();
        foreach ($result as $value) {
             $ext_arr[] = strtolower($value['type']);
              $icons_arr[] = $baseurl.$value['icon'];
        }
        if (!isset($ext_arr)) {
            $ext_arr[] ='';
            $icons_arr[] = '';
        }
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $key = array_search($item['file_type'], $ext_arr);
          
                if ($key !== false) {
                    $img = '<img src="' . $icons_arr[$key].'" alt="' . $item['file_type'] . '" />';
                
                    $item[$fieldName] = ($img.' '.$item['title']);
                } else {
                    $item[$fieldName] = ($item['file_icon'].' '.$item['title']);
                }
            }
        }
        return $dataSource;
    }
}
