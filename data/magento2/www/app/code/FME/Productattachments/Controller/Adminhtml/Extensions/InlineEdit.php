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
use FME\Productattachments\Model\Extensions as Extensions;
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
    protected $extensions;

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
        Extensions $extensions,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->extensions   = $extensions;
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

        foreach (array_keys($postItems) as $extensionsid) {
            /*
                @var \Magento\Productcats\Model\Category $extensions
            */
            $extensions = $this->extensions->load($extensionsid);
            try {
                $extensionsData = $this->dataProcessor->filter($postItems[$extensionsid]);
                $extensions->setData($extensionsData);
                $extensions->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCategoryId($extensions, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCategoryId($extensions, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCategoryId(
                    $extensions,
                    __('Something went wrong while saving the extension.')
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
     * Add extensions title to error message
     *
     * @param  CategoryInterface $extensions
     * @param  string            $errorText
     * @return string
     */
    protected function getErrorWithCategoryId(Extensions $extensions, $errorText)
    {
        return '[Extensions ID: '.$extensions->getCategoryId().'] '.$errorText;
    }//end getErrorWithCategoryId()


    protected function _isAllowed()
    {
        return true;
    }//end _isAllowed()
}//end class
