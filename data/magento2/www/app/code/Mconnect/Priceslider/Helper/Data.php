<?php
namespace Mconnect\Priceslider\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Search Suite Autocomplete config data helper
 * @package Mconnect\Priceslider\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /*-----------------------------popup type------------------------*/
    const XML_PATH_ALNAVIGATION_ENABLE= 'mconnect_priceslider/general/aln_price';
    /**
     * getPricesliderEnable
     *
     */
    public function getPricesliderEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ALNAVIGATION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
