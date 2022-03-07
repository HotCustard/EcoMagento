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
namespace FME\Productattachments\Controller\Adminhtml\Productattachments;

use FME\Productattachments\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDelete
 */
class MassDisable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'productattachments_id';

    /**
     * ResourceModel collection
     *
     * @var string
     */
    protected $collection = 'FME\Productattachments\Model\ResourceModel\Productattachments\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'FME\Productattachments\Model\Productattachments';

    /**
     * Item status
     *
     * @var boolean
     */
    protected $status = 0;
}//end class
