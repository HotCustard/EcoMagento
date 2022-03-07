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
namespace FME\Productattachments\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \FME\Productattachments\Controller\Index
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id               = $this->getRequest()->getParam('id');
        $model            = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->load($id);
        $mediaDirectory   = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
        $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
        // Checking Customer Group to download the attachment
        $customer_group_id = (array)$model['customer_group_id'];
        $customer_group_id_array = $customer_group_id;
        $groupid = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerGroupId();
        if ($customer_group_id !== null && !in_array($groupid, $customer_group_id_array)) {
            $cgroup    = $this->_objectManager->create('Magento\Customer\Model\Group')->load($customer_group_id);
            $groupName = $cgroup->getCode();
            $message = __(
                'This attachment is for only '.$groupName.' User Group to download'
            );
            $this->messageManager->addError($message);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        // Update Download Counter
        $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->updateCounter($id);
        $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
        $filePath   = $mediaDirectory->getAbsolutePath($model['filename']);
    //    $downloadLink = $model['download_link'];
        $fileconfig->Fileicon($filePath);
        $fileName = $model['filename'];
        $fileType = $fileconfig->getType();
        $fileSize = $fileconfig->getSize();
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
            ini_set('zlib.output_compression', 'Off');
        }
        header("Content-Type: $fileType");
        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Disposition: attachment; filename=$fileName");
        header('Content-Transfer-Encoding: binary');
        header('Content-length: '.filesize($filePath));
        // read file
        readfile($filePath);
    }//end execute()
}//end class
