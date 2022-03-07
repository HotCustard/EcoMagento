<?  namespace Mconnect\Priceslider\Helper;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;


class InitializeModuleObserver implements ObserverInterface{
		
		protected $_objectManager;
        protected $request;
	
		public function __construct( 
			\Magento\Framework\ObjectManager\ObjectManager $objectManager,
            \Magento\Framework\App\Request\Http $request,
			$data = []){
				$this->_objectManager = $objectManager;
                $this->request = $request;
		}	 
	 
	 
	 public function execute(Observer $observer){
		if($this->isProductionUrl($this->getRequestHost())) {
            return true;
        }	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		if($this->getLicenceKey() && $this->getSerialKey()){
		
			if( !(($this->simple_encrypt($this->getRequestHost()."Mconnect_Priceslider",substr($this->getLicenceKey(),1,16)) == $this->getSerialKey()) || ($this->simple_encrypt($this->getWebsiteHost()."Mconnect_Priceslider",substr($this->getLicenceKey(),1,16)) == $this->getSerialKey())) ){
				$objectManager->get("\Magento\Framework\Message\ManagerInterface")->addError( __("Licence Key Is Invalid For Mconnect Priceslider extension"));	
				$this->_disableModule("Mconnect_Priceslider");
				return false;
			}else{
				$this->_enableModule("Mconnect_Priceslider");
				return true;
			}
		
		}else{
			$objectManager->get("\Magento\Framework\Message\ManagerInterface")->addError( __("Licence Key Is Invalid For Mconnect Priceslider extension"));
			$this->_disableModule("Mconnect_Priceslider");
			return false;		
		}
		return true;
	}

	public function getLicenceKey(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
		return $objectManager->get("Magento\Framework\App\Config\ScopeConfigInterface")->getValue("mconnect_priceslider/active/licence_key", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore());
	}	
	public function getSerialKey(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
		return $objectManager->get("Magento\Framework\App\Config\ScopeConfigInterface")->getValue("mconnect_priceslider/active/serial_key", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore());
	}
	public function getMconnectModuleName(){	
		return str_replace("_"," ",$this->_getModuleName());
	}
	
	public function simple_encrypt($host, $key)
	{
		$abcd = array("+", "/", "\/", "=");
    return str_replace($abcd , "",trim(base64_encode(@mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $host, MCRYPT_MODE_ECB, @mcrypt_create_iv(@mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))));
	}
	
	protected function _disableModule($moduleName) {			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
			$outputPath = "advanced/modules_disable_output/$moduleName";
			
			$config = $objectManager->create("Magento\Config\Model\ResourceModel\Config");
			$config->saveConfig($outputPath,1,"default",0);
	}

	protected function _enableModule($moduleName) {			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
			$outputPath = "advanced/modules_disable_output/$moduleName";
			
			$config = $objectManager->create("Magento\Config\Model\ResourceModel\Config");
			$config->saveConfig($outputPath,0,"default",0);
	}
	public function isProductionUrl($host) {
        $isRealHost = false;
        if (strpos($host, "localhost") !== false) {
            $isRealHost = true;
        } else if(is_numeric(str_replace(".","",$host))) {
            $isRealHost = true;
        }
        return $isRealHost;
    }
	public function getRequestHost() {
		if (isset($_SERVER["HTTP_X_FORWARDED_HOST"]) && !empty($_SERVER["HTTP_X_FORWARDED_HOST"])) {
			$host = $_SERVER["HTTP_X_FORWARDED_HOST"];
			$hostExplode = explode(",", $host);
			$host = trim(end($hostExplode));
		}
		else {
			if (isset($_SERVER["HTTP_HOST"]) && !empty($_SERVER["HTTP_HOST"])) {
				$host = $_SERVER["HTTP_HOST"];
			}
			else {
				if (isset($_SERVER["SERVER_NAME"]) && !empty($_SERVER["SERVER_NAME"])) {
					$host = $_SERVER["SERVER_NAME"];
				}
				else {
					if (isset($_SERVER["SERVER_ADDR"]) && !empty($_SERVER["SERVER_ADDR"])) {
						$host = $_SERVER["SERVER_ADDR"];
					}
					else {
						$host = "";
					}
				}
			}
		}		
		$host = preg_replace("/:\d+$/", "", $host);	
		
		if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $host, $regs)) {
			return $regs["domain"];
		}
      return $host;
	}
    
    public function getWebsiteHost() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$host = $objectManager->get("Magento\Framework\App\Config\ScopeConfigInterface")->getValue("web/unsecure/base_url", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore());
		
        $host = str_replace( parse_url( $host, PHP_URL_SCHEME ) . "://", "", $host );
        $host = rtrim($host,"/");
        
		if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $host, $regs)) {
			return $regs["domain"];
		}
        return $host;
	}
    
    public function getStore(){
        $store = $this->request->getParam("store", 0);
        return $store;
    }
    
	public function getRequestUrl() {
		$protocol = "http";
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$protocol = "https";
		}
	
		$portNo = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $this->getRequestHost() . $portNo . $_SERVER["REQUEST_URI"];
	}
	
	public function getEncData(){	
		return "COMPILER_HALT_OFF";	
	}
	public function getDecryptDataSet(){	
		if (isset($_SERVER["HTTP_X_FORWARDED_HOST"]) && !empty($_SERVER["HTTP_X_FORWARDED_HOST"])) {
			$host = $_SERVER["HTTP_X_FORWARDED_HOST"];
			$hostExplode = explode(",", $host);
			$host = trim(end($hostExplode));
		}
		else {
			if (isset($_SERVER["HTTP_HOST"]) && !empty($_SERVER["HTTP_HOST"])) {
				$host = $_SERVER["HTTP_HOST"];
			}
			else {
				if (isset($_SERVER["SERVER_NAME"]) && !empty($_SERVER["SERVER_NAME"])) {
					$host = $_SERVER["SERVER_NAME"];
				}
				else {
					if (isset($_SERVER["SERVER_ADDR"]) && !empty($_SERVER["SERVER_ADDR"])) {
						$host = $_SERVER["SERVER_ADDR"];
					}
					else {
						$host = "";
					}
				}
			}
		}
		$licenceKeyCheck = $this->getLicenceKey();
		$serialKeyCheck = "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";
		$encryptBoth = $licenceKeyCheck.$serialKeyCheck."mconnectAFCISPCKXJEMLRIJDL";
		$sendData = base_64encode($encryptBoth);
		strrev($sendData);
		$checkData = "==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";
		return $checkData;
		
	}
	 
   
} ?>