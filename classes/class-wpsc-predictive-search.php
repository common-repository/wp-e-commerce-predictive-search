<?php
/**
 * WPSC Predictive Search
 *
 * Class Function into ecommerce plugin
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * get_id_excludes()
 * wpscps_get_product_thumbnail()
 * get_product_cat_thumbnail()
 * wpscps_limit_words()
 * wpscps_get_result_popup()
 * create_page()
 * get_product_variation_price_available()
 * strip_shortcodes()
 * upgrade_version_2_0()
 */
class WPSC_Predictive_Search
{
	
	public static function plugins_loaded() {
		global $wpsc_predictive_id_excludes;
		
		WPSC_Predictive_Search::get_id_excludes();
	}
	
	public static function get_id_excludes() {
		global $wpsc_predictive_id_excludes;
		
		$exclude_p_categories = get_option('ecommerce_search_exclude_p_categories', '');
		if (!is_array($exclude_p_categories)) {
			$exclude_p_categories_array = explode(",", $exclude_p_categories);
			if (is_array($exclude_p_categories_array) && count($exclude_p_categories_array) > 0) {
				$exclude_p_categories_array_new = array();
				foreach ($exclude_p_categories_array as $exclude_p_categories_item) {
					if ( trim($exclude_p_categories_item) > 0) $exclude_p_categories_array_new[] = $exclude_p_categories_item;
				}
				$exclude_p_categories = implode(",", $exclude_p_categories_array_new);
			} else {
				$exclude_p_categories = '';
			}
		} else {
			$exclude_p_categories = implode(",", $exclude_p_categories);
		}
		
		$exclude_p_tags = get_option('ecommerce_search_exclude_p_tags', '');
		if (!is_array($exclude_p_tags)) {
			$exclude_p_tags_array = explode(",", $exclude_p_tags);
			if (is_array($exclude_p_tags_array) && count($exclude_p_tags_array) > 0) {
				$exclude_p_tags_array_new = array();
				foreach ($exclude_p_tags_array as $exclude_p_tags_item) {
					if ( trim($exclude_p_tags_item) > 0) $exclude_p_tags_array_new[] = $exclude_p_tags_item;
				}
				$exclude_p_tags = implode(",", $exclude_p_tags_array_new);
			} else {
				$exclude_p_tags = '';
			}
		} else {
			$exclude_p_tags = implode(",", $exclude_p_tags);
		}
		
		$exclude_products = get_option('ecommerce_search_exclude_products', '');
		if (is_array($exclude_products)) {
			$exclude_products = implode(",", $exclude_products);
		}
		
		$exclude_posts = get_option('ecommerce_search_exclude_posts', '');
		if (is_array($exclude_posts)) {
			$exclude_posts = implode(",", $exclude_posts);
		}
		
		$exclude_pages = get_option('ecommerce_search_exclude_pages', '');
		if (is_array($exclude_pages)) {
			$exclude_pages = implode(",", $exclude_pages);
		}
		
		$wpsc_predictive_id_excludes = array();
		$wpsc_predictive_id_excludes['exclude_products'] = $exclude_products;
		$wpsc_predictive_id_excludes['exclude_p_categories'] = $exclude_p_categories;
		$wpsc_predictive_id_excludes['exclude_p_tags'] = $exclude_p_tags;
		$wpsc_predictive_id_excludes['exclude_posts'] = $exclude_posts;
		$wpsc_predictive_id_excludes['exclude_pages'] = get_option('ecommerce_search_page_id').','.$exclude_pages;
		
		return $wpsc_predictive_id_excludes;
	}
	
