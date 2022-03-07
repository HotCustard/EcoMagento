<?php
/**
 * Render Receiver name test
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Test\Unit\Block\Adminhtml\Managequotes\Grid;

class RendererReceiverNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $row;
    /**
     * @var  \Webkul\Quotesystem\Block\Adminhtml\Managequotes\Grid\RendererReceiverNameTest
     */
    protected $itemBlock;

    public function configure($recieverId)
    {
        $this->row = $this->getMock('Webkul\Quotesystem\Model\Quotes', ['getReceiver'], [], '', false);
        $this->row
            ->expects($this->atLeastOnce())
            ->method('getReceiver')
            ->will($this->returnValue($recieverId));
        $helper = $this->getMock(
            'Webkul\Quotesystem\Helper\Data',
            ['getCustomerData'],
            [],
            '',
            false
        );
        
        $escaper = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false);

        $context = $this->getMock('Magento\Backend\Block\Context', ['getEscaper'], [], '', false);
        $context
            ->expects($this->once())
            ->method('getEscaper')
            ->will($this->returnValue($escaper));

        if ($recieverId != 0) {
            $customerdata = $this->getMock(
                '\Magento\Customer\Model\Customer',
                [],
                [],
                '',
                false
            );
            $customerdata->expects($this->any())->method('load')
                ->with($recieverId)
                ->will($this->returnSelf());

            $helper
                ->expects($this->once())
                ->method('getCustomerData')
                ->will($this->returnValue($customerdata));
        }
        $this->itemBlock = new \Webkul\Quotesystem\Block\Adminhtml\Managequotes\Grid\RendererReceiverName(
            $context,
            $helper
        );
    }
    /**
     * @dataProvider optionHtmlProvider
     */
    public function testRender($recieverId, $expectedHtml)
    {
        $this->configure($recieverId);
        $realHtml = '<xhtml>' . $this->itemBlock->render($this->row) . '</xhtml>';
        $this->assertXmlStringEqualsXmlString($expectedHtml, $realHtml);
    }

    public function optionHtmlProvider()
    {
        return [
            [
                0,
                <<<HTML
                        <xhtml>
                            <span>Admin</span>
                        </xhtml>
HTML
            ],
            [
                'test_reciever',
                <<<HTML
                        <xhtml>
                            <span>Admin</span>
                        </xhtml>
HTML
            ],
            [
                3000,
                <<<HTML
                        <xhtml>
                            <span></span>
                        </xhtml>
HTML
            ]
        ];
    }
}
