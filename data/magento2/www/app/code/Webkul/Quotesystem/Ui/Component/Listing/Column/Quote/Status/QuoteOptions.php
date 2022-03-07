<?php
/**
 * Quote Options
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Ui\Component\Listing\Column\Quote\Status;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class QuoteOptions
 */
class QuoteOptions implements OptionSourceInterface
{
    const STATUS_UNAPPROVED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_DECLINE = 3;
    const STATUS_SOLD = 4;
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getOptionArray()
    {
        return [
            self::STATUS_UNAPPROVED => __('Unapproved'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_DECLINE => __('Declined'),
            self::STATUS_SOLD => __('Sold')
        ];
    }
}
