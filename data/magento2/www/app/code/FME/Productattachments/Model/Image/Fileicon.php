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
namespace FME\Productattachments\Model\Image;

use Magento\Framework\View\Asset\Repository;

class Fileicon extends \Magento\Framework\ObjectManager\ObjectManager
{
    protected $_repository;
    protected $filename;
    protected $size;
    protected $url = 'FME_Productattachments::images/icons/';
    var $icons = [
        // Microsoft Office
        'doc' => ['doc', 'Word Document'],
        'xls' => ['xls', 'Excel Spreadsheet'],
        'ppt' => ['ppt', 'PowerPoint Presentation'],
        'pps' => ['ppt', 'PowerPoint Presentation'],
        'pot' => ['ppt', 'PowerPoint Presentation'],
        'mdb' => ['access', 'Access Database'],
        'vsd' => ['visio', 'Visio Document'],
        'dwt' => ['dwt', 'Adobe Dreamweaver'],
//	'xxxx' => array('project', 'Project Document'), 	// dont remember type...
        'rtf' => ['rtf', 'RTF File'],
        // XML
        'htm' => ['htm', 'HTML Document'],
        'html' => ['htm', 'HTML Document'],
        'xml' => ['xml', 'XML Document'],
        // Images
        'jpg' => ['image', 'JPEG Image'],
        'jpe' => ['image', 'JPEG Image'],
        'jpeg' => ['image', 'JPEG Image'],
        'gif' => ['image', 'GIF Image'],
        'bmp' => ['image', 'Windows Bitmap Image'],
        'png' => ['image', 'PNG Image'],
        'tif' => ['image', 'TIFF Image'],
        'tiff' => ['image', 'TIFF Image'],
        // Audio
        'mp3' => ['audio', 'MP3 Audio'],
        'wma' => ['audio', 'WMA Audio'],
        'mid' => ['audio', 'MIDI Sequence'],
        'midi' => ['audio', 'MIDI Sequence'],
        'rmi' => ['audio', 'MIDI Sequence'],
        'au' => ['audio', 'AU Sound'],
        'snd' => ['audio', 'AU Sound'],
        // Video
        'mpeg' => ['video', 'MPEG Video'],
        'mpg' => ['video', 'MPEG Video'],
        'mpe' => ['video', 'MPEG Video'],
        'wmv' => ['video', 'Windows Media File'],
        'avi' => ['video', 'AVI Video'],
        // Archives
        'zip' => ['zip', 'ZIP Archive'],
        'rar' => ['zip', 'RAR Archive'],
        'cab' => ['zip', 'CAB Archive'],
        'gz' => ['zip', 'GZIP Archive'],
        'tar' => ['zip', 'TAR Archive'],
        'zip' => ['zip', 'ZIP Archive'],
        // OpenOffice
        'sdw' => ['oo-write', 'OpenOffice Writer document'],
        'sda' => ['oo-draw', 'OpenOffice Draw document'],
        'sdc' => ['oo-calc', 'OpenOffice Calc spreadsheet'],
        'sdd' => ['oo-impress', 'OpenOffice Impress presentation'],
        'sdp' => ['oo-impress', 'OpenOffice Impress presentation'],
        'ods' => ['oo-calc', 'OpenOffice Calc spreadsheet'],
        // Others
        'txt' => ['txt', 'Text Document'],
        'js' => ['js', 'Javascript Document'],
        'dll' => ['binary', 'Binary File'],
        'pdf' => ['pdf', 'Adobe Acrobat Document'],
        'php' => ['php', 'PHP Script'],
        'ps' => ['ps', 'Postscript File'],
        'dvi' => ['dvi', 'DVI File'],
        'swf' => ['swf', 'Flash'],
        'chm' => ['chm', 'Compiled HTML Help'],
        // Unkown
        'default' => ['txt', 'Unkown Document'],
    ];
    public function Fileicon($filename)
    {
        $this->filename = $filename;
        $this->size = filesize($this->filename);
    }
    public function setIconUrl($url)
    {
        $this->url = $this->_storeManager->getStore()->getBaseUrl('') . $url;
    }
    public function getSize()
    {
        return $this->evalSize($this->size);
    }
    public function getTime()
    {
        return fileatime($this->filename);
    }
    public function getName()
    {
        return $this->filename;
    }
    public function getOwner()
    {
        return fileowner($this->filename);
    }
    public function getGroup()
    {
        return filegroup($this->filename);
    }
    public function getType()
    {
        $file_array = preg_split("/\./", $this->filename);
        $suffix = $file_array[count($file_array) - 1];
        if (strlen($suffix) > 0) {
            return $suffix;
        } else {
            return false;
        }
    }
    public function evalSize($size)
    {
        if ($size >= 1073741824) {
            return round($size / 1073741824 * 100) / 100 . " GB";
        } elseif ($size >= 1048576) {
            return round($size / 1048576 * 100) / 100 . " MB";
        } elseif ($size >= 1024) {
            return round($size / 1024 * 100) / 100 . " KB";
        } else {
            return $size . " BYTE";
        }
    }
    public function getIcon()
    {
        $extension = $this->getType();

        if (key_exists($extension, $this->icons)) {
            return $this->icons[$extension];
        } else {
            return $this->icons['default'];
        }
    }
    public function displayIcon()
    {
        $array = $this->getIcon(); // print_r($array);exit;
        $params = ['_secure' => $this->_request->isSecure()];
        //echo $this->_repository->getUrlWithParams($this->url, $params);exit;
        // echo $this->_repository->getUrlWithParams($this->url, $params)
        // . '/'.$array[0]
        // . '.gif';exit;
        return '<img src="' . $this->_repository->getUrlWithParams($this->url, $params)
                . '/' . $array[0]
                . '.gif" alt="' . $array[1] . '" />';
    }
    protected $_request;
    public $_storeManager;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config,
        Repository $repository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($factory, $config);
        $this->_objectManager = $objectManager;
        $this->_repository = $repository;
        $this->_storeManager=$storeManager;
        $this->_request = $request;
    }
}
