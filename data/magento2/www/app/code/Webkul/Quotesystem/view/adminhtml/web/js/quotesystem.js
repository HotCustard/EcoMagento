/**
 * Quotes_edit.xml
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

require(
    [
    'jquery'
    ], function ($) {
        'use strict';
        $(document).ajaxStop(
            function () {
                if (!parseInt($('select[name="product[quote_status]"] option:selected').val())) {
                    $('input[name="product[min_quote_qty]"]').prop('disabled', true);
                }
                $('select[name="product[quote_status]"]').change(
                    function () {
                        if (!parseInt($('select[name="product[quote_status]"] option:selected').val())) {
                            $('input[name="product[min_quote_qty]"]').prop('disabled', true);
                        } else {
                            $('input[name="product[min_quote_qty]"]').prop('disabled', false);
                        }
                    }
                )
            }
        );
    }
);