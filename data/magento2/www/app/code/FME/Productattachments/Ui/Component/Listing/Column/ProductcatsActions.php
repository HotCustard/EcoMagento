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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductcatsActions
 */
class ProductcatsActions extends Column
{

/**
 * Url path
*/
    const Productcats_URL_PATH_EDIT   = 'productattachmentsadmin/productcats/edit';
    const Productcats_URL_PATH_DELETE = 'productattachmentsadmin/productcats/delete';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;


    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder         $actionUrlBuilder
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::Productcats_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl    = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }//end __construct()


    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            // echo '<pre>';print_r($dataSource['data']['items']);echo '</pre>';
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['category_id'])) {
                    $item[$name]['edit'] = [
                                            'href'  => $this->urlBuilder->getUrl($this->editUrl, ['category_id' => $item['category_id']]),
                                            'label' => __('Edit'),
                                           ];

                    if ($item['category_url_key'] != 'Default_Category') {
                        $item[$name]['delete'] = [
                                                  'href'    => $this->urlBuilder->getUrl(self::Productcats_URL_PATH_DELETE, ['category_id' => $item['category_id']]),
                                                  'label'   => __('Delete'),
                                                  'confirm' => [
                                                                'title'   => __('Delete ${ $.$data.title }'),
                                                                'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?'),
                                                               ],
                                                 ];
                    }
                }
            }
        }//end if

        return $dataSource;
    }//end prepareDataSource()
}//end class
