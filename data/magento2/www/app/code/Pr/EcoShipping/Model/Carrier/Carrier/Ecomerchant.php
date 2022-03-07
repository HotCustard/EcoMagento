<?php
namespace Pr\EcoShipping\Model\Carrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;


class Ecomerchant extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements CarrierInterface
{
    protected $_code = 'pr_ecoshipping_ecomerchant';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $shippingRateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $quoteQuoteAddressRateResultMethodFactory;

    /**
     * @var \Pr\EcoShipping\Model\Resource\Carrier\Ecomerchant 
     */
    protected $shippingResourceCarrierEcomerchantFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Option\ValueFactory
     */
    protected $catalogProductOptionValueFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $catalogProductFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $shippingRateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $quoteQuoteAddressRateResultMethodFactory,
        \Pr\EcoShipping\Model\Resource\Carrier\Ecomerchant $shippingResourceCarrierEcomerchantFactory,
        \Magento\Catalog\Model\Product\Option\ValueFactory $catalogProductOptionValueFactory,
        \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
        array $data = []
    ) {
        $this->shippingRateResultFactory = $shippingRateResultFactory;
        $this->quoteQuoteAddressRateResultMethodFactory = $quoteQuoteAddressRateResultMethodFactory;
        $this->shippingResourceCarrierEcomerchantFactory = $shippingResourceCarrierEcomerchantFactory;
        $this->catalogProductOptionValueFactory = $catalogProductOptionValueFactory;
        $this->catalogProductFactory = $catalogProductFactory;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    
    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $data
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if ($this->isActive() == true)
        {
            // this object will be returned as result of this method containing all the shipping rates of this method
            $result = $this->shippingRateResultFactory->create();
            
            if ($rate = $this->getRate($request))
            {
                // Calculate the weight
                $volume = $this->getVolume($request);
                $request->setPackageWeight($this->getWeight($request));
                $methods = array();
                
                if ($rate['price_economy'] > 0) {
                    $methods['pallet_economy'] = array(
                        'id'=>'pallet_economy',
                        'title'=>'Pallet (Economy)',
                        'price'=>$rate['price_economy'] * ceil($volume / 100),
                    );
                }
                
                if ($rate['price_next_day'] > 0) {
                    $methods['pallet_next_day'] = array(
                        'id'=>'pallet_next_day',
                        'title'=>'Pallet (Next Day)',
                        'price'=>$rate['price_next_day'] * ceil($volume / 100),
                    );
                }

                if ($rate['price_0kg'] > 0 and $request->getPackageWeight() < $this->getConfigData('max_package_weight')) {
                    $methods['courier'] = array(
                        'id'=>'courier',
                        'title'=>'Courier',
                        'price'=>$this->getCourierPrice($request->getPackageWeight(), $rate['price_0kg'], $rate['price_20kg'], $rate['price_100kg']),
                    );
                }
                
                if ($_method = $this->getCheapestMethod($methods)) {
                    // record carrier information
                    if ($_method['price'] > 0) {
                        $method = $this->quoteQuoteAddressRateResultMethodFactory->create();
                        $method->setCarrier($this->getCarrierCode());
                        $method->setCarrierTitle($this->getConfigData('title'));
                        $method->setMethod($_method['id']);
                        $method->setMethodTitle($_method['title']);
                        $method->setPrice($this->getFinalPriceWithHandlingFee($_method['price']));
                        $result->append($method);
                    } else {
                        // record carrier information
                        $method = $this->quoteQuoteAddressRateResultMethodFactory->create();
                        $method->setCarrier($this->getCarrierCode());
                        $method->setCarrierTitle($this->getConfigData('title'));
                        $method->setMethod('free_shipping');
                        $method->setMethodTitle('Shipping Included');
                        $method->setPrice(0);
                        $result->append($method);
                    }
                }
            }
            return $result;
        }
        return false;
    }
    
    /**
     * This method is used when viewing / listing Shipping Methods with Codes programmatically
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }
    
    public function getCheapestMethod(array $methods)
    {
        $result = null;
        foreach ($methods as $method)
        {
            if ($result == null and $method['price'] >= 0)
            {
                $result = $method;
            }
            elseif ($method['price'] >= 0 and $method['price'] < $result['price'])
            {
                $result = $method;
            }
        }
        return $result;
    }
    
    public function getCourierPrice($weight, $price_0kg, $price_20kg, $price_100kg)
    {
        if ($weight > 0 and $weight < 20)
        {
            return $price_0kg;
        }
        elseif ($weight >= 20 and $weight < 100)
        {
            return array_sum(array(
                $price_0kg,
                $price_20kg * ($weight - 20),
            ));
        }
        elseif ($weight >= 100)
        {
            return array_sum(array(
                $price_0kg,
                $price_20kg * 80,
                $price_100kg * ($weight - 100),
            ));
        }
        else
        {
            return 0;
        }
    }
    
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        return $this->shippingResourceCarrierEcomerchantFactory->create()->getRate($request);
    }
    
    public function getShippingCode(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        foreach ($request->getAllItems() as $item)
        {
            foreach ($item->getOptions() as $option)
            {
                if ($option->code == 'info_buyRequest')
                {
                    $info_buyRequest = unserialize($option->value);
                    foreach ($info_buyRequest['options'] as $id)
                    {
                        if ($option_value = $this->catalogProductOptionValueFactory->create()->load($id))
                        {
                            echo '<pre>';
                            print_r($option_value->getShippingCode());
                            echo '</pre>';
                        }
                    }
                }
            }
        }
    }
    
    public function getVolume(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $result = array();
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item)
            {
                $volume = 0;
                $subject = $this->catalogProductFactory->create()->load($item->getProduct()->getId())->getShippingCode();
                if (preg_match('/^P(?<pallet>[-+]?[0-9]*\.?[0-9]+)C(?<courier>[-+]?[0-9]*\.?[0-9]+)$/', trim($subject), $matches))
                {
                    $volume = $matches['pallet'] * $item->getQty();
                }
                
                foreach ($item->getOptions() as $option)
                {
                    if ($option->code == 'info_buyRequest')
                    {
                        $info_buyRequest = unserialize($option->value);
                        if (isset($info_buyRequest['options']))
                        {
                            foreach ($info_buyRequest['options'] as $id)
                            {
                                if ($option_value = $this->catalogProductOptionValueFactory->create()->load($id))
                                {
                                    $subject = $option_value->getShippingCode();
                                    if (preg_match('/^P(?<pallet>[-+]?[0-9]*\.?[0-9]+)C(?<courier>[-+]?[0-9]*\.?[0-9]+)$/', trim($subject), $matches))
                                    {
                                        $volume = $matches['pallet'] * $item->getQty();
                                    }
                                }
                            }
                        }
                    }
                }
                $result[] = $volume;
            }
        }
        return array_sum($result);
    }
    
    public function getWeight(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $result = array();
        if ($request->getAllItems())
        {
            foreach ($request->getAllItems() as $item)
            {
                $weight = 0;
                $subject = $this->catalogProductFactory->create()->load($item->getProduct()->getId())->getShippingCode();
                if (preg_match('/^P(?<pallet>[-+]?[0-9]*\.?[0-9]+)C(?<courier>[-+]?[0-9]*\.?[0-9]+)$/', trim($subject), $matches))
                {
                    $weight = $matches['courier'] * $item->getQty();
                }
                foreach ($item->getOptions() as $option)
                {
                    if ($option->code == 'info_buyRequest')
                    {
                        $info_buyRequest = unserialize($option->value);
                        if (isset($info_buyRequest['options']))
                        {
                            foreach ($info_buyRequest['options'] as $id)
                            {
                                if ($option_value = $this->catalogProductOptionValueFactory->create()->load($id))
                                {
                                    $subject = $option_value->getShippingCode();
                                    if (preg_match('/^P(?<pallet>[-+]?[0-9]*\.?[0-9]+)C(?<courier>[-+]?[0-9]*\.?[0-9]+)$/', trim($subject), $matches))
                                    {
                                        $weight = $matches['courier'] * $item->getQty();
                                    }
                                }
                            }
                        }
                    }
                }
                $result[] = $weight;
            }
        }
        return array_sum($result);
    }
}