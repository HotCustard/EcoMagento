/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
	'mage/url',
    'jquery/ui',
	'mage/cookies',
    'Magento_Catalog/js/catalog-add-to-cart'
], function($, $t, url) {
    "use strict";

    $.widget('scommerce.catalogAddToCart', $.mage.catalogAddToCart, {
        gaAddToCart: function($){
			var list = $.mage.cookies.get('productlist');
			$.ajax({
                url: url.build('gtmpro/index/addtocart'),
                type: 'get',
                dataType: 'json',
                success: function(product) {
                    if (product != undefined) {
						if (list == undefined){
							list='Category - '+ product.category
						}
						dataLayer.push({
							'event': 'addToCart',
							'ecommerce': {
								'currencyCode': product.currency,
								'add': {                                // 'add' actionFieldObject measures.
									'products': [{                        //  adding a product to a shopping cart.
										'name': product.name,
										'id': product.id,
										'price': product.price,
										'brand': product.brand,
										'category': product.category,
										'quantity': product.qty,
										'list': list
									}]
								}
							}
						});
					}
					$.ajax({
						url: url.build('gtmpro/index/unsaddtocart'),
						type: 'get',
						success: function(response) {
						}
					});					
                }
            });
			
        },
        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                    self.gaAddToCart($);
                }

            });
        }
    });

    return $.scommerce.catalogAddToCart;
});