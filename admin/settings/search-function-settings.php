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

class WPSC_PS_Search_Function_Settings extends WPSC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'search-function';
	
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
	public $form_key = 'wpsc_ps_search_function_settings';
	
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
				'success_message'	=> __( 'Search Function Settings successfully saved.', 'wp-e-commerce-predictive-search' ),
				'error_message'		=> __( 'Error: Search Function Settings can not save.', 'wp-e-commerce-predictive-search' ),
				'reset_message'		=> __( 'Search Function Settings successfully reseted.', 'wp-e-commerce-predictive-search' ),
			);
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		
		add_action( $this->plugin_name . '_settings_' . 'predictive_search_code' . '_start', array( $this, 'predictive_search_code_start' ) );
		
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
			'name'				=> 'search-function',
			'label'				=> __( 'Search Function', 'wp-e-commerce-predictive-search' ),
			'callback_function'	=> 'wpsc_ps_search_function_settings_form',
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
				
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Predictive Search Function', 'wp-e-commerce-predictive-search' ),
                'type' 		=> 'heading',
          		'id' 		=> 'predictive_search_code'
           	),
			
			array(
            	'name' 		=> __( 'Customize Search Function values :', 'wp-e-commerce-predictive-search' ),
				'desc'		=> __("The values you set here will be shown when you add the global search function to your header.php file. After adding the global function to your header.php file you can change the values here and 'Update' and they will be auto updated in the function.", 'wp-e-commerce-predictive-search' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Product name', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Product Name to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_product_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product SKU', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Product SKU to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_p_sku_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product category', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Product Categories to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_p_cat_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product tag', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Product Tags to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_p_tag_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Post', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Posts to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_post_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Page', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of Pages to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_page_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Price', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('On to show Product prices', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_show_price',
				'type' 		=> 'onoff_checkbox',
				'default'	=> '1',
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'wp-e-commerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'wp-e-commerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Description Characters', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Number of characters from product description to show in search field drop-down. Default value is "100".', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_character_max',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Width', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> 'px. '.__('Leave &lt;empty&gt; for 100% wide', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_width',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Padding', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_padding',
				'type' 		=> 'array_textfields',
				'ids'		=> array( 
	 								array(  'id' 		=> 'ecommerce_search_padding_top',
	 										'name' 		=> __( 'Top', 'wp-e-commerce-predictive-search' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),
	 
	 								array(  'id' 		=> 'ecommerce_search_padding_bottom',
	 										'name' 		=> __( 'Bottom', 'wp-e-commerce-predictive-search' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),
											
									array(  'id' 		=> 'ecommerce_search_padding_left',
	 										'name' 		=> __( 'Left', 'wp-e-commerce-predictive-search' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),
											
									array(  'id' 		=> 'ecommerce_search_padding_right',
	 										'name' 		=> __( 'Right', 'wp-e-commerce-predictive-search' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),
	 							)
			),
			array(  
				'name' 		=> __( 'Custom style', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('Put other custom style for the Predictive search box', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_custom_style',
				'type' 		=> 'text',
			),
			array(  
				'name' 		=> __( 'Global search', 'wp-e-commerce-predictive-search' ),
				'desc' 		=> __('On to set global search or search in current product category or current product tag. "Checked" to activate global search.', 'wp-e-commerce-predictive-search' ),
				'id' 		=> 'ecommerce_search_global_search',
				'type' 		=> 'onoff_checkbox',
				'default'	=> '1',
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'wp-e-commerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'wp-e-commerce-predictive-search' ),
			),
		
        ));
	}
	
	public function predictive_search_code_start() {
		echo '<tr valign="top"><td class="forminp" colspan="2">';
		?>
        <?php _e('Copy and paste this global function into your themes header.php file to replace any existing search function. (Be sure to delete the existing WordPress, WP e-Commerce or Theme search function)', 'wp-e-commerce-predictive-search' );?>
            <br /><code>&lt;?php<br />
            $ps_echo = true ; <br /> 
            if ( function_exists( 'wpsc_search_widget' ) ) wpsc_search_widget( $ps_echo ); <br /> 
            ?&gt;</code>
		<?php echo '</td></tr>';
	}
	
}

global $wpsc_ps_search_function_settings;
$wpsc_ps_search_function_settings = new WPSC_PS_Search_Function_Settings();

/** 
 * wpsc_ps_search_function_settings_form()
 * Define the callback function to show subtab content
 */
function wpsc_ps_search_function_settings_form() {
	global $wpsc_ps_search_function_settings;
	$wpsc_ps_search_function_settings->settings_form();
}

?>