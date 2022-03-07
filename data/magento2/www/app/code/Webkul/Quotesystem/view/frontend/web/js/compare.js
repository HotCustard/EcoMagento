/**
 * Webkul Quotesystem
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define(
    [
    "jquery",
    ], function ($) {
        'use strict';
        $.widget(
            'mage.wkCompareList', {
                _create: function () {
                    var self = this;
                    var showCart = self.options.showCart;
                    if (!showCart) {
                        $('td.cell.product.info').each(
                            function () {
                                if ($(this).find('.quote_button').length > 0) {
                                    $(this).find('.action.tocart.primary').remove();
                                }
                            }
                        );
                    }
                },
            }
        );
        return $.mage.wkCompareList;
    }
);