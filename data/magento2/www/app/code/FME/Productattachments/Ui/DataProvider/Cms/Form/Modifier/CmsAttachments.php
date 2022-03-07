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
namespace FME\Productattachments\Ui\DataProvider\Cms\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use FME\Productattachments\Ui\DataProvider\Cms\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form;
use Magento\Downloadable\Model\Source\TypeUpload;
use FME\Productattachments\Model\Config\Source\cgroups;

class CmsAttachments extends AbstractModifier
{
 
    // Components indexes
    const CUSTOM_FIELDSET_INDEX = 'cms_fieldset';
    const CUSTOM_FIELDSET_CONTENT = 'cms_fieldset_content';
    const CONTAINER_HEADER_NAME = 'dynamic_rows';
 
    // Fields names
   
 
    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;
    protected $typeUpload;
    protected $cgroups;
    protected $request;
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
 
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    protected $_productattachments;
    /**
     * @var array
     */
    protected $meta = [];
 
    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        TypeUpload $typeUpload,
        cgroups $cgroups,
        \Magento\Framework\App\RequestInterface $request,
        \FME\Productattachments\Model\Productattachments $productattachments,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->typeUpload = $typeUpload;
        $this->cgroups = $cgroups;
        $this->request = $request;
        $this->_productattachments = $productattachments;
        $this->urlBuilder = $urlBuilder;
    }
 
    /**
     * Data modifier, does nothing in our example.
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $id   = $this->request->getParam('page_id');
        if ($id!='') {
            $attachment = $this->_productattachments->getRelatedCms($id);
            foreach ($data as &$dat) {
                foreach ($attachment as $key => $value) {
                    $dat['page']['attachments']['dynamic_rows'][$key]['attachment_id'] = $value['productattachments_id'];
                    $dat['page']['attachments']['dynamic_rows'][$key]['record_id'] = $key;
                    $dat['page']['attachments']['dynamic_rows'][$key]['title'] = $value['title'];
                    $dat['page']['attachments']['dynamic_rows'][$key]['status'] = $value['status'];
                    $dat['page']['attachments']['dynamic_rows'][$key]['title'] = $value['title'];

                    $cgroup = $value['customer_group_id'];
           
                    if ($cgroup =='') {
                        $temp = '';
                    } else {
                          $grouparr = explode(',', $cgroup);
            
                        foreach ($grouparr as $kk) {
                            $cgrouparr[] = (int)$kk;
                        }
          
                          $dat['page']['attachments']['dynamic_rows'][$key]['customer_group'] = $cgrouparr;

                          unset($cgrouparr);
                    }
                    if ($value['filename'] == '') {
                        $dat['page']['attachments']['dynamic_rows'][$key]['type'] = 'url';
                         $dat['page']['attachments']['dynamic_rows'][$key]['link_url'] = $value['link_url'];
                    } else {
                        $dat['page']['attachments']['dynamic_rows'][$key]['type'] = 'file';
                        $dat['page']['attachments']['dynamic_rows'][$key]['filename'][0]['file'] = $value['filename'];
                        $dat['page']['attachments']['dynamic_rows'][$key]['filename'][0]['name'] = $value['filename'];
                        $dat['page']['attachments']['dynamic_rows'][$key]['filename'][0]['size'] = ((float) $value['file_size']) * 1024;
                        $dat['page']['attachments']['dynamic_rows'][$key]['filename'][0]['url'] = $value['download_link'];
                        $dat['page']['attachments']['dynamic_rows'][$key]['filename'][0]['status'] = 'old';
                    }
                }
            }
        }
        return $data;
    }
 
    /**
     * Meta-data modifier: adds ours fieldset
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addCustomFieldset();
 
        return $this->meta;
    }
 
    /**
     * Merge existing meta-data with our meta-data (do not overwrite it!)
     *
     * @return void
     */
    protected function addCustomFieldset()
    {
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::CUSTOM_FIELDSET_INDEX => $this->getFieldsetConfig(),
            ]
        );
    }
 
    /**
     * Declare ours fieldset config
     *
     * @return array
     */
    protected function getFieldsetConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('CMS Attachments'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => static::DATA_SCOPE_PRODUCT, // save data in the product data
                        'provider' => static::DATA_SCOPE_PRODUCT . '_data_source',
                        'ns' => static::FORM_NAME,
                        'collapsible' => true,
                        'sortOrder' => 125,
                        'opened' => false,
                    ],
                ],
            ],
            'children' => [
                static::CONTAINER_HEADER_NAME => $this->getDynamicRows(),
            ],
        ];
    }
 

    protected function getDynamicRows()
    {
        $dynamicRows['arguments']['data']['config'] = [
            'addButtonLabel' => __('Add Attachment'),
            'componentType' => DynamicRows::NAME,
            'itemTemplate' => 'record',
            'renderDefaultRecord' => false,
            'columnsHeader' => true,
            'additionalClasses' => 'admin__field-wide',
            'dataScope' => 'attachments',
            'deleteProperty' => 'is_delete',
            'deleteValue' => '1',
        ];

        return $this->arrayManager->set('children/record', $dynamicRows, $this->getRecord());
    }

    /**
     * @return array
     */
    protected function getRecord()
    {
        $record['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'isTemplate' => true,
            'is_collection' => true,
            'component' => 'Magento_Ui/js/dynamic-rows/record',
            'dataScope' => '',
        ];
        $recordPosition['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'sort_order',
            'visible' => false,
        ];
        $recordActionDelete['arguments']['data']['config'] = [
            'label' => null,
            'componentType' => 'actionDelete',
            'fit' => true,
        ];

        return $this->arrayManager->set(
            'children',
            $record,
            [
                'container_link_title' => $this->getTitleColumn(),
                'container_file' => $this->getFileColumn(),
                'customer_group' => $this->getCustomerGroup(),
                'status_column' =>$this->getStatusColumn(),
                'position' => $recordPosition,
                'action_delete' => $recordActionDelete,
            ]
        );
    }

    /**
     * @return array
     */
    protected function getTitleColumn()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Title'),
            'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'title',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }

    protected function getCustomerGroup()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Customer Group'),
            'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\MultiSelect::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'customer_group',
            'options' => $this->cgroups->toOptionArray(),
            
        ];

        return $this->arrayManager->set('children/customer_group', $titleContainer, $titleField);
    }


    protected function getStatusColumn()
    {
        $titleContainer['arguments']['data']['config'] = [
        'componentType' => Container::NAME,
        'formElement' => Container::NAME,
        'component' => 'Magento_Ui/js/form/components/group',
        'label' => __('Enable'),
        'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
        'formElement' => Form\Element\Select::NAME,
        'componentType' => Form\Field::NAME,
        'dataType' => Form\Element\DataType\Text::NAME,
        'dataScope' => 'status',
        'options' => $this->_getOptions(),
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }
    /**
     * @return array
     */
    

    /**
     * @return array
     */
    protected function getFileColumn()
    {
        $fileContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('File'),
            'dataScope' => '',
        ];
        $fileTypeField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'component' => 'Magento_Downloadable/js/components/upload-type-handler',
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'type',
            'options' => $this->typeUpload->toOptionArray(),
            'typeFile' => 'links_file',
            'typeUrl' => 'link_url',
        ];
        $fileLinkUrl['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'link_url',
            'placeholder' => 'URL',
            'validation' => [
                'required-entry' => true,
                'validate-url' => true,
            ],
        ];
        $fileUploader['arguments']['data']['config'] = [
            'formElement' => 'fileUploader',
            'componentType' => 'fileUploader',
            'component' => 'FME_Productattachments/projs/components/file-uploader',
            'elementTmpl' => 'FME_Productattachments/components/file-uploader',
            'fileInputName' => 'filename',
            'uploaderConfig' => [
                'url' => 'productattachmentsadmin/productattachments_image/upload',
            ],
            'dataScope' => 'filename',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set(
            'children',
            $fileContainer,
            [
                'type' => $fileTypeField,
                'link_url' => $fileLinkUrl,
                'links_file' => $fileUploader
            ]
        );
    }

    protected function _getOptions()
    {
        $options = [
            1 => [
                'label' => __('Yes'),
                'value' => 1
            ],
            2 => [
                'label' => __('No'),
                'value' => 0
            ],
        ];
 
        return $options;
    }
}
