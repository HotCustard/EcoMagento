 require([

    'jquery','mage/translate',

    'jquery/ui'

    ], function($, $t){

    jQuery(document).ready(function() { 



    	jQuery(".question-listing").hide();

    	jQuery(".view-question label").click(function(){

            jQuery(".question-listing").slideToggle("slow");

        });
		
		
		
		
		
		
		
		jQuery('a#prdInquiry').on('click', function(e){
        jQuery('.modal').prepend('<div class="modal-backdrop fade show"></div>');
		 jQuery('body').addClass('modal-open');
		 jQuery('.modal.fade').addClass('show');
        jQuery('#prd-inquiry').fadeIn(100);
        jQuery('div.modal-backdrop, #prd-inquiry a.close-modal-btn').on('click', function(){
            jQuery('div.modal-backdrop.fade.show').remove();
			jQuery('body').removeClass('modal-open');
			jQuery('.modal.fade').removeClass('show');
            jQuery('#prd-inquiry').hide();
        });
        e.preventDefault();
    });
		
		

		
		
		



		
		
		
		

  	});

})