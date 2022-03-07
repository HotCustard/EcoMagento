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

namespace FME\Productattachments\Model\Config\Source;

class Maxvalues implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $groups_array = [];       
            $groups_array[] = [
                               'value' => ini_get("upload_max_filesize"),
                               'label' => 'upload_max_filesize',
                              ];
            $groups_array[] = [
                               'value' => ini_get("post_max_size"),
                               'label' => 'post_max_size',
                              ];
            return $groups_array;
    }//end toOptionArray()
}//end class
