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
 * Class ProductattachmentsAction
 */
class ProductattachmentsActions extends Column
{
/**
 * Url path
*/
    const Productattachments_URL_PATH_EDIT   = 'productattachmentsadmin/productattachments/edit';
    const Productattachments_URL_PATH_DELETE = 'productattachmentsadmin/productattachments/delete';
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
        $editUrl = self::Productattachments_URL_PATH_EDIT
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
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['productattachments_id'])) {
                    $item[$name]['edit']      = [
                                                 'href'  => $this->urlBuilder->getUrl($this->editUrl, ['id' => $item['productattachments_id']]),
                                                 'label' => __('Edit'),
                                                ];
                    $item[$name]['delete']    = [
                                                 'href'    => $this->urlBuilder->getUrl(self::Productattachments_URL_PATH_DELETE, ['id' => $item['productattachments_id']]),
                                                 'label'   => __('Delete'),
                                                 'confirm' => [
                                                               'title'   => __('Delete ${ $.$data.title }'),
                                                               'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?'),
                                                              ],
                                                ];
                     $item[$name]['download'] = [
                                                 'href'   => $item['download_link'],
                                                 'label'  => __('Download ( '.$item['downloads'].' )'),
                                                 'target' => 'blank',
                                                ];
                }
            }//end foreach
        }//end if
        return $dataSource;
    }//end prepareDataSource()
}//end class
