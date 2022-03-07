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

namespace FME\Productattachments\Controller\Adminhtml\Productcats;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\Productattachments\Model\Productcats;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    protected $scopeConfig; 
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {
        $this->dataPersistor = $dataPersistor;
         $this->scopeConfig = $scopeConfig;
         $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
         $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('category_id');
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Block::STATUS_ENABLED;
            }
            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }      
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->create('FME\Productattachments\Model\Productcats')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Category no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if (isset($data['category_image'][0]['name']) && isset($data['category_image'][0]['tmp_name'])) {
                $data['category_image'] ='/productattachments/cats/'.$data['category_image'][0]['name'];
            } elseif (isset($data['category_image'][0]['name']) && !isset($data['category_image'][0]['tmp_name'])) {
                $data['category_image'] = $data['category_image'][0]['name'];
            } else {
                  $data['category_image'] = '';
            }

            if (!isset($data['category_url_key']) || (isset($data['category_url_key']) && $data['category_url_key'] !== 'Default_Category')) {
                    $data['category_url_key'] = $data['category_name'];

                    $data['category_url_key'] = $this->_objectManager->create('FME\Productattachments\Helper\Data')
                            ->nameToUrlKey($data['category_url_key']);
            }
            if (isset($data['parent_category_id'])) {
              $defaultCategory = $this->_objectManager->create('FME\Productattachments\Helper\Data')->getDefaultCategory();
              if ($data['parent_category_id'] == $defaultCategory->getId()) {
                  $data['level'] = 1;
              }
            }
            $model->setData($data);
            $this->inlineTranslation->suspend();
            try {
                    //////////////////// email
                $model->save();
                $this->messageManager->addSuccess(__('Category Saved successfully'));
                $this->dataPersistor->clear('productattachments_cats');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['category_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
            }
            $this->dataPersistor->set('productattachments_cats', $data);
            return $resultRedirect->setPath('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
