<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 14/2/19
 * Time: 5:26 PM
 */
namespace Mage2\Inquiry\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Adminhtml cms blocks content block
 */
class Inquiry extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage2_Inquiry';
        $this->_controller = 'adminhtml_block';
        $this->_headerText = __('Product Inquiry');
        $this->_addButtonLabel = __('Add New Inquiry');
        parent::_construct();
    }
}
