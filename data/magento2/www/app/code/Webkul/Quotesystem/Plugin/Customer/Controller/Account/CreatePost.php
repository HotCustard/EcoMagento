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

namespace Webkul\Quotesystem\Plugin\Customer\Controller\Account;

use Magento\Customer\Model\Session;

class CreatePost
{
    protected $session;

    protected $checkoutsession;
    
    /**
     * @param Session                         $customerSession
     * @param \Magento\Checkout\Model\Session $checkSession
     */
    public function __construct(
        Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutsession
    ) {
        $this->session = $customerSession;
        $this->checkoutSession = $checkoutsession;
    }

    /**
     * Change redirect after login
     *
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Magento\Framework\Controller\Result\Redirect   $result
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        $result
    ) {
        $customerId = $this->session->getCustomer()->getId();
        $refererUrl = $this->checkoutSession->getReferUrl();
        if ($refererUrl && $customerId) {
            $result->setPath($refererUrl);
        }
        return $result;
    }
}
