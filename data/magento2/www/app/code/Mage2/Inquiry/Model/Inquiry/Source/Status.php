<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 25/2/19
 * Time: 8:06 PM
 */
namespace Mage2\Inquiry\Model\Inquiry\Source;

use Mage2\Inquiry\Model\Inquiry;
use Magento\Cms\Model\Block;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Status implements OptionSourceInterface
{
    /**
     * @var Block
     */
    protected $Inquiry;

    /**
     * Constructor
     *
     * @param Inquiry $Inquiry
     */
    public function __construct(Inquiry $Inquiry)
    {
        $this->inquiry = $Inquiry;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->inquiry->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
