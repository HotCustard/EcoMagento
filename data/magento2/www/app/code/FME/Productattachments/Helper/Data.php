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

namespace FME\Productattachments\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /* General Settings */
    const XML_PATH_GENERAL_SHOW_COUNTER = 'productattachments/general/show_counter';
    const XML_PATH_GENERAL_LOGIN_DOWNLOAD = 'productattachments/general/login_before_download';
    const XML_PATH_LIST_LAYOUT = 'productattachments/general/list_layout';
    const XML_PATH_FRONT_NAME = 'productattachments/general/front_name';
    const XML_PATH_FRONT_DESC = 'productattachments/general/front_desc';
    /* Product View Page Settings */
    const XML_PRODUCT_ENABLE_ATTACHMENTS = 'productattachments/productattachments/enabled';
    const XML_PRODUCT_ENABLE_PDFLINK = 'productattachments/productattachments/newtab_enabled';
    const XML_PRODUCT_ATTACHMENT_HEADING = 'productattachments/productattachments/product_attachment_heading';
    const XML_PRODUCT_SHOW_CONTENT = 'productattachments/productattachments/showcontent';
    const XML_PRODUCT_TAB_TITLE = 'productattachments/productattachments/tab_title';
    /* CMS Page Settings */
    const XML_CMS_ENABLE_ATTACHMENTS = 'productattachments/cmspagesattachments/enabled';

    const XML_CMS_ENABLE_PDFLINK = 'productattachments/cmspagesattachments/newtab_enabledcms';
    const XML_CMS_ATTACHMENT_HEADING = 'productattachments/cmspagesattachments/cms_page_attachment_heading';
    const XML_CMS_SHOW_CONTENT = 'productattachments/cmspagesattachments/showcontent';

    protected static $_URL_ENCODED_CHARS = [
        ' ', '+', '(', ')', ';', ':', '@', '&', '`', '\'',
        '=', '!', '$', ',', '/', '?', '#', '[', ']', '%',
    ];

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productattachments\Model\ProductcatsFactory $productattachmentsproductcatsFactory,
        \FME\Productattachments\Model\Productcats $productattachmentsproductcats,
        \FME\Productattachments\Model\ProductattachmentsFactory $productattachmentsproductattachmentsFactory,
        \FME\Productattachments\Model\Productattachments $productattachmentsproductattachments,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) {
        $this->_productattachmentsproductcatsFactory = $productattachmentsproductcatsFactory;
        $this->_productattachmentsproductcats = $productattachmentsproductcats;
        $this->_productattachmentsproductattachmentsFactory = $productattachmentsproductattachmentsFactory;
        $this->_productattachmentsproductattachments = $productattachmentsproductattachments;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_eventManager = $context->getEventManager();
        $this->_resource = $coreResource;
        parent::__construct($context);
    }
    public function ispro_pdflink()
    {
         return $this->_scopeConfig->getValue(
             self::XML_PRODUCT_ENABLE_PDFLINK,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }
    public function iscms_pdflink()
    {
         return $this->_scopeConfig->getValue(
             self::XML_CMS_ENABLE_PDFLINK,
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }
    public function getFrontDesc()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_FRONT_DESC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Show Counter Function */
    public function showDownloadCounter()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GENERAL_SHOW_COUNTER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Login To Downlaod Fucntion */
    public function loginToDownload()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GENERAL_LOGIN_DOWNLOAD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Enable At Product View Page */
    public function enableAtProductView()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PRODUCT_ENABLE_ATTACHMENTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Product View Page Heading */
    public function getProductPageAttachmentHeading()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PRODUCT_ATTACHMENT_HEADING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Product View Show Attachemt Content */
    public function productShowContent()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PRODUCT_SHOW_CONTENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Enable At CMS Page */
    public function enableAtCmsPage()
    {
        return $this->_scopeConfig->getValue(
            self::XML_CMS_ENABLE_ATTACHMENTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* CMS Page Heading */
    public function getCMSPageAttachmentHeading()
    {
        return $this->_scopeConfig->getValue(
            self::XML_CMS_ATTACHMENT_HEADING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* CMS Page Show Attachemt Content */
    public function cmsShowContent()
    {
        return $this->_scopeConfig->getValue(
            self::XML_CMS_SHOW_CONTENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Name To Url Function */
    public static function nameToUrlKey($name)
    {
        $name = trim($name);
        $name = str_replace(self::$_URL_ENCODED_CHARS, '_', $name);
        do {
            $name = $newStr = str_replace('__', '_', $name, $count);
        } while ($count);
        return $name;
    }
    public function getLayout()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_LIST_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /* Get Category Data */
    public function getCatData($pid = 0)
    {
        $out = [];
        $collection = $this->_productattachmentsproductcats
                ->getCollection()
                ->addOrder('category_name', 'ASC');

        foreach ($collection as $item) {
            $out[] = $item->getData();
        }
        return $out;
    }
    /* Get Category Select */
    public function getSelectcat()
    {
        //$outtree = array();
        $outtree = $this->drawSelect(0); //echo '<pre>';print_r($outtree);exit;
        $out = [];
        foreach ($outtree['value'] as $k => $v) {
            $out[] = ['value' => $v, 'label' => $outtree['label'][$k]];
        }
        //$out = array_unshift($out, array('value' => 'null', 'label' => 'Select Category'));
        return $out;
    }
    /* Return Category Select Array */
    public function drawSelect($pid = 0)
    {
        $items = $this->getCatData($pid);
        $outtree = [];
        $outtree['value'][] = '';
        $outtree['label'][] = 'Select Category';
        if (count($items) > 0) {
            foreach ($items as $item) {
                $outtree['value'][] = $item['category_id'];
                $outtree['label'][] = $item['category_name'];
            }
        }
        return $outtree;
    }
    /* Get All Categories */
    public function getAllCategories()
    {
        $collection = $this->_productattachmentsproductcatsFactory->create()
                ->getCollection()
                ->addStoreFilter($this->_storeManager->getStore()->getId());
        return $collection;
    }
    /* Get All parent Titles */
    public function parentTitleOptions()
    {
        $collection = $this->_productattachmentsproductcatsFactory->create()->getCollection();
        $collection->addFieldToFilter('parent_category_id', 0);
        $options = [];
        $categoryInfo = [];
        $i = 0;
        foreach ($collection as $i) {
            $categoryInfo[$i->getCategoryId()] = $i->getCategoryName();
        }
        if (!empty($categoryInfo)) {
            return $categoryInfo;
        }
        return false;
    }
    public function getAllParentCategories($level = null, $front = false, $prod = false)
    {
        $collection = $this->_productattachmentsproductcatsFactory->create()->getCollection();
        if ($level != null) {
            //$collection->addFieldToFilter('main_table.level', $level);
        } else {
           // $collection->addFieldToFilter('main_table.parent_category_id', 0);
        }
        if ($front) {
            $collection->addFieldToFilter('main_table.is_visible_front', $front);
        }
        if ($prod) {
            $collection->addFieldToFilter('main_table.is_visible_prod', $prod);
        }
        $collection->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addStatusFilter(true);
       // echo (string) $collection->getSelect();exit;
        return $collection;
    }
    public function getSubCategories($parentCatId, $front = false, $prod = false)
    {
        $collection = $this->_productattachmentsproductcatsFactory->create()->getCollection()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('parent_category_id', $parentCatId)
                ->addStatusFilter(true);
        if ($front) {
            $collection->addFieldToFilter('main_table.is_visible_front', $front);
        }
        if ($prod) {
            $collection->addFieldToFilter('main_table.is_visible_prod', $prod);
        }
        return $collection;
    }
    /* Get Category List */
    public function getCategoryList($id = 0)
    {
        $val = [];
        $val[] = [
            'value' => false,
            'label' => __('Select Category')
        ];
        $resource = $this->_resource;
        $read = $resource->getConnection('core_read');
        $table = $resource->getTableName('productattachments_cats');
        /* if update mode else add */
        if ($id != 0) {
            $q = "SELECT category_id,category_name
				  FROM " . $table . "
				  WHERE category_id !=" . $id;
            //AND parent_category_id =0";

            $result = $read->fetchAll($q);

            foreach ($result as $r) {
                $val[] = [
                    'value' => $r['category_id'],
                    'label' => __($r['category_name'])
                ];
            }
        } else {
            $q = "SELECT category_id,category_name
				  FROM " . $table;
            //WHERE parent_category_id = 0";
            $result = $read->fetchAll($q);

            foreach ($result as $r) {
                $val[] = [
                    'value' => $r['category_id'],
                    'label' => __($r['category_name'])
                ];
            }
        }
        return $val;
    }
    public function checkParent($id)
    {
        $model = $this->_productattachmentsproductcats;
        $data = false;
        if ($id > 0) {
            $data = $model->load($id);
        }
        if ($data && $data->getCategoryUrlKey() == 'Default_Category') {
            $data = false;
        }
        return $data;
    }
    public function getProductAttachments($categoryId = null)
    {
        $collection = $this->_productattachmentsproductattachmentsFactory->create()
                ->getCollection()
                ->addStoreFilter($this->_storeManager->getStore()->getId());
        if ($categoryId != null) {
            $collection->addFieldToFilter('main_table.cat_id', $categoryId);
        }
        $collection->addEnableFilter(true);
        return $collection;
    }
    public function getAllLevels($obj)
    {
        $model = $this->_productattachmentsproductcats;
        $info = [];
        if (is_object($obj) && $obj->getId() > 0) {
            $curr_cat = $model->load($obj->getCatId());
            $info['curr_cat'] = $curr_cat;
            if ($curr_cat->getParentCategoryId() > 0) {
                $curr_par_cat = $this->_productattachmentsproductcats->load($curr_cat->getParentCategoryId());
                $info['curr_par_cat'] = $curr_par_cat;
            }
        }
        return $info;
    }
    public function getFrontName($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_FRONT_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getUrlSuffix()
    {
        return '';
    }
    /**
     * getProductattachmentsUrl
     * @return string
     */
    public function getProductattachmentsUrl()
    {
        $url = $this->getFrontName() . $this->getUrlSuffix();
        return $this->_storeManager->getStore()->getUrl($url);
    }
    public function getProductPageTabTitle($title)
    {
        $configTitle = $this->_scopeConfig->getValue(
            self::XML_PRODUCT_TAB_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($configTitle !== null) {
            $title = $configTitle;
        }
        return $title;
    }
    public function getDefaultCategory()
    {
        $model = $this->_productattachmentsproductcats;
        $data = $model->load('Default_Category', 'category_url_key');
        return $data;
    }
    public function getMediaType($type = 'url')
    {
        $media = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        if ($type == 'path') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $dir = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');

            $media = $dir->getPath($dir::MEDIA);
        }
        return $media;
    }
    public function prepareCsv($fileName = '')
    {
        $file = '/productattachments/' . $fileName . '.csv';
        //$media = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        $media = $this->getMediaType('path');
        $csvPath = $media . $file;
        //ini_set('max_input_vars', '10000');
        return $csvPath;
    }
    public function prepareCsvParts($csvFilePath)
    {
        $fh = fopen($csvFilePath, 'r');
        //set_time_limit(72000);
        if ($fh) {
            $fileno = 0;
            $lineno = 0;
            $startofnewfile = true;
            $lastlineno = 0;
            $media = $this->getMediaType('path');

            while ($rowData = fgets($fh)) {
                //Create new file
                if ($startofnewfile) {
                    $startofnewfile = false;
                    $lastlineno = 0;
                    //Create a file with unique name
                    $file = $media . "/productattachments/ProductAttachments_" . $fileno . ".csv";
                    $fw = fopen($file, 'w');
                }
                //write csv Line to the taret file in append mode.
                $fwrite = fwrite($fw, $rowData);
                //Count line numbers
                $lineno++;
                //Reached the limit of file now prepare to start new file
                if ($lineno == 2000) {
                    $lastlineno = $lineno;
                    fclose($fw);
                    $lineno = 0;
                    $startofnewfile = true;
                    $fileno++;
                }
            }
            if ($lastlineno == 0) {
                fclose($fw);
            }
            return true;
        } else {
            throw new \Magento\Exception(__('File ProductAttachments.csv do not exists'));
        }
        return false;
    }
    public function startImportAttachments()
    {
        $metaData = [
            'productattachments_id',
            'title',
            'filename',
            // 'file_icon',
            'file_type',
            'file_size',
            //'download_link',
            //'block_position',
            'link_url',
            //'link_title',
            'embed_video',
            //'video_title',
            'downloads',
            'content',
            'status',
            //'cmspage_id',
            'created_time',
            'update_time',
            'customer_group_id',
            'limit_downloads',
            'cat_id',
            'store_ids'
                //'product_id',
                //'sku'
        ];
        $csvFilePath = $this->prepareCsv('ProductAttachments');
        $media = $this->getMediaType('path');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //echo filesize($csvFilePath);exit;
        $fh = fopen($csvFilePath, 'r');
        //set_time_limit(72000);
        if ($fh) {
            $fileno = 0;
            $lineno = 0;
            $startofnewfile = true;
            $lastlineno = 0;
            while ($rowData = fgets($fh)) {
                //Create new file
                if ($startofnewfile) {
                    $startofnewfile = false;
                    $lastlineno = 0;
                    //Create a file with unique name
                    $file = $media . "/productattachments/ProductAttachments_" . $fileno . ".csv";

                    $fw = fopen($file, 'w');
                }
                //write csv Line to the taret file in append mode.
                $fwrite = fwrite($fw, $rowData);
                //Count line numbers
                $lineno++;
                //Reached the limit of file now prepare to start new file
                if ($lineno == 2000) {
                    $lastlineno = $lineno;
                    fclose($fw);
                    $lineno = 0;
                    $startofnewfile = true;
                    $fileno++;
                }
            }
            if ($lastlineno == 0) {
                fclose($fw);
            }
            $csvFile = file($file);
            $csvData = [];
            foreach ($csvFile as $line) {
                $csvRow = str_getcsv($line);
                array_walk($csvRow, function (&$v) {
                    if ($v == '\N') {
                        $v = '';
                    }
                });
                $csvData[] = array_combine($metaData, $csvRow);
            }
            $relations = []; //$this->importAttachmentsRelations();
            //echo '<pre>';print_r($csvData);exit;
            foreach ($csvData as $k => $item) {
                $related = [];
                $fileTo = '/productattachments/files/';
                $fileFrom = 'downloads/' . $item['productattachments_id'] . '/';
                $fileFrom .= $item['filename'];
                if (file_exists($media . $fileTo . $fileFrom)) {
                    $csvData[$k]['filename'] = $fileTo . $fileFrom;
                }
                if (!empty($relations)) {
                    foreach ($relations as $rel) {
                        if ($item['productattachments_id'] == $rel['productattachments_id']) {
                            $product = $objectManager->create('\Magento\Catalog\Model\Product');
                            $productId = $product->getIdBySku($rel['sku']);
                            if ($productId == null) {
                                continue;
                            }
                            $related[] = $productId;
                        }
                    }
                    $csvData[$k]['links']['related'] = implode('&', $related);
                }
            }
            unlink($file);
            return $csvData;
        } else {
            throw new \Magento\Exception(__('File does not exist'));
        }
    }
    public function importAttachmentsRelations($field = 'productattachments_id', $id = null)
    {
        $metaData = [
            'productattachments_id',
            'product_id',
            'sku'
        ];
        $csvPath = $this->prepareCsv('ProductAttachments_Relations');
        $csvData = [];
        if (file_exists($csvPath)) {
            $csvFile = file($csvPath);
            $csvData = [];
            foreach ($csvFile as $line) {
                $array = str_getcsv($line);
                $data = array_combine($metaData, $array);
                if ($id != null) {
                    if ($data[$field] != $id) {
                        continue;
                    }
                }
                $csvData[] = $data;
            }
        }
        return $csvData;
    }
    public function importAttachmentCategories()
    {
        $metaData = [
            'category_id',
            'category_name',
            'meta_description',
            'status',
        ];
        $csvPath = $this->prepareCsv('ProductAttachments_Categories');
        $csvData = [];
        if (file_exists($csvPath)) {
            $csvFile = file($csvPath);
            $csvData = [];
            foreach ($csvFile as $line) {
                $csvRow = str_getcsv($line);
                array_walk($csvRow, function (&$v) {
                    if ($v == '\N') {
                        $v = '';
                    }
                });
                $csvData[] = array_combine($metaData, $csvRow);
            }
        }
        return $csvData;
    }
    public function getMetaDataProductAttachments()
    {
        return [
            'productattachments_id',
            'title',
            'filename',
            // 'file_icon',
            //'file_type',
            //'file_size',
            //'download_link',
            //'block_position',
            'link_url',
            'link_title',
            'embed_video',
            'video_title',
            'downloads',
            'content',
            'status',
            'cmspage_id',
            'created_time',
            'update_time',
            'customer_group_id',
            'limit_downloads',
            'cat_id',
            'store_ids'
            //'product_id',
                //'sku'
        ];
    }
    public function getFileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = [
            0 => [
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ],
            1 => [
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ],
            2 => [
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ],
            3 => [
                "UNIT" => "KB",
                "VALUE" => 1024
            ],
            4 => [
                "UNIT" => "B",
                "VALUE" => 1
            ],
        ];
        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}
