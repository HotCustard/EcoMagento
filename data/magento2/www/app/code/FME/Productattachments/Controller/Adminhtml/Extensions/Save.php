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

namespace FME\Productattachments\Controller\Adminhtml\Extensions;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\Productattachments\Model\Extensions;
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
            $id = $this->getRequest()->getParam('extension_id');

            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Block::STATUS_ENABLED;
            }
            if (empty($data['extension_id'])) {
                $data['extension_id'] = null;
            }      
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->create('FME\Productattachments\Model\Extensions')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Extension no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if (isset($data['icon'][0]['name']) && isset($data['icon'][0]['tmp_name'])) {
                $data['icon'] ='/productattachments/icons/'.$data['icon'][0]['name'];
            } elseif (isset($data['icon'][0]['name']) && !isset($data['icon'][0]['tmp_name'])) {
                $data['icon'] = $data['icon'][0]['name'];
            } else {
                  $data['icon'] = '';
            }
         //   print_r($data);exit;
            $model->setData($data);
            $this->inlineTranslation->suspend();
            try {
                    //////////////////// email
                $model->save();
                $this->messageManager->addSuccess(__('Extension Saved successfully'));
                $this->dataPersistor->clear('productattachments_extensions');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['extension_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
            }

            $this->dataPersistor->set('productattachments_extensions', $data);
            return $resultRedirect->setPath('*/*/edit', ['extension_id' => $this->getRequest()->getParam('extension_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
