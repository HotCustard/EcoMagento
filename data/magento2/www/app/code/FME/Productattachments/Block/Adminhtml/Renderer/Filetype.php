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

namespace FME\Productattachments\Block\Adminhtml\Renderer;

use \Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Data\CollectionDataSourceInterface;

class Filetype extends AbstractRenderer implements CollectionDataSourceInterface
{
    /**
     * constructor
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }//end __construct()
    public function render(\Magento\Framework\DataObject $row)
    {
        $downlaod_link = $row->getData('file_icon');
        $number        = $row->getData('file_type');
        $result        = $downlaod_link.' '.'( .'.$number.' )';

        return $result;
    }//end render()
}//end class
