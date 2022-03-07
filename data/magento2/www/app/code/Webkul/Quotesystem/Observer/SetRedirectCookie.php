<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Quotesystem\Helper\Data;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class SetRedirectCookie implements ObserverInterface
{
    protected $redirect;

    protected $accountRedirect;

    protected $checkoutSession;
    
    /**
     * @param AccountRedirect                                   $accountRedirect
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     */
    public function __construct(
        AccountRedirect $accountRedirect,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->accountRedirect = $accountRedirect;
        $this->redirect = $redirect;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * quote Item qty Set after
     *
     * @param \Magento\Framework\Event\Observer $observer
     */

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $refererUrl = $this->checkoutSession->getReferUrl();
        $this->accountRedirect->setRedirectCookie($refererUrl);
    }
}
