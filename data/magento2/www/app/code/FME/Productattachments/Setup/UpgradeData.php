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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use FME\Productattachments\Model\ProductcatsFactory;
use FME\Productattachments\Model\ProductattachmentsFactory;

class UpgradeData implements UpgradeDataInterface
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
    public function __construct(
            ProductcatsFactory $productcatsFactory, 
            ProductattachmentsFactory $productattachmentsFactory)
    {
        $this->productcatsFactory        = $productcatsFactory;
        $this->productattachmentsFactory = $productattachmentsFactory;
    }//end __construct()
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableCatsStore = $setup->getTable('productattachments_category_store');
        if (version_compare($context->getVersion(), '1.4.9', '<')) {
            
            $isExistDefault = $this->createProductCats()->load('Default_Category', 'category_url_key');
            $lastId         = $isExistDefault->getId();
                
            if ($setup->getConnection()->isTableExists($tableCatsStore) == true && $lastId) {
                $store_data = [
                         'category_id'      => $lastId,
                         'store_id'   => 0
                        ];

                $setup->getConnection()->insert($tableCatsStore, $store_data);
                
            }//end if
        }//end if
        $setup->endSetup();
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

}//end class
