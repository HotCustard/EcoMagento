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
namespace FME\Productattachments\Controller\Adminhtml\Productattachments;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Api\Data\BookmarkInterfaceFactory;
use Magento\Ui\Controller\Adminhtml\AbstractAction;

class Filters extends \Magento\Backend\App\Action
{
    const CURRENT_IDENTIFIER = 'current';

    const ACTIVE_IDENTIFIER = 'activeIndex';

    const VIEWS_IDENTIFIER = 'views';

    /**
     * @var BookmarkRepositoryInterface
     */
    protected $bookmarkRepository;

    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var BookmarkInterfaceFactory
     */
    protected $bookmarkFactory;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;
    protected $jsonEncoder;
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Context $context,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        BookmarkInterfaceFactory $bookmarkFactory,
        UserContextInterface $userContext,
        DecoderInterface $jsonDecoder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkManagement = $bookmarkManagement;
        $this->bookmarkFactory = $bookmarkFactory;
        $this->userContext = $userContext;
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productFactory = $productFactory;
    }

    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $bookmark = $this->bookmarkFactory->create();
        $params = $this->getRequest()->getParams();
            //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            //$product = $objectManager->get('Magento\Catalog\Model\Product')->load($params['id']);
            $product=$this->_productFactory->create()->load($value);
            $jsonData = "{\"current\":{\"filters\":{\"applied\":{\"placeholder\":true,\"product_names\":\"test\"}},\"columns\":{\"productattachments_id\":{\"visible\":true,\"sorting\":\"asc\"},\"title\":{\"visible\":true,\"sorting\":false},\"file_size\":{\"visible\":true,\"sorting\":false},\"store_id\":{\"visible\":true,\"sorting\":false},\"ids\":{\"visible\":true,\"sorting\":false},\"status\":{\"visible\":true,\"sorting\":false},\"actions\":{\"visible\":true,\"sorting\":false},\"products\":{\"visible\":true,\"sorting\":false},\"cms\":{\"visible\":true,\"sorting\":false},\"category\":{\"visible\":true,\"sorting\":false},\"product_names\":{\"visible\":true,\"sorting\":false}},\"displayMode\":\"grid\",\"paging\":{\"options\":{\"20\":{\"value\":20,\"label\":20},\"30\":{\"value\":30,\"label\":30},\"50\":{\"value\":50,\"label\":50},\"100\":{\"value\":100,\"label\":100},\"200\":{\"value\":200,\"label\":200}},\"value\":20},\"positions\":{\"ids\":0,\"productattachments_id\":1,\"title\":2,\"file_size\":3,\"store_id\":4,\"product_names\":5,\"cms\":6,\"category\":7,\"status\":8,\"actions\":9},\"search\":{\"value\":\"\"}}}";
            $data = $this->jsonDecoder->decode($jsonData);
            $data['current']['filters']['applied']['product_names'] = $product['name'];
            $json2 = $this->jsonEncoder->encode($data);
        $action = key($data);
        switch ($action) {
            case self::ACTIVE_IDENTIFIER:
                $this->updateCurrentBookmark($data[$action]);
                break;

            case self::CURRENT_IDENTIFIER:
                $this->updateBookmark(
                    $bookmark,
                    $action,
                    $bookmark->getTitle(),
                    $json2
                );

                break;

            case self::VIEWS_IDENTIFIER:
                foreach ($data[$action] as $identifier => $data) {
                    $this->updateBookmark(
                        $bookmark,
                        $identifier,
                        isset($data['label']) ? $data['label'] : '',
                        $json2
                    );
                    $this->updateCurrentBookmark($identifier);
                }

                break;

            default:
                throw new \LogicException(__('Unsupported bookmark action.'));
        }
            return $resultRedirect->setPath('productattachmentsadmin/productattachments/index');
    }

    protected function updateBookmark(BookmarkInterface $bookmark, $identifier, $title, $config)
    {
        $updateBookmark = $this->checkBookmark($identifier);
        if ($updateBookmark !== false) {
            $bookmark = $updateBookmark;
        }
            $grid = 'productattachments_listing';
        $bookmark->setUserId($this->userContext->getUserId())
            ->setNamespace($grid)
            ->setIdentifier($identifier)
            ->setTitle($title)
            ->setConfig($config);
        $this->bookmarkRepository->save($bookmark);
    }

    /**
     * Update current bookmark
     *
     * @param string $identifier
     * @return void
     */
    protected function updateCurrentBookmark($identifier)
    {
        $grid = 'productattachments_listing';   
        $bookmarks = $this->bookmarkManagement->loadByNamespace($grid);
        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->getIdentifier() == $identifier) {
                $bookmark->setCurrent(true);
            } else {
                $bookmark->setCurrent(false);
            }
            $this->bookmarkRepository->save($bookmark);
        }
    }

    /**
     * Check bookmark by identifier
     *
     * @param string $identifier
     * @return bool|BookmarkInterface
     */
    protected function checkBookmark($identifier)
    {
        $result = false;
        $grid = 'productattachments_listing';
        $updateBookmark = $this->bookmarkManagement->getByIdentifierNamespace(
            $identifier,
            $grid
        );

        if ($updateBookmark) {
            $result = $updateBookmark;
        }
        return $result;
    }
}
