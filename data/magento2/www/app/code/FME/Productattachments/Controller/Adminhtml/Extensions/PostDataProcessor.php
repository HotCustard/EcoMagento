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

class PostDataProcessor
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $messageManager
     */
    protected $messageManager;


    /**
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date               $dateFilter
     * @param \Magento\Framework\Message\ManagerInterface                  $messageManager
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
    ) {
        $this->dateFilter       = $dateFilter;
        $this->messageManager   = $messageManager;
        $this->validatorFactory = $validatorFactory;
    }//end __construct()


    /**
     * Check if required fields is not empty
     *
     * @param  array $data
     * @return boolean
     */
    public function filter($data)
    {
        $inputFilter = new \Zend_Filter_Input(
            [],
            [],
            $data
        );
        $data        = $inputFilter->getUnescaped();
        return $data;
    }//end filter()


    protected function _isAllowed()
    {
        return true;
    }//end _isAllowed()
}//end class
