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

namespace FME\Productattachments\Controller\Adminhtml\Import;

use \Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use FME\Productattachments\Model\ProductattachmentsFactory;
use FME\Productattachments\Helper\Data;

class Import extends Action
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $storeManager;
    /**
     * News model factory
     *
     * @var \FME\Productattachments\Model\ProductattachmentsFactory
     */
    protected $_productattachmentsFactory;
    /**
     * @param Context                   $context
     * @param Registry                  $coreRegistry
     * @param PageFactory               $resultPageFactory
     * @param ProductattachmentsFactory $productattachmentsFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ProductattachmentsFactory $productattachmentsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helper
    ) {
        parent::__construct($context);
        $this->_coreRegistry              = $coreRegistry;
        $this->_resultPageFactory         = $resultPageFactory;
        $this->storeManager   = $storeManager;
        $this->_productattachmentsFactory = $productattachmentsFactory;
        $this->_helper                    = $helper;
    }//end __construct()
    /**
     * @return void
     */
    public function execute()
    {
        /*
            @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
        */
        $resultRedirect = $this->resultRedirectFactory->create();
        /*
            @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productattachments::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Attachments'));

        $dir   = $this->_objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
        $media = $dir->getPath($dir::MEDIA);
        $baseurl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $count = 0;

        try {
            $categories = $this->_helper->importAttachmentCategories();

            if (empty($categories)) {
                $this->messageManager->addError(__('Error while importing categories.'));
                return $resultRedirect->setPath('*/*/index');
            }

            foreach ($categories as $category) {
                if ($category['category_name'] === 'Default') {
                    continue;
                }

                $category['category_url_key']   = $this->_objectManager->create('FME\Productattachments\Helper\Data')->nameToUrlKey($category['category_name']);
                $category['parent_category_id'] = $this->_helper->getDefaultCategory()->getId();
                if ($category['parent_category_id'] == $this->_helper->getDefaultCategory()->getId()) {
                    $category['level'] = 1;
                }

                $category['category_store_ids'] = [0];
                $productCats                    = $this->_objectManager->create('FME\Productattachments\Model\Productcats');
                $productCats->setIsPkAutoIncrement(false);
                $productCats->setData($category);
                try {
                    $productCats->save();
                    // $count++;
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/index');
                }
            }//end foreach

            $metaData = $this->_helper->getMetaDataProductAttachments();

            $csvFilePath = $this->_helper->prepareCsv('ProductAttachments');

            if (!file_exists($csvFilePath)) {
                $this->messageManager->addError(__('File ProductAttachments.csv do not exists!'));
                return $resultRedirect->setPath('*/*/index');
            }

            if ($this->_helper->prepareCsvParts($csvFilePath) === false) {
                $this->messageManager->addError(__('Unknown Error occured!'));
                return $resultRedirect->setPath('*/*/index');
            }

            $number = 0;

            $found  = true;
            $partno = 0;

            while ($found) {
                $partCsvPath = $this->_helper->prepareCsv('ProductAttachments_'.$partno);

                if (file_exists($partCsvPath)) {
                    $found = true;

                    $csvFile = file($partCsvPath);
                    $csvData = [];

                    foreach ($csvFile as $line) {
                        $csvRow = str_getcsv($line);

                        array_walk(
                            $csvRow,
                            function (&$v) {
                                if ($v == '\N') {
                                    $v = '';
                                }
                            }
                        );

                        $csvData[] = array_combine($metaData, $csvRow);
                    }

                    $relations = $this->_helper->importAttachmentsRelations();

                    foreach ($csvData as $k => $item) {
                        $related = [];
                        // this is a default path that every
                        // attachment from the system gets
                        $fileTo = '/productattachments/files/';
                        // change value for path accordingly
                        // $fileFrom = 'downloads/' . $item['productattachments_id'] . '/';
                        // all manually copied files before import, are placed here
                        $fileFrom = 'downloads/';

                        $fileFrom .= $item['filename'];

                        if (file_exists($media.$fileTo.$fileFrom)) {
                            $csvData[$k]['filename'] = $fileTo.$fileFrom;
                        }

                        if (!empty($relations)) {
                            foreach ($relations as $rel) {
                                if ($item['productattachments_id'] == $rel['productattachments_id']) {
                                    $product   = $this->_objectManager->create('\Magento\Catalog\Model\Product');
                                    $productId = $product->getIdBySku($rel['sku']);

                                    if ($productId == null) {
                                        continue;
                                    }

                                    $related[] = $productId;
                                }
                            }

                            $csvData[$k]['links']['related'] = implode('&', $related);
                        }
                    }//end foreach

                    unlink($partCsvPath);
                    $partno++;
                } else {
                    $found = false;
                }//end if

                if (!empty($csvData) && $found) {
                   // print_r($csvData);exit;
                    $ich = 0;
                    foreach ($csvData as $item) {
                        if ($ich ==0) {
                            $ich++;
                            continue;
                        }
                        if (isset($item['store_ids'])) {
                            $item['store_id'] = explode(',', $item['store_ids']);
                        } else {
                            // default store will be set for visibility
                            $item['store_id'] = [0];
                        }

                                                $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
                        $filePath = $media.$item['filename'];
                        if (file_exists($filePath)) {
                            // echo $filePath; exit;
                            $fileconfig->Fileicon($filePath);

                            $item['file_icon'] = $fileconfig->displayIcon();
                            $item['file_type'] = $fileconfig->getType();
                            $item['file_size'] = $fileconfig->getSize();
                            $item['download_link'] = $baseurl.$item['filename'];
                        }
                        $item['block_position']= 'additional,other';
                        unset($item['store_ids']);

                        $model = $this->_objectManager->create('FME\Productattachments\Model\Productattachments');
                        $model->setIsPkAutoIncrement(false);
                        $model->setData($item);

                        try {
                            $model->setCreatedTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));

                            $model->save();
                            $count++;
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->messageManager->addError($e->getMessage());
                            return $resultRedirect->setPath('*/*/index');
                        }
                    }//end foreach
                }//end if
            }//end while

            $this->messageManager->addSuccess(__('Number of attachments imported: '.$count));
            return $resultRedirect->setPath('*/*/index');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }//end try

        return $resultPage;
    }//end execute()


    /**
     * Module access rights checking
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Productattachments::fmeextensions_productattachments_import');
    }//end _isAllowed()
}//end class
