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
    namespace Webkul\Quotesystem\Ui\Component\Listing\Column;

    use Magento\Framework\View\Element\UiComponent\ContextInterface;
    use Magento\Framework\View\Element\UiComponentFactory;
    use Magento\Ui\Component\Listing\Columns\Column;

class QuotePrice extends Column
{
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    protected $helper;

    /**
     * @param ContextInterface                            $context
     * @param UiComponentFactory                          $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Webkul\Quotesystem\Helper\Data             $helper
     * @param array                                       $components
     * @param array                                       $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\Quotesystem\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }
    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $store = $this->storeManager->getStore(
                $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            );
            $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());

            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['quote_currency'] != $store->getBaseCurrencyCode() && $item['quote_currency'] != '') {
                    $basePrice = $this->helper->getBaseCurrencyPrice($item['quote_price'], $item['quote_currency']);
                    $item['quote_price'] = $currency->toCurrency(sprintf("%f", $basePrice));
                } else {
                    $item['quote_price'] = $currency->toCurrency(sprintf("%f", $item['quote_price']));
                }
            }
        }
        return $dataSource;
    }
}