	public static function wpscps_get_product_thumbnail( $product_id, $size = 'product-thumbnails', $placeholder_width = 0, $placeholder_height = 0  ) {
		$image_url = '';
		$thumbnail_id = 0;
		if ( $placeholder_width == 0 )
			$placeholder_width = 64;
		if ( $placeholder_height == 0 )
			$placeholder_height = 64;
			// Use product thumbnail
		if ( has_post_thumbnail( $product_id ) ) {
				$thumbnail_id = get_post_thumbnail_id( $product_id  );
			// Use first product image
		} else {
		
				// Get all attached images to this product
				$attached_images = (array)get_posts( array(
					'post_type'   => 'attachment',
					'numberposts' => 1,
					'post_status' => null,
					'post_parent' => $product_id ,
					'orderby'     => 'menu_order',
					'order'       => 'ASC'
				) );
		
				if ( !empty( $attached_images ) )
					$thumbnail_id = $attached_images[0]->ID;
		}
			
		if ($thumbnail_id != 0) {
			$image_attribute = wp_get_attachment_image_src( $thumbnail_id, 'full');	
	
		
			$image_lager_default_url = $image_attribute[0];
			$width_old = $image_attribute[1];
			$height_old = $image_attribute[2];
			$g_thumb_width  = $placeholder_width;
			$g_thumb_height = $placeholder_height;
			$thumb_height = $g_thumb_height;
			$thumb_width = $g_thumb_width;
			if($width_old > $g_thumb_width){
				$factor = ($width_old / $g_thumb_width);
				$thumb_height = round($height_old / $factor);
			
				$intermediate_size = "wpsc-{$thumb_width}x{$thumb_height}";
				$image_meta = get_post_meta( $thumbnail_id, '' );
				
				// Clean up the meta array
				foreach ( $image_meta as $meta_name => $meta_value )
				$image_meta[$meta_name] = maybe_unserialize( array_pop( $meta_value ) );
				
				$attachment_metadata = $image_meta['_wp_attachment_metadata'];
				// Determine if we already have an image of this size
				if ( isset( $attachment_metadata['sizes'] ) && (count( $attachment_metadata['sizes'] ) > 0) && ( isset( $attachment_metadata['sizes'][$intermediate_size] ) ) ) {
					$intermediate_image_data = image_get_intermediate_size( $thumbnail_id, $intermediate_size );
					$uploads = wp_upload_dir();
					if ( $intermediate_image_data['path'] != '' && file_exists( $uploads['basedir'] . "/" .$intermediate_image_data['path'] ) ) {
						$image_url = $intermediate_image_data['url'];
					} else {
						$image_url = home_url( "index.php?wpsc_action=scale_image&amp;attachment_id={$thumbnail_id}&amp;width=$thumb_width&amp;height=$thumb_height" );
					}
				} else {
					$image_url = home_url( "index.php?wpsc_action=scale_image&amp;attachment_id={$thumbnail_id}&amp;width=$thumb_width&amp;height=$thumb_height" );
				}
			} else {
				$image_url = $image_lager_default_url;
			}
		}
			
		if (trim($image_url != '')) {
			return '<img src="' . $image_url . '" alt="" width="' . $placeholder_width . '" />';
		} else {
			return '<img src="'. WPSC_CORE_THEME_URL . 'wpsc-images/noimage.png" alt="Placeholder" width="' . $placeholder_width . '" height="' . $placeholder_height . '" />';
		}
	}
	
	public static function get_product_cat_thumbnail( $term_id, $placeholder_width = 0, $placeholder_height = 0  ) {
		if ( $placeholder_width == 0 )
			$placeholder_width = 64;
		if ( $placeholder_height == 0 )
			$placeholder_height = 64;
		$category_image = wpsc_get_categorymeta($term_id, 'image');
		if((!empty($category_image)) && is_file(WPSC_CATEGORY_DIR.$category_image)) {
			$g_thumb_width = $placeholder_width;
			$g_thumb_height = $placeholder_height;
				
			$info_image = getimagesize ( WPSC_CATEGORY_DIR.$category_image );
			list ( $width_old, $height_old ) = $info_image;
			$thumb_height = $g_thumb_height;
			$thumb_width = $g_thumb_width;
			if($width_old > $g_thumb_width){
				$factor = ($width_old / $g_thumb_width);
				$thumb_height = round($height_old / $factor);
					
				$intermediate_size_data = image_make_intermediate_size( WPSC_CATEGORY_DIR.$category_image, $thumb_width, $thumb_height, false );
				if ($intermediate_size_data != false) wpsc_update_categorymeta( $term_id, 'image', $intermediate_size_data['file'] );
				$category_image = wpsc_get_categorymeta($term_id, 'image');
			}
			$category_url = WPSC_CATEGORY_URL.basename($category_image);
			return '<img src="' . $category_url . '" alt="" width="' . $placeholder_width . '" />';
		} else {
			return '<img src="'. WPSC_CORE_THEME_URL . 'wpsc-images/noimage.png" alt="Placeholder" width="' . $placeholder_width . '" height="' . $placeholder_height . '" />';
		}
	}
	
