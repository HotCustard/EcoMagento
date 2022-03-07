<?php
namespace Mconnect\Priceslider\Block;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @param FilterInterface $filter
     * @return string
     */
     
    protected $storeManager;
    protected $currencySymbol;
    protected $localeCurrency;
    
    public $objectManager = null;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currencySymbol,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        ObjectManager $objectManager,
        array $data = []
    ) {
        $this->_listProduct = $listProduct;
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->currencySymbol = $currencySymbol;
        $this->localecurrency = $localeCurrency;
        parent::__construct(
            $context,
            $data
        );
    }
    
     public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }
    
    public function getMaxMinPrice($filter)
    {
        $getPrice=$this->getPriceRange($filter);
        $limitPrice = ['minLimitSet' => (($getPrice['min'])-1) ,
        'maxLimitSet'=>(($getPrice['max'])+1)];
        
         return $limitPrice;
    }
    
    public function getPriceRange($filter)
    {
        $Filterprice = ['min' => 0 , 'max'=>0];
        if ($filter->getName() == 'Price') {
            $priceArr = $filter->getResource()->loadPrices(10000000000);
            
            $Filterprice['min'] = reset($priceArr);
            $Filterprice['max'] = end($priceArr);
        }
        return $Filterprice;
    }
    
    public function getFilterUrl()
    {
         $query = ['price'=> ''];
         return $this->getUrl('*/*/*', ['_current' => true,
         '_use_rewrite' => true, '_query' => $query]);
    }
    
    public function getStoreCurrency(){
        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }
}
