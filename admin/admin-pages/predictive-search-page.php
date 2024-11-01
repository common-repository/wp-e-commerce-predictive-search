<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WPSC Predictive Search Page

TABLE OF CONTENTS

- var menu_slug
- var page_data

- __construct()
- page_init()
- page_data()
- add_admin_menu()
- tabs_include()
- admin_settings_page()

-----------------------------------------------------------------------------------*/

class WPSC_Admin_Predictive_Search_Page extends WPSC_Predictive_Search_Admin_UI
{	
	/**
	 * @var string
	 */
	private $menu_slug = 'wpsc-predictive-search';
	
	/**
	 * @var array
	 */
	private $page_data;
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->page_init();
		$this->tabs_include();
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* page_init() */
	/* Page Init */
	/*-----------------------------------------------------------------------------------*/
	public function page_init() {
		
		add_filter( $this->plugin_name . '_add_admin_menu', array( $this, 'add_admin_menu' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* page_data() */
	/* Get Page Data */
	/*-----------------------------------------------------------------------------------*/
	public function page_data() {
		
		$page_data = array(
			'type'				=> 'submenu',
			'parent_slug'		=> 'edit.php?post_type=wpsc-product',
			'page_title'		=> __( 'Predictive Search', 'wp-e-commerce-predictive-search' ),
			'menu_title'		=> __( 'Predictive Search', 'wp-e-commerce-predictive-search' ),
			'capability'		=> 'manage_options',
			'menu_slug'			=> $this->menu_slug,
			'function'			=> 'wpsc_admin_predictive_search_page_show',
			'admin_url'			=> 'edit.php?post_type=wpsc-product',
			'callback_function' => '',
			'script_function' 	=> '',
			'view_doc'			=> '',
		);
		
		if ( $this->page_data ) return $this->page_data;
		return $this->page_data = $page_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_admin_menu() */
	/* Add This page to menu on left sidebar */
	/*-----------------------------------------------------------------------------------*/
	public function add_admin_menu( $admin_menu ) {
		
		if ( ! is_array( $admin_menu ) ) $admin_menu = array();
		$admin_menu[] = $this->page_data();
		
		return $admin_menu;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* tabs_include() */
	/* Include all tabs into this page
	/*-----------------------------------------------------------------------------------*/
	public function tabs_include() {
		
		include_once( $this->admin_plugin_dir() . '/tabs/global-settings-tab.php' );
		include_once( $this->admin_plugin_dir() . '/tabs/all-results-pages-tab.php' );
		include_once( $this->admin_plugin_dir() . '/tabs/exclude-content-tab.php' );
		include_once( $this->admin_plugin_dir() . '/tabs/search-function-tab.php' );
		include_once( $this->admin_plugin_dir() . '/tabs/google-analytics-tab.php' );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page */
	/*-----------------------------------------------------------------------------------*/
	public function admin_settings_page() {
		global $wpsc_predictive_search_admin_init;
		
		$wpsc_predictive_search_admin_init->admin_settings_page( $this->page_data() );
	}
	
}

global $wpsc_admin_predictive_search_page;
$wpsc_admin_predictive_search_page = new WPSC_Admin_Predictive_Search_Page();

/** 
 * wpsc_admin_predictive_search_page_show()
 * Define the callback function to show page content
 */
function wpsc_admin_predictive_search_page_show() {
	global $wpsc_admin_predictive_search_page;
	$wpsc_admin_predictive_search_page->admin_settings_page();
}

?>