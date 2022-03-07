<?php
/**
 * Created by PhpStorm.
 * User: yogesh
 * Date: 20/2/19
 * Time: 7:44 PM
 */
namespace Mage2\Inquiry\Block\Adminhtml\Inquiry\Edit;

use Magento\Backend\Block\Widget\Context;
use Mage2\Inquiry\Api\InquiryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var InquiryRepositoryInterface
     */
    protected $inquiryRepository;

    /**
     * @param Context $context
     * @param InquiryRepositoryInterface $inquiryRepository
     */
    public function __construct(
        Context $context,
        InquiryRepositoryInterface $inquiryRepository
    ) {
        $this->context = $context;
        $this->inquiryRepository = $inquiryRepository;
    }

    /**
     * @return int|null
     * @throws LocalizedException
     */
    public function getInquiryId()
    {
        try {
            return $this->inquiryRepository->getById(
                $this->context->getRequest()->getParam('inquiry_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
