<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_Productattachments
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/

namespace FME\Productattachments\Controller;

use FME\Productattachments\Helper\Data;

/**
 * Cms Controller Router
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Product Attachments factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_productattachmentsFactory;
    
    /**
     * Product Attachments Category factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_productcatsFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    protected $_helper;
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \FME\Productattachments\Model\ProductattachmentsFactory $productattachmentsFactory,
        \FME\Productattachments\Model\ProductcatsFactory $productcatsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_productattachmentsFactory = $productattachmentsFactory;
        $this->_productcatsFactory = $productcatsFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->_helper = $helper;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $frontName = $this->_helper->getFrontName();
        $path = trim($request->getPathInfo(), '/');
        list($identifier, $cats, $attachments) = array_pad(explode('/', $path, 3), 3, null);
        if ($identifier !== $frontName) {
            return null;
        }
        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $identifier = $condition->getIdentifier();
        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
        }
        if (!$condition->getContinue()) {
            return null;
        }
        if ($cats !== null) {
            /** @var \FME\Productattachments\Model\Productcats $productcats */
            $productcats = $this->_productcatsFactory->create();
            $productcatsId = $productcats->checkIdentifier($cats, $this->_storeManager->getStore()->getId());
            if (!$productcatsId) {
                return null;
            }
        }
        if ($attachments !== null) {
            /** @var \FME\Productattachments\Model\Productattachments $productattachments */
            $productattachments = $this->_productattachmentsFactory->create();
            $productattachmentsId = $productattachments->setStoreId($this->_storeManager->getStore()->getId())->load($attachments);
            //$productattachments->checkIdentifier($attachments, $this->_storeManager->getStore()->getId());
            if (!$productattachmentsId) {
                return null;
            }
        }
        $request->setModuleName('productattachments')
                ->setControllerName('index')
                ->setActionName('attachmentslist');
        if ($cats && $attachments == null) {
            $request->setActionName('view')
                    ->setParam('u', $cats);
        }
        if ($attachments) {
            $request->setActionName('attachments')
                    ->setParam('v', $attachments);
        }
        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
}
