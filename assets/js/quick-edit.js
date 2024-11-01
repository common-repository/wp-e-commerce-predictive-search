jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  
		
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $wpsc_predictive_search_inline_data = jQuery('#wpsc_predictive_search_inline_' + post_id );
		
		var predictive_search_focuskw 				= $wpsc_predictive_search_inline_data.find('.predictive_search_focuskw').text();
		var ecommerce_search_exclude_item 			= $wpsc_predictive_search_inline_data.find('.ecommerce_search_exclude_item').text();
		
		jQuery('#wpsc-predictive-search-fields-quick textarea[name="_predictive_search_focuskw"]', '.inline-edit-row').text(predictive_search_focuskw);
		
		if (ecommerce_search_exclude_item=='yes') {
			jQuery('#wpsc-predictive-search-fields-quick input[name="_ecommerce_search_exclude_item"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('#wpsc-predictive-search-fields-quick input[name="_ecommerce_search_exclude_item"]', '.inline-edit-row').removeAttr('checked'); 
		}
    });  
    
    jQuery('#wpbody').on('click', '#doaction, #doaction2', function(){  

		jQuery('#wpsc-predictive-search-fields-bulk .wpsc-predictive-keyword-value').hide();
		
	});
	
	 jQuery('#wpbody').on('change', '#wpsc-predictive-search-fields-bulk .change_to', function(){  
    
    	if (jQuery(this).val() > 0) {
    		jQuery(this).closest('div').find('.wpsc-predictive-keyword-value').show();
    	} else {
    		jQuery(this).closest('div').find('.wpsc-predictive-keyword-value').hide();
    	}
    
    });
});  