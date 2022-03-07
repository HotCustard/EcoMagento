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
    'mage/template',
    'Magento_Ui/js/modal/alert',
    "Magento_Ui/js/modal/modal",
    'mage/validation',
    "jquery/file-uploader"
    ], function ($,mageTemplate,alert,modal) {
        'use strict';
        var customerId;
        $.widget(
            'mage.WkAddquote', {
                options: {

                },
                _create: function () {
                    var self = this;
                    $("#wk_quotesystem_products").css('display','none');
                    $("body").on('click', "._clickable",function () {
                        if (!customerId) {
                            customerId = parseInt($(this).find(".col-entity_id").html());
                        }
                        $("#sales_order_create_customer_grid").css('display','none');
                        $("#wk_quotesystem_products").css('display','block');
                    });
                    var options = {
                        type: 'slide',
                        responsive: true,
                        innerScroll: true,
                        modalClass: 'popup', 
                        validation:{},
                        buttons: [
                            {
                                text: $.mage.__('Submit'),
                                class: 'button',
                                click: function () {
                                    var form = $('#wk_quotesystem_popup');
                                    if ($(form).validation() && $(form).validation('isValid')) {
                                        $.ajax(
                                            {
                                                url: form.attr('action'),
                                                data: form.serialize(),
                                                type: 'post',
                                                // dataType: 'json',
                                                /**
                                                 * @inheritdoc 
                                                 */
                                                beforeSend: function () {
                                                    $('body').trigger('processStart');
                                                },
                                                /**
                                                 * @inheritdoc 
                                                 */
                                                success: function (res) {
                                                    var eventData, parameters;
                                                    $('body').trigger('processStop');
                                                    $(form)[0].reset();
                                                    $(".wk_quote_configurable_options").empty();
                                                    $("#wk_quotesystem_popup").modal('closeModal');
                                                    window.location.href = self.options.quoteUrl
                                                }
                                            }
                                        );
                                    }
                                }
                            },
                            {
                                text: $.mage.__('Reset'),
                                class: 'reset',
                                click: function () {
                                    var form = $('#wk_quotesystem_popup');
                                    $(form)[0].reset();
                                    $(".wk-uploaded-file").remove();
                                }
                            }
                        ]
                    };
                    var popup = modal(options, $('#wk_quotesystem_popup'));
                    $(".wk_quotesystem_column_action").click (
                        function(e) {
                            e.preventDefault();
                            let id = parseInt($(this).siblings(".col-entity_id").html());
                            $(".wk_quote_configurable_options").empty();
                            $.ajax(
                                {
                                    url: self.options.ajaxurl,
                                    data: {id},
                                    type: "get",
                                    success: function(res) {
                                        var productType = res;
                                        $.ajax(
                                            {
                                                url: self.options.customOptionsUrl,
                                                data: {id},
                                                type: "post",
                                                success: function (res) {
                                                    $("#catalog_product_composite_configure_fields_configurable").empty();
                                                    $(".wk_quote_configurable_options").prepend(res);
                                                    $("#product_composite_configure_fields_qty").remove();
                                                    $("#catalog_product_composite_configure_fields_configurable").remove();
                                                    if (productType == 'configurable') {
                                                        var formkey = self.options.formkey;
                                                        $.ajax(
                                                            {
                                                                url: self.options.optionsurl,
                                                                data: {id},
                                                                type: "post",
                                                                success: function (res) {
                                                                    $("#catalog_product_composite_configure_fields_configurable").empty();
                                                                    $(".wk_quote_configurable_options").prepend(res);
                                                                }
                                                            }
                                                        )
                                                    }
                                                }
                                            }
                                        )
                                        if(res == 'bundle') {
                                            var formkey = self.options.formkey;
                                            alert(
                                                {
                                                    content: $.mage.__("Bundle Product can't be Quoted from Admin end.")
                                                }
                                            );
                                            // $.ajax(
                                            //     {
                                            //         url: self.options.bundleOptionsUrl,
                                            //         data: {id},
                                            //         type: "post",
                                            //         success: function (res) {
                                            //             $("#catalog_product_composite_configure_fields_configurable").empty();
                                            //             $(".entry-edit").prepend(res);
                                            //         }
                                            //     }
                                            // )
                                        }
                                        $("#wk_quotesystem_popup").find('input[name="product"]').val(id);
                                        $("#wk_quotesystem_popup").find('input[name="customer_id"]').val(customerId);
                                        $(".wk-uploaded-file").remove();
                                        $("#wk_quotesystem_popup").modal("openModal");
                                    }
                                }
                            ) 
                        }
                    );
                    $('#wk-file-field').fileupload(
                        {
                            dataType: 'json',
                            sequentialUploads: true,
                            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc|zip)$/i,
                            add: function (e, data) {
                                var progressTmpl = mageTemplate('#wk-file-field-uploader-template'),
                                tmpl;
                                var thisObj = $(this);

                                $.each(
                                    data.files, function (index, file) {
                                        data.fileId = Math.random().toString(33).substr(2, 18);

                                        tmpl = progressTmpl(
                                            {
                                                data: {
                                                    id: data.fileId
                                                }
                                            }
                                        );
                                        if ($('.wk-uploaded-file').length) {
                                            var indexKey = 1;
                                            $('.wk-uploaded-file').each(
                                                function () {
                                                    if (indexKey == 1) {
                                                        $(this).before(tmpl);
                                                    }
                                                    indexKey++;
                                                }
                                            );
                                        } else {
                                            $(tmpl).appendTo('.wk-file-field-container');
                                        }
                                    }
                                );
                                thisObj.fileupload('process', data).done(
                                    function () {
                                        data.submit();
                                    }
                                );
                            },
                            done: function (e, data) {
                                if (data.result && !data.result.error) {
                                    var progressTmpl = mageTemplate('#wk-file-field-template'),
                                    tmpl;
                                    tmpl = progressTmpl(
                                        {
                                            data: {
                                                name: data.result.name,
                                                file: data.result.file,
                                                extension: data.result.type
                                            }
                                        }
                                    );

                                    $(tmpl).appendTo('.wk-file-field-container');
                                } else {
                                    $('#' + data.fileId)
                                    .delay(2000)
                                    .hide('highlight');
                                    alert(
                                        {
                                            content: $.mage.__('We don\'t recognize or support this file extension type.')
                                        }
                                    );
                                }
                                $('#' + data.fileId).remove();
                            },
                            progress: function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10);
                                var progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';
                                $(progressSelector).css('width', progress + '%');
                            },
                            fail: function (e, data) {
                                var progressSelector = '#' + data.fileId;
                                $(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                                .delay(2000)
                                .hide('highlight')
                                .remove();
                            }
                        }
                    );
                    $('#wk-file-field').fileupload(
                        'option', {
                            process: [{
                                action: 'load',
                                fileTypes: /^image\/(gif|jpe?g|png|pdf|doc|zip)$/
                            }, {
                                action: 'resize',
                                maxWidth: self.options.maxWidth ,
                                maxHeight: self.options.maxHeight
                            }, {
                                action: 'save'
                            }]
                        }
                    );

                    $('.wk-file-field-container').on(
                        "click", ".wk-uploaded-file-del", function () {
                            var thisObj = $(this);
                            var fileName = $(this).parent('.wk-uploaded-file').find('.wk-uploaded-file-value').val();
                            $.ajax(
                                {
                                    url: self.options.fileDeleteUrl,
                                    data: { file_name : fileName },
                                    type: "post",
                                    datatype: "json",
                                    showLoader: true,
                                    success: function (data) {
                                        thisObj.parent('.wk-uploaded-file').remove();
                                    },
                                    error: function (data) {
                                        thisObj.parent('.wk-uploaded-file').remove();
                                    }
                                }
                            );
                        }
                    );
                }
            }
        )
        return $.mage.WkAddquote;
    }
);
