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
namespace FME\Productattachments\Model\System;

class Layouts extends \Magento\Framework\ObjectManager\ObjectManager
{
    /** ---Functions--- */

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Empty'),
                'value' => 'empty'
            ],
            [
                'label' => __('1 column'),
                'value' => '1column'
            ],
            [
                'label' => __('2 columns with left bar'),
                'value' => '2columns-left'
            ],
            [
                'label' => __('2 column with right bar'),
                'value' => '2columns-right'
            ],
            [
                'label' => __('3 columns'),
                'value' => '3columns'
            ]
        ];
    }
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config
    ) {
        parent::__construct($factory, $config);
        $this->_objectManager = $objectManager;
    }
}
