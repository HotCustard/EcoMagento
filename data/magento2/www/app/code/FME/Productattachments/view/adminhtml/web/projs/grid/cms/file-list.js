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
define([
    './column',
    'jquery',
    'ko',
    'mage/template',
    'text!FME_Productattachments/template/preview.html',
    'Magento_Ui/js/modal/alert'
], function (Column, $, ko, mageTemplate, attachmentPreviewTemplate, alert) {
    'use strict';


    return Column.extend({
        defaults: {
            bodyTmpl: 'FME_Productattachments/cms/file-list.html',
            fieldClass: {
                'data-grid-attachment-cell': true
            }
        },
        getAlt: function (row) {
            return row[this.index + '_alt']
        },
        getAttachmentList: function (row) {
            
            return row[this.index + '_list'];
        },

        getCmsId: function (row) {
            return row['page_id'];
        },

        getAttachmentListId: function (row) {
            return 'att-list-' + this.getCmsId(row);
        },

        isPreviewAvailable: function () {
            return this.has_preview || false;
        },
       
        getFieldHandler: function (row) {
            if (this.isPreviewAvailable()) {
                return this.preview.bind(this, row);
            }
        },

        getMaxAttachmentItems: function () {

            return 4;
        }

    });
});
