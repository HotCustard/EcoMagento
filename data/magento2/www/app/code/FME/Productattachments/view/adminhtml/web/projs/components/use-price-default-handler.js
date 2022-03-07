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
            linksPurchasedSeparately: '0',
            listens: {
                linksPurchasedSeparately: 'changeVisibleStatus'
            }
        },

        /**
         * Change visibility of checkbox
         */
        changeVisibleStatus: function () {
            if (this.linksPurchasedSeparately === '1') {
                this.visible(true);
            } else {
                this.visible(false);
            }
        }
    });
});
