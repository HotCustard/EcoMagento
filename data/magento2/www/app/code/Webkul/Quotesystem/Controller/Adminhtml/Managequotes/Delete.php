<?php
/**
 * Quote Delete controller Admin panel.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Controller\Adminhtml\Managequotes;

use Magento\Backend\App\Action;
use Webkul\Quotesystem;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Webkul\Quotesystem\Helper\Data
     */
    protected $_quoteHelper;
    /**
     * @var Webkul\Quotesystem\Api\QuoteRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @param Action\Context                           $context
     * @param Quotesystem\Helper\Data                  $quoteHelper
     * @param Quotesystem\Api\QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Action\Context $context,
        Quotesystem\Helper\Data $quoteHelper,
        Quotesystem\Api\QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->_quoteHelper = $quoteHelper;
        $this->_quoteRepository = $quoteRepository;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Webkul_Quotesystem::quotes'
        );
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
*/
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data && array_key_exists('entity_id', $data)) {
            $quoteId = $data['entity_id'];
            if ($quoteId) {
                try {
                    $this->_quoteRepository->deleteById($quoteId);
                    $this->messageManager->addSuccess(
                        __('Quote is successfully deleted.')
                    );
                    return $resultRedirect->setPath('*/*/');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException(
                        $e,
                        __('Something went wrong while Deleting the data.')
                    );
                }
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
