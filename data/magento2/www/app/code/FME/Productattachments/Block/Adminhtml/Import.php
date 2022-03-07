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

namespace FME\Productattachments\Block\Adminhtml;

use Magento\Backend\Block\Widget\Tabs;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;
use FME\Productattachments\Helper\Data;

class Import extends Tabs
{
    public $helper;
    public $path = '/productattachments/';
    public function __construct(
        Context $context,
        EncoderInterface $encoderInterface,
        Session $authSession,
        Data $helper
    ) {
        parent::__construct($context, $encoderInterface, $authSession);
        $this->helper = $helper;
        $this->setTemplate('FME_Productattachments::import.phtml');
        $this->setFormAction($this->_urlBuilder->getUrl('*/import/import'));
    }//end __construct()
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }//end _beforeToHtml()
}//end class
