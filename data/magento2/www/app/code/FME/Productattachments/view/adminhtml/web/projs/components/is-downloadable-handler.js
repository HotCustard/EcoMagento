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
    'Magento_Ui/js/form/element/single-checkbox'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                disabled: 'changeVisibility'
            },
            modules: {
                samplesFieldset: '${ $.samplesFieldset }',
                linksFieldset: '${ $.linksFieldset}'
            }
        },

        /**
         * Change visibility for samplesFieldset & linksFieldset based on current statuses of checkbox.
         */
        changeVisibility: function () {
            if (this.samplesFieldset() && this.linksFieldset()) {
                if (this.checked() && !this.disabled()) {
                    this.samplesFieldset().visible(true);
                    this.linksFieldset().visible(true);
                } else {
                    this.samplesFieldset().visible(false);
                    this.linksFieldset().visible(false);
                }
            }
        },

        /**
         * Handle checked state changes for checkbox / radio button.
         *
         * @param {Boolean} newChecked
         */
        onCheckedChanged: function (newChecked) {
            this.changeVisibility();
            this._super(newChecked);
        }
    });
});
