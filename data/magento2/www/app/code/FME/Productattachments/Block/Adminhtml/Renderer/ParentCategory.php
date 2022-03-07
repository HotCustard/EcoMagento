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

use FME\Productattachments\Model\ProductcatsFactory;
use FME\Productattachments\Model\Productcats;
use FME\Productattachments\Helper\Data;
use \Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\Decoder;

class ParentCategory extends AbstractRenderer implements CollectionDataSourceInterface
{
    /**
     * settings instance
     *
     * @var \Umc\Base\Model\Core\Settings
     */
    protected $settings;

    /**
     * file system reference
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * decoder
     *
     * @var \Magento\Framework\Url\Decoder
     */
    protected $decoder;

    /**
     * @var \Umc\Base\Model\Downloader
     */
    protected $downloader;
    /**
     * constructor
     *
     * @param Settings   $settings
     * @param Filesystem $filesystem
     * @param Decoder    $decoder
     * @param Downloader $downloader
     * @param Context    $context
     * @param array      $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        Filesystem $filesystem,
        Decoder $decoder,
        Data $helper,
        ProductcatsFactory $productattachmentsproductcatsFactory,
        Productcats $productattachmentsproductcats,
        array $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->decoder    = $decoder;
        $this->_productattachmentsproductcatsFactory = $productattachmentsproductcatsFactory;
        $this->_productattachmentsproductcats        = $productattachmentsproductcats;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }//end __construct()
    public function render(\Magento\Framework\DataObject $row)
    {
        $m = $this->_productattachmentsproductcats->load($row['category_id']);
        $all    = $this->_productattachmentsproductcatsFactory->create()->getCollection();
        $result = '';
        if ($row['parent_category_id'] != 0) {
            foreach ($all as $c) {
                if ($c->getCategoryId() == $row['parent_category_id']) {
                    $result = $c->getCategoryName();
                }
            }
        } else {
            $result = 'N/A';
        }
        return $result;
    }//end render()
}//end class
