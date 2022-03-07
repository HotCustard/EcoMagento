/**
 * Webkul Quotesystem
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
    "jquery",
    "Magento_Ui/js/modal/modal",
    'mage/validation',
    "jquery/file-uploader"
    ], function ($,modal) {
        'use strict';
        var customerId;
        $.widget(
            'mage.WkProductoption', {
                options: {

                },
                _create: function () {
                    console.log("chala kya")
                    console.log(self.options.data);
                }
            }  
        )
        return $.mage.WkProductoption;
    }
);