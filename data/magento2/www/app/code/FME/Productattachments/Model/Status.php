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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Status implements OptionSourceInterface
{

    /**
     * @var \Magento\Cms\Model\
     */
    protected $productattachments;
    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\ $productattachments
     */
    public function __construct(\FME\Productattachments\Model\Productattachments $productattachments)
    {
        $this->productattachments = $productattachments;
    }//end __construct()

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[]        = [
                             'label' => '',
                             'value' => '',
                            ];
        $availableOptions = $this->productattachments->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                          'label' => $value,
                          'value' => $key,
                         ];
        }

        return $options;
    }//end toOptionArray()
}//end class
