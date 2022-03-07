<?php


namespace Pr\CmsSearch\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{

    protected $request;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->request = $request;
        parent::__construct($context, $data);
    }


    public function getSearchPages(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $queryText = $this->request->getParam('q');
        $pagesCollection=$objectManager->create('Magento\Cms\Model\Page')->getCollection()
                            ->addFieldToFilter('is_active',1)
                            ->addFieldToFilter(
                                            array('title','content'),
                                                array(
                                                    array('like'=>'%'.$queryText.'%'), 
                                                    array('like'=>'%'.$queryText.'%')
                                                )
                                            );
        
        return $pagesCollection;
    }
}
