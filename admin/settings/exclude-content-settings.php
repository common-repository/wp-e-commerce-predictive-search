<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WPSC Predictive Search Exclude Content Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WPSC_PS_Exclude_Content_Settings extends WPSC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'exclude-content';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = '';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wpsc_ps_exclude_contents_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Exclude Content Settings successfully saved.', 'wp-e-commerce-predictive-search' ),
				'error_message'		=> __( 'Error: Exclude Content Settings can not save.', 'wp-e-commerce-predictive-search' ),
				'reset_message'		=> __( 'Exclude Content Settings successfully reseted.', 'wp-e-commerce-predictive-search' ),
			);
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		//add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wpsc_predictive_search_admin_interface;
		
		$wpsc_predictive_search_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wpsc_predictive_search_admin_interface;
		
		$wpsc_predictive_search_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'exclude-content',
			'label'				=> __( 'Exclude Content', 'wp-e-commerce-predictive-search' ),
			'callback_function'	=> 'wpsc_ps_exclude_content_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wpsc_predictive_search_admin_interface;
		
		$output = '';
		$output .= $wpsc_predictive_search_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		
		global $wpdb;
		$all_products = array();
		$all_posts = array();
		$all_pages = array();
		$all_p_categories = array();
		$all_p_tags = array();
		
		if ( is_admin() && in_array (basename($_SERVER['PHP_SELF']), array('edit.php') ) && isset( $_GET['tab'] ) && $_GET['tab'] == 'exclude-content' ) {
			
		$results_products = $wpdb->get_results("SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_type='wpsc-product' AND post_status='publish' ORDER BY post_title ASC");
		if ($results_products) {
			foreach($results_products as $product_data) {
				$all_products[$product_data->ID] = $product_data->post_title;
			}
		}
		$results_posts = $wpdb->get_results("SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_type='post' AND post_status='publish' ORDER BY post_title ASC");
		if ($results_posts) {
			foreach($results_posts as $post_data) {
				$all_posts[$post_data->ID] = $post_data->post_title;
			}
		}
		$results_pages = $wpdb->get_results("SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_type='page' AND post_status='publish' ORDER BY post_title ASC");
		if ($results_pages) {
			foreach($results_pages as $page_data) {
				$all_pages[$page_data->ID] = $page_data->post_title;
			}
		}
		$results_p_categories = $wpdb->get_results("SELECT t.term_id, t.name FROM ".$wpdb->prefix."terms AS t INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON(t.term_id=tt.term_id) WHERE tt.taxonomy='wpsc_product_category' ORDER BY t.name ASC");
		if ($results_p_categories) {
			foreach($results_p_categories as $p_categories_data) {
				$all_p_categories[$p_categories_data->term_id] = $p_categories_data->name;
			}
		}
		$results_p_tags = $wpdb->get_results("SELECT t.term_id, t.name FROM ".$wpdb->prefix."terms AS t INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON(t.term_id=tt.term_id) WHERE tt.taxonomy='product_tag' ORDER BY t.name ASC");
		if ($results_p_tags) {
			foreach($results_p_tags as $p_tags_data) {
				$all_p_tags[$p_tags_data->term_id] = $p_tags_data->name;
			}
		}
		
		}
				
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Exclude From Predictive Search', 'wp-e-commerce-predictive-search' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Exclude Products', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_exclude_products',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Products', 'wp-e-commerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_products,
			),
			array(  
				'name' 		=> __( 'Exclude Product Categories', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_exclude_p_categories',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Product Categories', 'wp-e-commerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_p_categories,
			),
			array(  
				'name' 		=> __( 'Exclude Product Tags', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_exclude_p_tags',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Product Tags', 'wp-e-commerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_p_tags,
			),
			array(  
				'name' 		=> __( 'Exclude Posts', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_exclude_posts',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Posts', 'wp-e-commerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_posts,
			),
			array(  
				'name' 		=> __( 'Exclude Pages', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_exclude_pages',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Pages', 'wp-e-commerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_pages,
			),
		
        ));
	}
	
}

global $wpsc_ps_exclude_content_settings;
$wpsc_ps_exclude_content_settings = new WPSC_PS_Exclude_Content_Settings();

/** 
 * wpsc_ps_exclude_content_settings_form()
 * Define the callback function to show subtab content
 */
function wpsc_ps_exclude_content_settings_form() {
	global $wpsc_ps_exclude_content_settings;
	$wpsc_ps_exclude_content_settings->settings_form();
}

?>