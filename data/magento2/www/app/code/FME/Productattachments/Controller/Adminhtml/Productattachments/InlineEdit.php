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
use FME\Productattachments\Model\Productattachments as Productattachments;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var Productattachments
     */
    protected $productattachments;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @param Context            $context
     * @param PostDataProcessor  $dataProcessor
     * @param Productattachments $productattachments
     * @param JsonFactory        $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Productattachments $productattachments,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor      = $dataProcessor;
        $this->productattachments = $productattachments;
        $this->jsonFactory        = $jsonFactory;
    }//end __construct()
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /*
            @var \Magento\Framework\Controller\Result\Json $resultJson
        */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                 'messages' => [__('Please correct the data sent.')],
                 'error'    => true,
                ]
            );
        }
        foreach (array_keys($postItems) as $productattachmentsId) {
            /*
                @var \Magento\Productattachments\Model\Productattachments $productattachments
            */
            $productattachments = $this->productattachments->load($productattachmentsId);
            try {
                $productattachmentsData = $this->dataProcessor->filter($postItems[$productattachmentsId]);
                $productattachments->setData($productattachmentsData);
                $productattachments->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithProductattachmentsId($productattachments, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithProductattachmentsId($productattachments, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithProductattachmentsId(
                    $productattachments,
                    __('Something went wrong while saving the productattachments.')
                );
                $error      = true;
            }
        }//end foreach

        return $resultJson->setData(
            [
             'messages' => $messages,
             'error'    => $error,
            ]
        );
    }//end execute()
    /**
     * Add productattachments title to error message
     *
     * @param  ProductattachmentsInterface $productattachments
     * @param  string                      $errorText
     * @return string
     */
    protected function getErrorWithProductattachmentsId(Productattachments $productattachments, $errorText)
    {
        return '[Productattachments ID: '.$productattachments->getProductattachmentsId().'] '.$errorText;
    }//end getErrorWithProductattachmentsId()
    protected function _isAllowed()
    {
        return true;
    }//end _isAllowed()
}//end class
