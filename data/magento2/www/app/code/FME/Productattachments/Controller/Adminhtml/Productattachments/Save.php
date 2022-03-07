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

namespace FME\Productattachments\Controller\Adminhtml\Productattachments;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\Prodfaqs\Model\Topic;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $_repository;
    protected $dataPersistor;
    protected $scopeConfig; 
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
        $this->_productFactory = $productFactory;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
            $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
            if (isset($data['filename'][0]['name'])) {
                         $data['download_link'] = $data['filename'][0]['url'];
                if (isset($data['filename'][0]['tmp_name'])) {
                    $data['filename'] = 'productattachments/files/'.$data['filename'][0]['name'];
                    $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
                    $filePath = $baseScmsMediaURL . $data['filename'];
                    $fileconfig->Fileicon($filePath);
                    $data['file_icon'] = $fileconfig->displayIcon();
                    $data['file_type'] = $fileconfig->getType();
                    $data['file_size'] = $fileconfig->getSize();
                } else {
                    $data['filename'] = $data['filename'][0]['name'];
                }
            } elseif (!isset($data['filename'][0]['name'])) {
                $data['filename'] =null;
                $data['file_icon'] = null;
                $data['file_type'] = null;
                $data['file_size'] = null;
                $data['download_link'] = null;
            }
            //Save
            $data['block_position'] = 'additional,other';
            if (!empty($data['customer_group_id'])) {
                $data['customer_group_id'] = implode(',', $data['customer_group_id']);
            } else {
                $data['customer_group_id'] = null;
            }
            $id = $this->getRequest()->getParam('productattachments_id');
            if (empty($data['productattachments_id'])) {
                $data['productattachments_id'] = null;
            }
            $model = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Attachment no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            if (isset($data["category_products"])) {
                $cat_array = json_decode($data['category_products'], true);
                $pro_array = array_values($cat_array);
                $c=0;
                foreach ($cat_array as $key => $value) {
                    $pro_array[$c] = $key;
                    $c++;
                }
                unset($data['category_products']);
                $data['product_id'] = $pro_array;
                foreach ($pro_array as $key => $value) {
                  $product=$this->_productFactory->create()->load($value);
                    //$product = $objectManager->get('Magento\Catalog\Model\Product')->load($value);
                    $pro_arr[] = $product->getName();
                }
                if (isset($pro_arr)) {
                    $products_names = implode(',', $pro_arr);
                    $data['product_names'] = $products_names;
                }
            }
            if (isset($data['cmspage_ids'])) {
            } else {
                $data['cmspage_ids'] = (array)null;
            }
            $model->setData($data);

            if ($id) {
                $model->setId($id);
            }
            try {
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }
              
                $model->save();
                $this->messageManager->addSuccess(__('File was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find File to save'));
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
