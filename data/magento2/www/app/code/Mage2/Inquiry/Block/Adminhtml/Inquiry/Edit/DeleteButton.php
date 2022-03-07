<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 14/2/19
 * Time: 4:47 PM
 */
namespace Mage2\Inquiry\Block\Adminhtml\Inquiry\Edit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getInquiryId()) {
            $data = [
                'label' => __('Delete Inquiry'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['inquiry_id' => $this->getInquiryId()]);
    }
}
