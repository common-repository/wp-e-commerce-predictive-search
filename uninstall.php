<?php
/**
 * Plugin Uninstall
 *
 * Uninstalling deletes options, tables, and pages.
 *
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();


if ( get_option('ecommerce_search_clean_on_deletion') == 1 ) {
    delete_option('ecommerce_search_text_lenght');
    delete_option('ecommerce_search_result_items');
    delete_option('ecommerce_search_sku_enable');
    delete_option('ecommerce_search_price_enable');
    delete_option('ecommerce_search_addtocart_enable');
    delete_option('ecommerce_search_categories_enable');
    delete_option('ecommerce_search_tags_enable');
    delete_option('ecommerce_search_box_text');
    delete_option('ecommerce_search_page_id');
    delete_option('ecommerce_search_exclude_products');
    
    delete_option('ecommerce_search_exclude_p_categories');
    delete_option('ecommerce_search_exclude_p_tags');
    delete_option('ecommerce_search_exclude_posts');
    delete_option('ecommerce_search_exclude_pages');
    delete_option('ecommerce_search_focus_enable');
    delete_option('ecommerce_search_focus_plugin');
    delete_option('ecommerce_search_product_items');
    delete_option('ecommerce_search_p_sku_items');
    delete_option('ecommerce_search_p_cat_items');
    delete_option('ecommerce_search_p_tag_items');
    delete_option('ecommerce_search_post_items');
    delete_option('ecommerce_search_page_items');
    delete_option('ecommerce_search_character_max');
    delete_option('ecommerce_search_width');
    delete_option('ecommerce_search_padding_top');
    delete_option('ecommerce_search_padding_bottom');
    delete_option('ecommerce_search_padding_left');
    delete_option('ecommerce_search_padding_right');
    delete_option('ecommerce_search_custom_style');
    delete_option('ecommerce_search_global_search');
    
    delete_option('ecommerce_search_enable_google_analytic');
    delete_option('ecommerce_search_google_analytic_id');
    delete_option('ecommerce_search_google_analytic_query_parameter');
    
    delete_option('ecommerce_search_clean_on_deletion');
    
    delete_post_meta_by_key('_predictive_search_focuskw');
    
}
