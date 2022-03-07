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
use Magento\Framework\Url;

class Image extends AbstractRenderer implements CollectionDataSourceInterface
{
    /**
     * file system reference
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * constructor
     *
     * @param Settings $settings
     * @param Filesystem $filesystem
     * @param Decoder $decoder
     * @param Downloader $downloader
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        Url $filesystem,
        array $data = []
    ) {
        $this->filesystem = $filesystem; 
        parent::__construct($context, $data);
    }
  
    public function render(\Magento\Framework\DataObject $row)
    {
        $mediaDirectory = $this->filesystem
                ->getBaseUrl(['_type'=>'media']);
        if ($row->getData('category_image')!=null) {
            $baseScmsMediaURL = $mediaDirectory.$row->getData('category_image');
        } else {
            $baseScmsMediaURL = $mediaDirectory.'productattachments/no_image.png';
        }
        
        $result = "<img src=".$baseScmsMediaURL." width='100' height='100' />";
        return $result;
    }
}
