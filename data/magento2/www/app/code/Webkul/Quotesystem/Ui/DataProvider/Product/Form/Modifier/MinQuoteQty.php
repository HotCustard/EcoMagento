<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Form;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Data provider for main panel of product page
 *
 * @api
 * @since 101.0.0
 */
class MinQuoteQty extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager     $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        \Webkul\Quotesystem\Helper\Data $helper
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     *
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeMinQtyField($meta);

        return $meta;
    }
    /**
     * {@inheritdoc}
     *
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }
    /**
     * Customize Weight filed
     *
     * @param  array $meta
     * @return array
     * @since  101.0.0
     */
    protected function customizeMinQtyField(array $meta)
    {
        $weightPath = $this->arrayManager->findPath('min_quote_qty', $meta, null, 'children');
        if ($weightPath) {
            $meta = $this->arrayManager->merge(
                $weightPath . static::META_CONFIG_PATH,
                $meta,
                [
                    'value' => $this->helper->getConfigMinQty(),
                    'dataScope' => 'min_quote_qty',
                    'validation' => [
                        // 'required-entry' => true,
                        'validate-digits' => true
                    ],
                    'additionalClasses' => 'admin__field-small',
                    'imports' => [
                        'disabled' => '!${$.provider}:' . self::DATA_SCOPE_PRODUCT
                            . '.quote_status:value'
                    ]
                ]
            );
        }

        return $meta;
    }
}
