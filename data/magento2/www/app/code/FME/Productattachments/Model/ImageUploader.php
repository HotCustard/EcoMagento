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
namespace FME\Productattachments\Model;

/**
 * Catalog image uploader
 */
class ImageUploader
{
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Base tmp path
     *
     * @var string
     */
    protected $baseTmpPath;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Allowed extensions
     *
     * @var string
     */
    protected $allowedExtensions;
    protected $extensions;

    /**
     * ImageUploader constructor
     *
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem                      $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory   $uploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param string                                             $baseTmpPath
     * @param string                                             $basePath
     * @param string[]                                           $allowedExtensions
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productattachments\Model\Extensions $extensions,
        \Psr\Log\LoggerInterface $logger,
        $baseTmpPath,
        $basePath,
        $allowedExtensions
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory          = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory         = $uploaderFactory;
        $this->storeManager            = $storeManager;
        $this->logger                  = $logger;
        $this->baseTmpPath             = $baseTmpPath;
        $this->extensions = $extensions;
        $this->basePath                = $basePath;
        $this->allowedExtensions       = $allowedExtensions;
    }//end __construct()
    /**
     * Set base tmp path
     *
     * @param string $baseTmpPath
     *
     * @return void
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }//end setBaseTmpPath()
    /**
     * Set base path
     *
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }//end setBasePath()
    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     *
     * @return void
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }//end setAllowedExtensions()
    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }//end getBaseTmpPath()
    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }//end getBasePath()
    /**
     * Retrieve base path
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }//end getAllowedExtensions()
    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/').'/'.ltrim($imageName, '/');
    }//end getFilePath()
    /**
     * Checking file for moving and move it
     *
     * @param string $imageName
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath    = $this->getBasePath();

        $baseImagePath    = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);
        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }//end moveFileFromTmp()
    /**
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
       $result = $this->extensions->getExtensions();
        $ext_arr = ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'zip', 'ppt', 'pptx', 'flv', 'mp3', 'mp4', 'csv', 'html', 'bmp', 'txt', 'rtf', 'psd','dvi', 'ods'];
        foreach ($result as $value) {
             $ext_arr[] = $value['type'];
        }
        if (!isset($ext_arr)) {
             $ext_arr[] ='';
        }
        $flag        = 0;
        $baseTmpPath = $this->getBaseTmpPath();
      //  $arr[0] = $fileId;
       // $this->logger->log(100,print_r($ext_arr,true));
 
     //   print_r($ext_arr);exit;
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        if ($fileId == 'icon') {
             $uploader->setAllowedExtensions(['jpg', 'jpeg','gif' ,'png']);
            $uploader->setAllowRenameFiles(true);

            $result      = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
           // $baseTmpPath = 'productattachments/icons';
        } elseif ($fileId == 'category_image') {
             $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);

            $result      = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
            //$baseTmpPath = 'productattachments/cats';
        } elseif ($fileId == 'filename') {
             $uploader->setAllowedExtensions($ext_arr);
            $uploader->setAllowRenameFiles(true);

            $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        }

         //$this->logger->log(100,print_r($result,true));
        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /*
            * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path']     = str_replace('\\', '/', $result['path']);
        $result['url']      = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).$this->getFilePath($baseTmpPath, $result['file']);
        $result['name']     = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/').'/'.ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }//end saveFileToTmpDir()
}//end class
