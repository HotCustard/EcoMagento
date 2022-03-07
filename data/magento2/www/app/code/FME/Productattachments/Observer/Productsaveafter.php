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
namespace FME\Productattachments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use FME\Productattachments\Model\Productattachments as ProductattachmentsModel;

class Productsaveafter implements ObserverInterface
{
    protected $request;
    protected $productattachmentsModel;
    protected $_objectManager;

    public function __construct(Context $context, \Magento\Catalog\Model\ProductFactory $productFactory, ProductattachmentsModel $productattachmentsModel)
    {
        $this->_objectManager = $context->getObjectManager();
        $this->request = $context->getRequest();
        $this->productattachmentsModel = $productattachmentsModel;
        $this->_productFactory = $productFactory;
        $this->_resource = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\ResourceConnection');
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct()->getId();  // you will get product object
        $postData = $this->request->getPost();
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$prod = $objectManager->get('Magento\Catalog\Model\Product')->load($_product);
        $prod=$this->_productFactory->create()->load($_product);
       
         $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
            $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
        $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
       
        $dont_delete = [];
        
        if (isset($postData['product']['attachments'])) {
            foreach ($postData['product']['attachments']['dynamic_rows'] as $value) {
                //print_r($value);
                if (isset($value['attachment_id'])) {
                    $data['productattachments_id'] = $value['attachment_id'];
                    $model = $this->productattachmentsModel->load($value['attachment_id']);
                } else {
                    $data['productattachments_id'] = null;
                    $model = $this->productattachmentsModel;
                }
                if (isset($value['filename'])) {
                    if ($value['filename'][0]['status'] == 'old') {
                        $temp='';
                    } 
                    else {
                        $filepath = $baseScmsMediaURL.'productattachments/files/'.$value['filename'][0]['name'];

                        $data['filename'] = 'productattachments/files/'.$value['filename'][0]['name'];

                        $fileconfig->Fileicon($filepath);
                        $data['download_link'] = $value['filename'][0]['url'];
                        $data['file_icon'] = $fileconfig->displayIcon();
                        $data['file_type'] = $fileconfig->getType();
                        $data['file_size'] = $fileconfig->getSize();
                    }
                }
                $data['product_names'] = $prod['name'];
                $data['cmspage_ids'] = (array)null;
                $data['product_id'] = (array)$_product;
                $data['one_product_only'] = 1;
                $data['title'] = $value['title'];
                //$data['status'] = @$value['status'] ? @$value['status'] : 1;
                if (isset($value['status'])) {
                    if ($value['status']) {
                        $data['status'] = $value['status'];
                    } else {
                        $data['status'] = 1;
                    }
                }
                $data['downloads'] = 0;
                 $data['block_position'] = 'additional,other';
                 $data['limit_downloads'] = 0;
                 $data['cat_id'] = 1;
                 if($model->getId()){
                     $data['cat_id'] = $model->getCatId();
                 }
                 $data['store_id'] = (array)0;

                if ($value['type'] == 'url') {
                    $data['link_url'] = $value['link_url'];
                    $data['file_icon'] = '';
                    $data['file_type'] = '';
                    $data['file_size'] = '';
                    $data['filename'] = '';
                }
                if (isset($value['customer_group'])) {
                    foreach ($value['customer_group'] as $key => $allGroup) {
                          $groups_array[] = $allGroup;
                    }
            
                    $cgroup = implode(',', $groups_array);
                    $data['customer_group_id'] = $cgroup;
                    unset($groups_array);
                } else {
                    $data['customer_group_id'] = null;
                }

                if (isset($value['is_delete'])) {
                    if (isset($value['attachment_id'])) {
                        // print_r($model->getData());exit;
                        //$model->delete();
                        $model_del = $this->_objectManager->create('FME\Productattachments\Model\Productattachments');
                        $model_del->setId($value['attachment_id'])->delete();
                        unset($data);
                        unset($model_del);
                        continue;
                    } else {
                        $data['productattachments_id'] = null;
                        continue;
                    }
                } else {
                       // print_r($data);
                    $model->setData($data);
                    if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                              $model->setCreatedTime(date('y-m-d h:i:s'))
                              ->setUpdateTime(date('y-m-d h:i:s'));
                    }
                    $model->save();
                    $dont_delete[] = $model->getId();
                    unset($data);
                    unset($model);
                }
            }// exit;
        }
        
        $relatedAttachments = $this->productattachmentsModel->getRelatedAttachments($_product);
        //print_r($relatedAttachments);
        //exit();
        foreach ($relatedAttachments as $attachment){
            if(in_array($attachment['productattachments_id'], $dont_delete)){
                //let this id servive              
            }else{
                //delete that attachment by product
                /*if(isset($attachment['productattachments_id'])){
                    $model_del = $this->_objectManager->create('FME\Productattachments\Model\Products');
                    $model_del->setId($attachment['productattachments_id'])->delete();
                    unset($model_del);
                }*/
                $read = $this->_resource->getConnection('core_read');
                $myTable = $this->_resource->getTableName('productattachments_products');
                $read->delete(
                    $myTable,
                    ['productattachments_id = ?' => $attachment['productattachments_id'], 'product_id = ?' => $_product]
                );
            }
        }        
    }
}
