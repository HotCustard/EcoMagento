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

class SetRefererUrl implements ObserverInterface
{
    protected $redirect;

    protected $checkoutSession;
   
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
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
        $refererUrl = $this->redirect->getRefererUrl();
        $this->checkoutSession->setReferUrl($refererUrl);
    }
}
