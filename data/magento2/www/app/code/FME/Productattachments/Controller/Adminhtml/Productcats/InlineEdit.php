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
use FME\Productattachments\Model\Productcats as Productcats;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var Productcats
     */
    protected $productcats;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @param Context           $context
     * @param PostDataProcessor $dataProcessor
     * @param Productcats       $productcats
     * @param JsonFactory       $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Productcats $productcats,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->productcats   = $productcats;
        $this->jsonFactory   = $jsonFactory;
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

        foreach (array_keys($postItems) as $productcatsId) {
            /*
                @var \Magento\Productcats\Model\Category $productcats
            */
            $productcats = $this->productcats->load($productcatsId);
            try {
                $productcatsData = $this->dataProcessor->filter($postItems[$productcatsId]);
                $productcats->setData($productcatsData);
                $productcats->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCategoryId($productcats, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCategoryId($productcats, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCategoryId(
                    $productcats,
                    __('Something went wrong while saving the productcats.')
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
     * Add productcats title to error message
     *
     * @param  CategoryInterface $productcats
     * @param  string            $errorText
     * @return string
     */
    protected function getErrorWithCategoryId(Productcats $productcats, $errorText)
    {
        return '[Productcats ID: '.$productcats->getCategoryId().'] '.$errorText;
    }//end getErrorWithCategoryId()
    protected function _isAllowed()
    {
        return true;
    }//end _isAllowed()
}//end class
