require(['jquery'], function () {

jQuery(document).ready(function($) {
	
	
	
	
	
	(function($) {
    $.fn.extend({
        easyResponsiveTabs: function(options) {
            var defaults = {
                type: 'default',
                width: 'auto',
                fit: true,
                closed: false,
                activate: function() {}
            }
            var options = $.extend(defaults, options);
            var opt = options,
                jtype = opt.type,
                jfit = opt.fit,
                jwidth = opt.width,
                vtabs = 'vertical',
                accord = 'accordion';
            $(this).bind('tabactivate', function(e, currentTab) {
                if (typeof options.activate === 'function') {
                    options.activate.call(currentTab, e)
                }
            });
            this.each(function() {
                var $respTabs = $(this);
                var $respTabsList = $respTabs.find('ul.resp-tabs-list');
                $respTabs.find('ul.resp-tabs-list li').addClass('resp-tab-item');
                $respTabs.css({
                    'display': 'block',
                    'width': jwidth
                });
                $respTabs.find('.resp-tabs-container > div').addClass('resp-tab-content');
                jtab_options();

                function jtab_options() {
                    if (jtype == vtabs) {
                        $respTabs.addClass('resp-vtabs');
                    }
                    if (jfit == true) {
                        $respTabs.css({
                            width: '100%',
                            margin: '0px'
                        });
                    }
                    if (jtype == accord) {
                        $respTabs.addClass('resp-easy-accordion');
                        $respTabs.find('.resp-tabs-list').css('display', 'none');
                    }
                }
                var $tabItemh2;
                $respTabs.find('.resp-tab-content').before("<h2 class='resp-accordion' role='tab'><span class='resp-arrow'></span></h2>");
                var itemCount = 0;
                $respTabs.find('.resp-accordion').each(function() {
                    $tabItemh2 = $(this);
                    var innertext = $respTabs.find('.resp-tab-item:eq(' + itemCount + ')').html();
                    $respTabs.find('.resp-accordion:eq(' + itemCount + ')').append(innertext);
                    $tabItemh2.attr('aria-controls', 'tab_item-' + (itemCount));
                    itemCount++;
                });
                var count = 0,
                    $tabContent;
                $respTabs.find('.resp-tab-item').each(function() {
                    $tabItem = $(this);
                    $tabItem.attr('aria-controls', 'tab_item-' + (count));
                    $tabItem.attr('role', 'tab');
                    if (options.closed !== true && !(options.closed === 'accordion' && !$respTabsList.is(':visible')) && !(options.closed === 'tabs' && $respTabsList.is(':visible'))) {
                        $respTabs.find('.resp-tab-item').first().addClass('resp-tab-active');
                        $respTabs.find('.resp-accordion').first().addClass('resp-tab-active');
                        $respTabs.find('.resp-tab-content').first().addClass('resp-tab-content-active').attr('style', 'display:block');
                    }
                    var tabcount = 0;
                    $respTabs.find('.resp-tab-content').each(function() {
                        $tabContent = $(this);
                        $tabContent.attr('aria-labelledby', 'tab_item-' + (tabcount));
                        tabcount++;
                    });
                    count++;
                });
                $respTabs.find("[role=tab]").each(function() {
                    var $currentTab = $(this);
                    $currentTab.click(function() {
                        var $tabAria = $currentTab.attr('aria-controls');
                        if ($currentTab.hasClass('resp-accordion') && $currentTab.hasClass('resp-tab-active')) {
                            $respTabs.find('.resp-tab-content-active').slideUp('', function() {
                                $(this).addClass('resp-accordion-closed');
                            });
                            $currentTab.removeClass('resp-tab-active');
                            return false;
                        }
                        if (!$currentTab.hasClass('resp-tab-active') && $currentTab.hasClass('resp-accordion')) {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').slideUp().removeClass('resp-tab-content-active resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').slideDown().addClass('resp-tab-content-active');
                        } else {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').removeAttr('style').removeClass('resp-tab-content-active').removeClass('resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').addClass('resp-tab-content-active').attr('style', 'display:block');
                        }
                        $currentTab.trigger('tabactivate', $currentTab);
                    });
                    $(window).resize(function() {
                        $respTabs.find('.resp-accordion-closed').removeAttr('style');
                    });
                });
            });
        }
    });
})(jQuery);
	
	
	
	
	
	
	
	
   
    $('.horizontalTab').easyResponsiveTabs();
   

jQuery('table').wrap('<div class="table-responsive"></div>');
    
    
});
});





// require(['jquery', 'cpowlcarousel'], function ($) {
//     $(document).ready(function () {
//         $(".hb-slider").owlCarousel({
//             items: 1,
// 			loop:true,
//             itemsDesktop: [1080, 1],
//             itemsDesktopSmall: [860, 1],
//             itemsTablet: [768, 1],
//             itemsTabletSmall: [639, 1],
//             itemsMobile: [360, 1],
//             pagination: true,
//             navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'>"],
//             navigation: false,
// 			autoPlay : 5000,

//         });

//         $(".hpb-slider").owlCarousel({
//             items: 3,
//             itemsDesktop: [1080, 3],
//             itemsDesktopSmall: [860, 3],
//             itemsTablet: [768, 2],
//             itemsTabletSmall: [639, 2],
//             itemsMobile: [479, 1],
//             pagination: false,
//             navigationText: ['<div class="lft-btn"><i class="fa fa-angle-left"></i></div>', '<div class="rgt-btn"><i class="fa fa-angle-right"></div>'],
//             navigation: true,
//         });
//         $(".htb-slider").owlCarousel({
//             items: 1,
//             itemsDesktop: [1080, 1],
//             itemsDesktopSmall: [860, 1],
//             itemsTablet: [768, 1],
//             itemsTabletSmall: [639, 1],
//             itemsMobile: [479, 1],
//             pagination: false,
//             navigationText: ['<div class="lft-btn"><i class="fa fa-angle-left"></i></div>', '<div class="rgt-btn"><i class="fa fa-angle-right"></div>'],
//             navigation: true,
//         });
//         $(".hbr-slider").owlCarousel({
//             items:4,
// 			 margin: 20,
//             itemsDesktop: [1080, 4],
//             itemsDesktopSmall: [860, 3],
//             itemsTablet: [768, 3],
//             itemsTabletSmall: [639, 3],
//             itemsMobile: [479, 2],
//               pagination: false,
			
//             navigationText: ['<div class="lft-btn"><i class="fa fa-angle-left"></i></div>', '<div class="rgt-btn"><i class="fa fa-angle-right"></div>'],
//             navigation: true,
//         });
		
// 		 $(".homelatestprd .products-row").owlCarousel({
//             items:4,
// 			 margin: 20,
//             itemsDesktop: [1080, 4],
//             itemsDesktopSmall: [860, 3],
//             itemsTablet: [768, 3],
//             itemsTabletSmall: [639, 3],
//             itemsMobile: [479, 2],
//               pagination: false,
			
//             navigationText: ['<div class="lft-btn"><i class="fa fa-angle-left"></i></div>', '<div class="rgt-btn"><i class="fa fa-angle-right"></div>'],
//             navigation: true,
//         });
		
		
		
	
		
//     });
	
	
		
	
	
	
// });

