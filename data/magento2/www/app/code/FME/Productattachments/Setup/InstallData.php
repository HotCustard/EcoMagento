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

namespace FME\Productattachments\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use FME\Productattachments\Model\Productcats;
use FME\Productattachments\Model\ProductcatsFactory;
use FME\Productattachments\Model\Productattachments;
use FME\Productattachments\Model\ProductattachmentsFactory;

class InstallData implements InstallDataInterface
{

    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $productcatsFactory;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $productattachmentsFactory;


    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(ProductcatsFactory $productcatsFactory, ProductattachmentsFactory $productattachmentsFactory)
    {
        $this->productcatsFactory        = $productcatsFactory;
        $this->productattachmentsFactory = $productattachmentsFactory;
    }//end __construct()


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableCats = $setup->getTable('productattachments_cats');
        $tableIcons = $setup->getTable('productattachments_extensions');
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            if ($setup->getConnection()->isTableExists($tableCats) == true) {
                $data = [
                         'category_name'      => 'Default Category',
                         'category_url_key'   => 'Default_Category',
                         'parent_category_id' => 0,
                         'level'              => 0,
                         'path'               => 1,
                         'category_store_ids' => [0],
                         'created_at'         => date('Y-m-d H:i:s'),
                         'is_root'            => 1,
                         'status'             => 1,
                        ];
                // smart approach for avoiding conflict
                $isExistDefault = $this->createProductCats()->load('Default_Category', 'category_url_key');
                $lastId         = 0;

                if (!$isExistDefault->getId()) {
                    $lastId = $this->createProductcats()->setData($data)->save()->getId();
                    // $lastId = $save->getId();
                } else {
                    $lastId = $isExistDefault->getId();
                }

                $this->_updateProductcats($lastId);
                $this->_updateProductattachments($lastId);
            }//end if

            $setup->endSetup();
        }//end if
    }//end install()


    /**
     * Create productcats
     *
     * @return productcats
     */
    public function createProductcats()
    {
        return $this->productcatsFactory->create();
    }//end createProductcats()


    /**
     * Create productcats
     *
     * @return productcats
     */
    public function createProductattachments()
    {
        return $this->productattachmentsFactory->create();
    }//end createProductattachments()


    protected function _updateProductcats($lastId)
    {
        $collection = $this->createProductcats()->getCollection()->addFieldToFilter('parent_category_id', 0)->addFieldToFilter('category_id', ['neq' => $lastId]);

        foreach ($collection as $item) {
            $this->createProductcats()->setId($item->getId())->setParentCategoryId($lastId)->setPath($lastId.'/'.$item->getId())->setLevel(1)->save();
        }
    }//end _updateProductcats()


    protected function _updateProductattachments($lastId)
    {
        $collection = $this->createProductattachments()->getCollection()->addFieldToFilter('cat_id', 0);

        foreach ($collection as $item) {
            $this->createProductattachments()->setId($item->getId())->setCatId($lastId)->save();
        }
    }//end _updateProductattachments()
}//end class