	public static function wpscps_limit_words($str='',$len=100,$more=true) {
	   if (trim($len) == '' || $len < 0) $len = 100;
	   if ( $str=="" || $str==NULL ) return $str;
	   if ( is_array($str) ) return $str;
	   $str = trim($str);
	   $str = strip_tags(str_replace("\r\n", "", $str));
	   if ( strlen($str) <= $len ) return $str;
	   $str = substr($str,0,$len);
	   if ( $str != "" ) {
			if ( !substr_count($str," ") ) {
					  if ( $more ) $str .= " ...";
					return $str;
			}
			while( strlen($str) && ($str[strlen($str)-1] != " ") ) {
					$str = substr($str,0,-1);
			}
			$str = substr($str,0,-1);
			if ( $more ) $str .= " ...";
			}
			return $str;
	}
	
	public static function wpscps_get_result_popup() {
		add_filter( 'posts_search', array('WPSC_Predictive_Search_Hook_Filter', 'search_by_title_only'), 500, 2 );
		add_filter( 'posts_orderby', array('WPSC_Predictive_Search_Hook_Filter', 'predictive_posts_orderby'), 500, 2 );
		add_filter( 'get_meta_sql', array('WPSC_Predictive_Search_Hook_Filter', 'remove_where_on_focuskw_meta'), 500, 6 );
		add_filter( 'posts_request', array('WPSC_Predictive_Search_Hook_Filter', 'posts_request_unconflict_role_scoper_plugin'), 500, 2);
		global $wpdb;
		global $wpsc_predictive_id_excludes;
		$rs_headings = array();
		$rs_items = array();
		$rs_footers = array();
		$row = 6;
		$text_lenght = 100;
		$show_price = 1;
		$search_keyword = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$found_items = false;
		$total_product = $total_p_sku = $total_post = $total_page = $total_pcat = $total_ptag = 0;
		$items_search_default = WPSC_Predictive_Search_Widgets::get_items_search();
		$search_in_default = array();
		foreach ($items_search_default as $key => $data) {
			if ($data['number'] > 0) {
				$search_in_default[$key] = $data['number'];
			}
		}
		if (isset($_REQUEST['row']) && $_REQUEST['row'] > 0) $row = stripslashes( strip_tags( $_REQUEST['row'] ) );
		if (isset($_REQUEST['text_lenght']) && $_REQUEST['text_lenght'] >= 0) $text_lenght = stripslashes( strip_tags( $_REQUEST['text_lenght'] ) );
		if (isset($_REQUEST['show_price']) && trim($_REQUEST['show_price']) != '') $show_price = stripslashes( strip_tags( $_REQUEST['show_price'] ) );
		if (isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['q'] ) );
		if (isset($_REQUEST['pcat']) && trim($_REQUEST['pcat']) != '') $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		if (isset($_REQUEST['ptag']) && trim($_REQUEST['ptag']) != '') $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		if (isset($_REQUEST['scat']) && trim($_REQUEST['scat']) != '') $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		if (isset($_REQUEST['stag']) && trim($_REQUEST['stag']) != '') $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		if (isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = unserialize(stripslashes( strip_tags( $_REQUEST['search_in'] ) ) );
		if (!is_array($search_in) || count($search_in) < 1 || array_sum($search_in) < 1) $search_in = $search_in_default;
		
		$search_list = array();
		foreach ($search_in as $key => $number) {
			if ($number > 0)
				$search_list[$key] = $key;
		}
		
		$ecommerce_search_focus_enable = get_option('ecommerce_search_focus_enable');
		$ecommerce_search_focus_plugin = get_option('ecommerce_search_focus_plugin');
		$meta_query_args = array();
		if ( $ecommerce_search_focus_enable == 1 ) {
			$meta_query_args['relation'] = 'OR';
			$meta_query_args[] = array( 'key' => '_predictive_search_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE');
					
			if ($ecommerce_search_focus_plugin == 'yoast_seo_plugin') 
				$meta_query_args[] = array( 'key' => '_yoast_wpseo_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE');
			elseif ($ecommerce_search_focus_plugin == 'all_in_one_seo_plugin') 
				$meta_query_args[] = array( 'key' => '_aioseop_keywords', 'value' => $search_keyword, 'compare' => 'LIKE');
		}
				
		if ($search_keyword != '') {
			$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
			$search_nospecial = false;
			if ( $search_keyword != $search_keyword_nospecial ) $search_nospecial = true;
			
			$extra_parameter_product = '';
			$extra_parameter_post = '';
			
			if ( isset($search_in['product']) && $search_in['product'] > 0) {
								
				$row = $search_in['product'];
				$end_row = $row;
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> 0, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
				
				if ($pcat_slug != '') {
					$args['tax_query'] = array( array('taxonomy' => 'wpsc_product_category', 'field' => 'slug', 'terms' => $pcat_slug) );
					if (get_option('permalink_structure') == '')
						$extra_parameter_product .= '&pcat='.$pcat_slug;
					else
						$extra_parameter_product .= '/pcat/'.$pcat_slug;
				} elseif($ptag_slug != '') {
					$args['tax_query'] = array( array('taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug) );
					if (get_option('permalink_structure') == '')
						$extra_parameter_product .= '&ptag='.$ptag_slug;
					else
						$extra_parameter_product .= '/ptag/'.$ptag_slug;
				}
				
				$search_products = get_posts($args);
				
				$total_product = count($search_products);
				if ( $search_products && $total_product > 0 ) {
					$found_items = true;
					$rs_headings['product'] = "<div class='ajax_search_content_title'>".__('Product Name', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['product'] = '';
					foreach ( $search_products as $product ) {
						$link_detail = get_permalink($product->ID);
						$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($product->ID,'product-thumbnails',64,64);
						$product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $product->post_content) ) ) ),$text_lenght,'...');
						if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $product->post_excerpt) ) ) ),$text_lenght,'...');
						
						$price_html = '';
						if ( $show_price == 1)
							$price_html = WPSC_Predictive_Search_Shortcodes::get_product_price_dropdown($product->ID);
							
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.stripslashes( $product->post_title).'</span>'.$price_html.'<span class="rs_description">'.$product_description.'</span></div></a></div></div>';
						$rs_items['product'] .= $item_html.'[|]'.$link_detail.'[|]'.stripslashes( $product->post_title)."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			if ( isset($search_in['p_sku']) && $search_in['p_sku'] > 0) {
								
				$row = $search_in['p_sku'];
				$end_row = $row;
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> 0, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'meta_key' => '_wpsc_sku', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
				if ($pcat_slug != '') {
					$args['tax_query'] = array( array('taxonomy' => 'wpsc_product_category', 'field' => 'slug', 'terms' => $pcat_slug) );
					if (get_option('permalink_structure') == '')
						$extra_parameter_product .= '&pcat='.$pcat_slug;
					else
						$extra_parameter_product .= '/pcat/'.$pcat_slug;
				} elseif($ptag_slug != '') {
					$args['tax_query'] = array( array('taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug) );
					if (get_option('permalink_structure') == '')
						$extra_parameter_product .= '&ptag='.$ptag_slug;
					else
						$extra_parameter_product .= '/ptag/'.$ptag_slug;
				}
				
				$search_products = get_posts($args);
				
				$total_p_sku = count($search_products);
				if ( $search_products && $total_p_sku > 0 ) {
					$found_items = true;
					$rs_headings['p_sku'] = "<div class='ajax_search_content_title'>".__('Product SKU', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['p_sku'] = '';
					foreach ( $search_products as $product ) {
						$link_detail = get_permalink($product->ID);
						$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($product->ID,'product-thumbnails',64,64);
						$product_sku = stripslashes( get_post_meta($product->ID, '_wpsc_sku', true) );
						
						$price_html = '';
						if ( $show_price == 1)
							$price_html = WPSC_Predictive_Search_Shortcodes::get_product_price_dropdown($product->ID);
							
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.$product_sku.'</span><span class="rs_name">'.stripslashes( $product->post_title).'</span>'.$price_html.'</div></a></div></div>';
						$rs_items['p_sku'] .= $item_html.'[|]'.$link_detail.'[|]'.$product_sku."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			if ( isset($search_in['post']) && $search_in['post'] > 0) {
				
				$row = $search_in['post'];
				$end_row = $row;
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> 0, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'post', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_posts'], 'suppress_filters' => FALSE);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
				
				if ($cat_slug != '') {
					$args['category_name'] = $cat_slug;
					if (get_option('permalink_structure') == '')
						$extra_parameter_post .= '&scat='.$cat_slug;
					else
						$extra_parameter_post .= '/scat/'.$cat_slug;
				} elseif($tag_slug != '') {
					$args['tag'] = $tag_slug;
					if (get_option('permalink_structure') == '')
						$extra_parameter_post .= '&stag='.$tag_slug;
					else
						$extra_parameter_post .= '/stag/'.$tag_slug;
				}
								
				$search_posts = get_posts($args);
				$total_post = count($search_posts);
				if ( $search_posts && $total_post > 0 ) {
					$found_items = true;
					$rs_headings['post'] = "<div class='ajax_search_content_title'>".__('Posts', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['post'] = '';
					foreach ( $search_posts as $item ) {
						$link_detail = get_permalink($item->ID);
						$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($item->ID,'product-thumbnails',64,64);
						$product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $item->post_content) ) ) ),$text_lenght,'...');
						if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $item->post_excerpt) ) ) ),$text_lenght,'...');
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.stripslashes( $item->post_title).'</span><span class="rs_description">'.$product_description.'</span></div></a></div></div>';
						$rs_items['post'] .= $item_html.'[|]'.$link_detail.'[|]'.stripslashes( $item->post_title)."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			if ( isset($search_in['page']) && $search_in['page'] > 0) {
				$row = $search_in['page'];
				$end_row = $row;
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> 0, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'page', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_pages'], 'suppress_filters' => FALSE);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
				
				$search_pages = get_posts($args);
				$total_page = count($search_pages);
				if ( $search_pages && $total_page > 0 ) {
					$found_items = true;
					$rs_headings['page'] = "<div class='ajax_search_content_title'>".__('Pages', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['page'] = '';
					foreach ( $search_pages as $item ) {
						$link_detail = get_permalink($item->ID);
						$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($item->ID,'product-thumbnails',64,64);
						$product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $item->post_content) ) ) ),$text_lenght,'...');
						if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( str_replace("\n", "", $item->post_excerpt) ) ) ),$text_lenght,'...');
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.stripslashes( $item->post_title).'</span><span class="rs_description">'.$product_description.'</span></div></a></div></div>';
						$rs_items['page'] .= $item_html.'[|]'.$link_detail.'[|]'.stripslashes( $item->post_title)."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			global $wp_version;
			if ( version_compare( $wp_version, '4.0', '<' ) ) {
				$search_keyword_esc = like_escape( $search_keyword );
				$search_keyword_nospecial = like_escape( $search_keyword_nospecial );
			} else {
				$search_keyword_esc = $wpdb->esc_like( $search_keyword );
				$search_keyword_nospecial = $wpdb->esc_like( $search_keyword_nospecial );
			}
			
			if ( isset($search_in['p_cat']) && $search_in['p_cat'] > 0) {
				$row = $search_in['p_cat'];
				$end_row = $row;
				$number_items = $row+1;
				$where = "tt.taxonomy IN ('wpsc_product_category') ";
				$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
				if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
				$where .= ")";
				
				if ( trim($wpsc_predictive_id_excludes['exclude_p_categories']) != '') 
					$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_categories']).") ";
				$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
				$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT 0,$number_items";
																
				$search_cats = $wpdb->get_results($query);
				$total_pcat = count($search_cats);
				if ( $search_cats && $total_pcat > 0 ) {
					$found_items = true;
					$rs_headings['p_cat'] = "<div class='ajax_search_content_title'>".__('Product Categories', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['p_cat'] = '';
					foreach ( $search_cats as $item ) {
						$link_detail = get_term_link($item->slug, 'wpsc_product_category');
						$avatar = WPSC_Predictive_Search::get_product_cat_thumbnail($item->term_id,64,64);
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.stripslashes( $item->name).'</span><span class="rs_description">'.WPSC_Predictive_Search::wpscps_limit_words(strip_tags( str_replace("\n", "", $item->description) ),$text_lenght,'...').'</span></div></a></div></div>';
						$rs_items['p_cat'] .= $item_html.'[|]'.$link_detail.'[|]'.stripslashes( $item->name)."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			if ( isset($search_in['p_tag']) && $search_in['p_tag'] > 0) {
				$row = $search_in['p_tag'];
				$end_row = $row;
				$number_items = $row+1;
				$where = "tt.taxonomy IN ('product_tag') ";
				$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
				if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
				$where .= ")";
				
				if ( trim($wpsc_predictive_id_excludes['exclude_p_tags']) != '') 
					$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_tags']).") ";
				$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
				$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT 0,$number_items";
												
				$search_tags = $wpdb->get_results($query);
				$total_ptag = count($search_tags);	
				if ( $search_tags && $total_ptag > 0 ) {
					$found_items = true;
					$rs_headings['p_tag'] = "<div class='ajax_search_content_title'>".__('Product Tags', 'wp-e-commerce-predictive-search' )."</div>[|]#[|]$search_keyword\n";
					$rs_items['p_tag'] = '';
					foreach ( $search_tags as $item ) {
						$link_detail = get_term_link($item->slug, 'product_tag');
						$avatar = WPSC_Predictive_Search::get_product_cat_thumbnail($item->term_id,64,64);
						$item_html = '<div class="ajax_search_content"><div class="result_row"><a href="'.$link_detail.'"><span class="rs_avatar">'.$avatar.'</span><div class="rs_content_popup"><span class="rs_name">'.stripslashes( $item->name).'</span><span class="rs_description">'.WPSC_Predictive_Search::wpscps_limit_words(strip_tags( str_replace("\n", "", $item->description) ),$text_lenght,'...').'</span></div></a></div></div>';
						$rs_items['p_tag'] .= $item_html.'[|]'.$link_detail.'[|]'.stripslashes( $item->name)."\n";
						$end_row--;
						if ($end_row < 1) break;
					}
				}
			}
			
			if ($found_items === false) {
				echo '<div class="ajax_no_result">'.__('Nothing found for that name. Try a different spelling or name.', 'wp-e-commerce-predictive-search' ).'</div>';
			} else {
				$rs_footer_html = '';
				foreach ($search_in as $key => $number) {
					if ($number > 0) {
						if (isset($rs_headings[$key]))
							echo $rs_headings[$key];
						if (isset($rs_items[$key]))
							echo $rs_items[$key];
						if (isset($rs_footers[$key]))
							$rs_footer_html .= $rs_footers[$key];
					}
				}
				
				$search_other = $search_list;
				if ($total_product < 1 )  { unset($search_list['product']); unset($search_other['product']); 
				} elseif ($total_product <= $search_in['product']) { unset($search_list['product']); }
				
				if ($total_p_sku < 1) { unset($search_list['p_sku']); unset($search_other['p_sku']);
				} elseif ($total_p_sku <= $search_in['p_sku']) { unset($search_list['p_sku']); }
				
				if ($total_post < 1) { unset($search_list['post']); unset($search_other['post']);
				} elseif ($total_post <= $search_in['post']) { unset($search_list['post']); }
				
				if ($total_page < 1) { unset($search_list['page']); unset($search_other['page']);
				} elseif ($total_page <= $search_in['page']) { unset($search_list['page']); }
				
				if ($total_pcat < 1) { unset($search_list['p_cat']); unset($search_other['p_cat']);
				} elseif ($total_pcat <= $search_in['p_cat']) { unset($search_list['p_cat']); }
				
				if ($total_ptag < 1) { unset($search_list['p_tag']); unset($search_other['p_tag']);
				} elseif ($total_ptag <= $search_in['p_tag']) { unset($search_list['p_tag']); }
				
				if (count($search_list) > 0) {
					$rs_footer_html .= '<div class="more_result" rel="more_result"><span>'.__('See more search results for', 'wp-e-commerce-predictive-search' )." "."'".$search_keyword."' ".__('in', 'wp-e-commerce-predictive-search' ).":</span>";
					foreach ($search_list as $other_rs) {
						if (get_option('permalink_structure') == '')
							$search_in_parameter = '&search_in='.$other_rs;
						else
							$search_in_parameter = '/search-in/'.$other_rs;
						
						if (get_option('permalink_structure') == '')
							$link_search = get_permalink(get_option('ecommerce_search_page_id')).'&rs='. urlencode($search_keyword) .$search_in_parameter.$extra_parameter_product.$extra_parameter_post.'&search_other='.implode(",", $search_other);
						else
							$link_search = rtrim( get_permalink(get_option('ecommerce_search_page_id')), '/' ).'/keyword/'. urlencode($search_keyword) .$search_in_parameter.$extra_parameter_product.$extra_parameter_post.'/search-other/'.implode(",", $search_other);
							
						$rs_item = '<a href="'.$link_search.'">'.$items_search_default[$other_rs]['name'].' <span class="see_more_arrow"></span></a>';
						$rs_footer_html .= "$rs_item";
					}
					$rs_footer_html .= "</div>[|]#[|]$search_keyword";
				}
				echo $rs_footer_html;
			}
		}
		die();
	}
	
	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;
		 
		$option_value = get_option($option); 
		 
		if ( $option_value > 0 && get_post( $option_value ) ) 
			return;
		
		$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
		if ( $page_found ) :
			if ( ! $option_value ) 
				update_option( $option, $page_found );
			return;
		endif;
		
		$page_data = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'post_parent' 		=> $post_parent,
			'comment_status' 	=> 'closed'
		);
		$page_id = wp_insert_post( $page_data );
		
		update_option( $option, $page_id );
	}
	
	public static function get_product_variation_price_available($product_id){
		global $wpdb;
		
		$joins = array(
			"INNER JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.id AND pm.meta_key = '_wpsc_price'",
		);
	
		$selects = array(
			'pm.meta_value AS price',
		);
	
		$joins[] = "INNER JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = p.id AND pm2.meta_key = '_wpsc_special_price'";
		$selects[] = 'pm2.meta_value AS special_price';
	
		$joins = implode( ' ', $joins );
		$selects = implode( ', ', $selects );
	
		$sql = $wpdb->prepare( "
			SELECT {$selects}
			FROM {$wpdb->posts} AS p
			{$joins}
			WHERE
				p.post_type = 'wpsc-product'
				AND
				p.post_parent = %d
		", $product_id );
	
		$results = $wpdb->get_results( $sql );
		$prices = array();
	
		foreach ( $results as $row ) {
			$price = (float) $row->price;
			$special_price = (float) $row->special_price;
			if ( $special_price != 0 && $special_price < $price )
				$price = $special_price;
			$prices[] = $price;
		}
	
		sort( $prices );
		if (count($prices) > 0)
			$price = apply_filters( 'wpsc_do_convert_price', $prices[0] );
		else 
			$price = 0;
				
		return $price;
	}
	
	public static function strip_shortcodes ($content='') {
		$content = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content);
		
		return $content;
	}
	
	public static function upgrade_version_2_0() {
		$exclude_products = get_option('ecommerce_search_exclude_products', '');
		$exclude_p_categories = get_option('ecommerce_search_exclude_p_categories', '');
		$exclude_p_tags = get_option('ecommerce_search_exclude_p_tags', '');
		$exclude_posts = get_option('ecommerce_search_exclude_posts', '');
		$exclude_pages = get_option('ecommerce_search_exclude_pages', '');
		
		if ($exclude_products !== false) {
			$exclude_products_array = explode(",", $exclude_products);
			if (is_array($exclude_products_array) && count($exclude_products_array) > 0) {
				$exclude_products_array_new = array();
				foreach ($exclude_products_array as $exclude_products_item) {
					if ( trim($exclude_products_item) > 0) $exclude_products_array_new[] = trim($exclude_products_item);
				}
				$exclude_products = $exclude_products_array_new;
			} else {
				$exclude_products = array();
			}
			update_option('ecommerce_search_exclude_products', (array) $exclude_products);
		} else {
			update_option('ecommerce_search_exclude_products', array());
		}
		
		if ($exclude_posts !== false) {
			$exclude_posts_array = explode(",", $exclude_posts);
			if (is_array($exclude_posts_array) && count($exclude_posts_array) > 0) {
				$exclude_posts_array_new = array();
				foreach ($exclude_posts_array as $exclude_posts_item) {
					if ( trim($exclude_posts_item) > 0) $exclude_posts_array_new[] = trim($exclude_posts_item);
				}
				$exclude_posts = $exclude_posts_array_new;
			} else {
				$exclude_posts = array();
			}
			update_option('ecommerce_search_exclude_posts', (array) $exclude_posts);
		} else {
			update_option('ecommerce_search_exclude_posts', array());
		}
		
		if ($exclude_pages !== false) {
			$exclude_pages_array = explode(",", $exclude_pages);
			if (is_array($exclude_pages_array) && count($exclude_pages_array) > 0) {
				$exclude_pages_array_new = array();
				foreach ($exclude_pages_array as $exclude_pages_item) {
					if ( trim($exclude_pages_item) > 0) $exclude_pages_array_new[] = trim($exclude_pages_item);
				}
				$exclude_pages = $exclude_pages_array_new;
			} else {
				$exclude_pages = array();
			}
			update_option('ecommerce_search_exclude_pages', (array) $exclude_pages);
		} else {
			update_option('ecommerce_search_exclude_pages', array());
		}
		
		if ($exclude_p_categories !== false) {
			$exclude_p_categories_array = explode(",", $exclude_p_categories);
			if (is_array($exclude_p_categories_array) && count($exclude_p_categories_array) > 0) {
				$exclude_p_categories_array_new = array();
				foreach ($exclude_p_categories_array as $exclude_p_categories_item) {
					if ( trim($exclude_p_categories_item) > 0) $exclude_p_categories_array_new[] = trim($exclude_p_categories_item);
				}
				$exclude_p_categories = $exclude_p_categories_array_new;
			} else {
				$exclude_p_categories = array();
			}
			update_option('ecommerce_search_exclude_p_categories', (array) $exclude_p_categories);
		} else {
			update_option('ecommerce_search_exclude_p_categories', array());
		}
		
		if ($exclude_p_tags !== false) {
			$exclude_p_tags_array = explode(",", $exclude_p_tags);
			if (is_array($exclude_p_tags_array) && count($exclude_p_tags_array) > 0) {
				$exclude_p_tags_array_new = array();
				foreach ($exclude_p_tags_array as $exclude_p_tags_item) {
					if ( trim($exclude_p_tags_item) > 0) $exclude_p_tags_array[] = trim($exclude_p_tags_item);
				}
				$exclude_p_tags = $exclude_p_tags_array;
			} else {
				$exclude_p_tags = array();
			}
			update_option('ecommerce_search_exclude_p_tags', (array) $exclude_p_tags);
		} else {
			update_option('ecommerce_search_exclude_p_tags', array());
		}
	}
}
?>