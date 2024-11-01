<?php
/**
 * Register Activation Hook
 */
update_option('wpsc_predictive_search_plugin', 'wpsc_predictive_search');
function wpsc_predictive_install() {
	global $wpdb;
	global $wp_rewrite;
	WPSC_Predictive_Search::create_page( 'ecommerce-search', 'ecommerce_search_page_id', __('Predictive Search', 'wp-e-commerce-predictive-search' ), '[ecommerce_search]' );
	
	// Set Settings Default from Admin Init
	global $wpsc_predictive_search_admin_init;
	$wpsc_predictive_search_admin_init->set_default_settings();

	delete_option('ecommerce_search_lite_clean_on_deletion');

	update_option('wpsc_predictive_search_version', '3.0.0');

	delete_transient("wpsc_predictive_search_update_info");
	$wp_rewrite->flush_rules();
	
	update_option('wpsc_predictive_search_just_installed', true);
}

function wpscps_init() {
	if ( get_option('wpsc_predictive_search_just_installed') ) {
		delete_option('wpsc_predictive_search_just_installed');
		wp_redirect( admin_url( 'edit.php?post_type=wpsc-product&page=wpsc-predictive-search', 'relative' ) );
		exit;
	}

	wpscps_plugin_textdomain();
}

function register_widget_wpscps_predictive_search() {
	register_widget('WPSC_Predictive_Search_Widgets');
}

// Add language
add_action('init', 'wpscps_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WPSC_Predictive_Search_Hook_Filter', 'a3_wp_admin' ) );

// Load Global variables
add_action( 'plugins_loaded', array( 'WPSC_Predictive_Search', 'plugins_loaded' ), 8 );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WPSC_Predictive_Search_Hook_Filter', 'plugin_extra_links'), 10, 2 );
	
// Need to call Admin Init to show Admin UI
global $wpsc_predictive_search_admin_init;
$wpsc_predictive_search_admin_init->init();

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_' . WPSC_PS_NAME, array( 'WPSC_Predictive_Search_Hook_Filter', 'settings_plugin_links' ) );

// Custom Rewrite Rules
add_action( 'init', array('WPSC_Predictive_Search_Hook_Filter', 'custom_rewrite_rule' ) );
	
// Registry widget
add_action( 'widgets_init', 'register_widget_wpscps_predictive_search' );

// Add shortcode [ecommerce_search]
add_shortcode( 'ecommerce_search', array('WPSC_Predictive_Search_Shortcodes', 'parse_shortcode_search_result' ) );

// Add shortcode [ecommerce_widget_search]
add_shortcode( 'ecommerce_search_widget', array('WPSC_Predictive_Search_Shortcodes', 'parse_shortcode_search_widget' ) );

// Add Predictive Search Meta Box to all post type
add_action( 'add_meta_boxes', array('WPSC_Predictive_Search_Meta','create_custombox'), 9 );

// Save Predictive Search Meta Box to all post type
if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	add_action( 'save_post', array('WPSC_Predictive_Search_Meta','save_custombox' ) );
}

// Add search widget icon to Page Editor
if (in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php') ) ) {
	add_action( 'media_buttons_context', array('WPSC_Predictive_Search_Shortcodes','add_search_widget_icon' ) );
	add_action( 'admin_footer', array('WPSC_Predictive_Search_Shortcodes','add_search_widget_mce_popup' ) );
}

if (!is_admin()) {
	add_filter( 'posts_search', array('WPSC_Predictive_Search_Hook_Filter', 'search_by_title_only'), 500, 2 );
	add_filter( 'posts_orderby', array('WPSC_Predictive_Search_Hook_Filter', 'predictive_posts_orderby'), 500, 2 );
	add_filter( 'get_meta_sql', array('WPSC_Predictive_Search_Hook_Filter', 'remove_where_on_focuskw_meta'), 500, 6 );
	add_filter( 'posts_request', array('WPSC_Predictive_Search_Hook_Filter', 'posts_request_unconflict_role_scoper_plugin'), 500, 2);
}

