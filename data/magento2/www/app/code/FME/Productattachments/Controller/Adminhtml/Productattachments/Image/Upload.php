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
namespace FME\Productattachments\Controller\Adminhtml\Productattachments\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload extends \Magento\Backend\App\Action
{
    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $baseTmpPath;

    protected $imageUploader;

    protected $logger;
   
    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \FME\Productattachments\Model\ImageUploader $imageUploader,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->logger    = $logger;
    }//end __construct()
    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }//end _isAllowed()
    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir('filename');

            $result['cookie'] = [
                                 'name'     => $this->_getSession()->getName(),
                                 'value'    => $this->_getSession()->getSessionId(),
                                 'lifetime' => $this->_getSession()->getCookieLifetime(),
                                 'path'     => $this->_getSession()->getCookiePath(),
                                 'domain'   => $this->_getSession()->getCookieDomain(),
                                ];
        } catch (\Exception $e) {
            $result = [
                       'error'     => $e->getMessage(),
                       'errorcode' => $e->getCode(),
                      ];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }//end execute()
}//end class
