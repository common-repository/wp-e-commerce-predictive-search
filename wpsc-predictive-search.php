<?php
/*
Plugin Name: WP e-Commerce Predictive Search PRO
Description: Super charge you site with WP e-Commerce Predictive Products Search. Delivers stunning results as you type. Searches your entire WP e-Commerce product database
Version: 3.0.0
Author: a3rev Software
Author URI: https://a3rev.com/
Text Domain: wp-e-commerce-predictive-search
Domain Path: /languages
Requires at least: 4.6
Tested up to: 4.8.0
License: GPLv2 or later

	WP e-Commerce Predictive Search PRO. Plugin for the WP e-Commerce plugin.
	Copyright © 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define( 'WPSC_PS_FILE_PATH', dirname(__FILE__) );
define( 'WPSC_PS_DIR_NAME', basename(WPSC_PS_FILE_PATH) );
define( 'WPSC_PS_FOLDER', dirname(plugin_basename(__FILE__)) );
define( 'WPSC_PS_DIR', WP_PLUGIN_DIR . '/' . WPSC_PS_FOLDER);
define( 'WPSC_PS_NAME', plugin_basename(__FILE__) );
define( 'WPSC_PS_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WPSC_PS_JS_URL',  WPSC_PS_URL . '/assets/js' );
define( 'WPSC_PS_CSS_URL',  WPSC_PS_URL . '/assets/css' );
define( 'WPSC_PS_IMAGES_URL',  WPSC_PS_URL . '/assets/images' );
if(!defined("WPSC_PREDICTIVE_SEARCH_DOCS_URI"))
    define("WPSC_PREDICTIVE_SEARCH_DOCS_URI", "http://docs.a3rev.com/user-guides/wp-e-commerce/wpec-predictive-search/");

/**
 * Load Localisation files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales found in:
 * 		- WP_LANG_DIR/wp-e-commerce-predictive-search/wp-e-commerce-predictive-search-LOCALE.mo
 * 	 	- WP_LANG_DIR/plugins/wp-e-commerce-predictive-search-LOCALE.mo
 * 	 	- /wp-content/plugins/wp-e-commerce-predictive-search/languages/wp-e-commerce-predictive-search-LOCALE.mo (which if not found falls back to)
 */
function wpscps_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-e-commerce-predictive-search' );

	load_textdomain( 'wp-e-commerce-predictive-search', WP_LANG_DIR . '/wp-e-commerce-predictive-search/wp-e-commerce-predictive-search-' . $locale . '.mo' );
	load_plugin_textdomain( 'wp-e-commerce-predictive-search', false, WPSC_PS_FOLDER.'/languages' );
}


include('admin/admin-ui.php');
include('admin/admin-interface.php');

include('admin/admin-pages/predictive-search-page.php');

include('admin/admin-init.php');

include 'classes/class-wpsc-predictive-search-filter.php';
include 'classes/class-wpsc-predictive-search.php';
include 'classes/class-wpsc-predictive-search-shortcodes.php';
include 'classes/class-wpsc-predictive-search-metabox.php';
include 'classes/class-wpsc-predictive-search-bulk-quick-editions.php';
include 'widget/wpsc-predictive-search-widgets.php';

// Editor
include 'tinymce3/tinymce.php';

include 'admin/wpsc-predictive-search-init.php';

/**
* Call when the plugin is activated
*/
register_activation_hook(__FILE__,'wpsc_predictive_install');

?>