// AJAX get result search page
add_action( 'wp_ajax_wpscps_get_result_search_page', array('WPSC_Predictive_Search_Shortcodes','get_result_search_page' ) );
add_action( 'wp_ajax_nopriv_wpscps_get_result_search_page', array('WPSC_Predictive_Search_Shortcodes','get_result_search_page' ) );

// AJAX get result search popup
add_action( 'wp_ajax_wpscps_get_result_popup', array('WPSC_Predictive_Search','wpscps_get_result_popup' ) );
add_action( 'wp_ajax_nopriv_wpscps_get_result_popup', array('WPSC_Predictive_Search','wpscps_get_result_popup' ) );

//Add ajax search box script and style at footer
if ( ! is_admin() )
	add_action('init',array('WPSC_Predictive_Search_Hook_Filter','wpscps_add_frontend_style'));

function wpsc_search_widget($ps_echo = true, $product_items = 6, $p_sku_items = 0, $p_cat_items = 0, $p_tag_items = 0, $post_items = 0, $page_items = 0, $character_max = 100, $style='', $global_search = true, $show_price = 1) {
	
	if (!$global_search) $global_search = 0; else $global_search = 1;
	
	$product_items = get_option('ecommerce_search_product_items', $product_items);
	$p_sku_items = get_option('ecommerce_search_p_sku_items', $p_sku_items);
	$p_cat_items = get_option('ecommerce_search_p_cat_items', $p_cat_items);
	$p_tag_items = get_option('ecommerce_search_p_tag_items', $p_tag_items);
	$post_items = get_option('ecommerce_search_post_items', $post_items);
	$page_items = get_option('ecommerce_search_page_items', $page_items);
	$show_price = get_option('ecommerce_search_show_price', $show_price);
	$character_max = get_option('ecommerce_search_character_max', $character_max);
	$search_box_text = get_option('ecommerce_search_box_text');
	
	$custom_style = '';
	if (get_option('ecommerce_search_custom_style', $style) != '') $custom_style .= get_option('ecommerce_search_custom_style', $style); else $custom_style .= $style;
	if (get_option('ecommerce_search_width') != '') $custom_style .= 'width:'.get_option('ecommerce_search_width').'px !important;';
	if (get_option('ecommerce_search_padding_top') != '') $custom_style .= 'padding-top:'.get_option('ecommerce_search_padding_top').'px !important;';
	if (get_option('ecommerce_search_padding_bottom') != '') $custom_style .= 'padding-bottom:'.get_option('ecommerce_search_padding_bottom').'px !important;';
	if (get_option('ecommerce_search_padding_left') != '') $custom_style .= 'padding-left:'.get_option('ecommerce_search_padding_left').'px !important;';
	if (get_option('ecommerce_search_padding_right') != '') $custom_style .= 'padding-right:'.get_option('ecommerce_search_padding_right').'px !important;';
		
	$global_search = get_option('ecommerce_search_product_name', $global_search);
	
	$items_search_default = WPSC_Predictive_Search_Widgets::get_items_search();
	$items = array();
	foreach ($items_search_default as $key => $data) {
		if (isset(${$key.'_items'}) )
			$items[$key] = ${$key.'_items'};
		else 
			$items[$key] = $data['number'];
	}
	$widget_id = rand(100, 10000);
	$ps_search_html = WPSC_Predictive_Search_Widgets::wpscps_results_search_form($widget_id, $items, $character_max, $custom_style, $global_search, $search_box_text, $show_price);
	
	if ( $ps_echo != false ) echo $ps_search_html;
	else return $ps_search_html;
}

add_action( 'init', 'wpsc_predictive_upgrade_plugin' );

function wpsc_predictive_upgrade_plugin() {
	// Upgrade to 3.0.0
	if ( version_compare( get_option('wpsc_predictive_search_version'), '3.0.0') === -1){
		update_option('wpsc_predictive_search_version', '3.0.0');

		global $wpsc_predictive_search_admin_init;
		$wpsc_predictive_search_admin_init->set_default_settings();

		
	}

	update_option('wpsc_predictive_search_version', '3.0.0');

}

?>