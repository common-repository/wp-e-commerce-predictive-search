<?php
/**
 * WPSC Predictive Search Hook Filter
 *
 * Hook anf Filter into ecommerce plugin
 *
 * Table Of Contents
 *
 * wpscps_add_settings_search()
 * wpscps_add_frontend_script()
 * wpscps_add_frontend_style()
 * add_query_vars()
 * add_rewrite_rules()
 * custom_rewrite_rule()
 * search_by_title_only()
 * predictive_posts_orderby()
 * remove_where_on_focuskw_meta()
 * posts_request_unconflict_role_scoper_plugin()
 * posts_join()
 * a3_wp_admin()
 * plugin_extra_links()
 */
class WPSC_Predictive_Search_Hook_Filter
{
	
	/*
	* Include the script for widget search and Search page
	*/
	public static function wpscps_add_frontend_script() {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'ajax-wpsc-autocomplete-script', WPSC_PS_JS_URL . '/ajax-autocomplete/jquery.autocomplete.js', array(), false, true );
	}
	
	public static function wpscps_add_frontend_style() {
		wp_enqueue_style( 'ajax-wpsc-autocomplete-style', WPSC_PS_JS_URL . '/ajax-autocomplete/jquery.autocomplete.css' );
	}
	
	public static function add_query_vars($aVars) {
		$aVars[] = "keyword";    // represents the name of the product category as shown in the URL
		$aVars[] = "search-in";
		$aVars[] = "pcat";
		$aVars[] = "ptag";
		$aVars[] = "scat";
		$aVars[] = "stag";
		$aVars[] = "search-other";
		return $aVars;
	}
	
	public static function add_rewrite_rules($aRules) {
		//var_dump($_SERVER);
		$ecommerce_search_page_id = get_option('ecommerce_search_page_id');
		$search_page = get_page($ecommerce_search_page_id);
		if (!empty($search_page)) {
			$search_page_slug = $search_page->post_name;
			if (stristr($_SERVER['REQUEST_URI'], $search_page_slug) !== FALSE) {
				//$url_text = stristr($_SERVER['REQUEST_URI'], $search_page_slug);
				$position = strpos($_SERVER['REQUEST_URI'], $search_page_slug);
				$new_url = substr($_SERVER['REQUEST_URI'], ($position + strlen($search_page_slug.'/') ) );
				$parameters_array = explode("/", $new_url);
				
				if (is_array($parameters_array) && count($parameters_array) > 1) {
					$array_key = array();
					$array_value = array();
					$number = 0;
					foreach ($parameters_array as $parameter) {
						$number++;
						if (trim($parameter) == '') continue;
						if ($number%2 == 0) $array_value[] = $parameter;
						else $array_key[] = $parameter;
					}
					if (count($array_key) > 0 && count($array_value) > 0 ) {
						$rewrite_rule = '';
						$original_url = '';
						$number_matches = 0;
						foreach ($array_key as $key) {
							$number_matches++;
							$rewrite_rule .= $key.'/([^/]*)/';
							$original_url .= '&'.$key.'=$matches['.$number_matches.']';
						}
						
						//var_dump($search_page_slug.'/'.$rewrite_rule.'?$ => index.php?pagename='.$search_page_slug.$original_url);
						
						//$aNewRules = array($search_page_slug.'/rs/([^/]*)/search_in/([^/]*)/?$' => 'index.php?pagename='.$search_page_slug.'&rs=$matches[1]&search_in=$matches[2]');
						
						$aNewRules = array($search_page_slug.'/'.$rewrite_rule.'?$' => 'index.php?pagename='.$search_page_slug.$original_url);
						$aRules = $aNewRules + $aRules;
						
					}
				}
			}
		}
		return $aRules;
	} 
	
	public static function custom_rewrite_rule() {
		// BEGIN rewrite
		// hook add_query_vars function into query_vars
		add_filter('query_vars', array('WPSC_Predictive_Search_Hook_Filter', 'add_query_vars') );
	
		add_filter('rewrite_rules_array', array('WPSC_Predictive_Search_Hook_Filter', 'add_rewrite_rules') );
		
		$ecommerce_search_page_id = get_option('ecommerce_search_page_id');
		$search_page = get_page($ecommerce_search_page_id);
		if (!empty($search_page)) {
			$search_page_slug = $search_page->post_name;
			if (stristr($_SERVER['REQUEST_URI'], $search_page_slug) !== FALSE) {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
			}
		}
		// END rewrite
	}
	
	public static function remove_special_characters_in_mysql( $field_name ) {
		if ( trim( $field_name ) == '' ) return '';
		
		$field_name = 'REPLACE( '.$field_name.', "(", "")';
		$field_name = 'REPLACE( '.$field_name.', ")", "")';
		$field_name = 'REPLACE( '.$field_name.', "{", "")';
		$field_name = 'REPLACE( '.$field_name.', "}", "")';
		$field_name = 'REPLACE( '.$field_name.', "<", "")';
		$field_name = 'REPLACE( '.$field_name.', ">", "")';
		$field_name = 'REPLACE( '.$field_name.', "©", "")'; 	// copyright
		$field_name = 'REPLACE( '.$field_name.', "®", "")'; 	// registered
		$field_name = 'REPLACE( '.$field_name.', "™", "")'; 	// trademark
		$field_name = 'REPLACE( '.$field_name.', "£", "")'; 
		$field_name = 'REPLACE( '.$field_name.', "¥", "")';	
		$field_name = 'REPLACE( '.$field_name.', "§", "")';
		$field_name = 'REPLACE( '.$field_name.', "¢", "")';
		$field_name = 'REPLACE( '.$field_name.', "µ", "")';
		$field_name = 'REPLACE( '.$field_name.', "¶", "")';
		$field_name = 'REPLACE( '.$field_name.', "–", "")'; 
		$field_name = 'REPLACE( '.$field_name.', "¿", "")'; 
		$field_name = 'REPLACE( '.$field_name.', "«", "")'; 
		$field_name = 'REPLACE( '.$field_name.', "»", "")'; 
		 
	
		$field_name = 'REPLACE( '.$field_name.', "&lsquo;", "")'; 	// left single curly quote
		$field_name = 'REPLACE( '.$field_name.', "&rsquo;", "")'; 	// right single curly quote
		$field_name = 'REPLACE( '.$field_name.', "&ldquo;", "")'; 	// left double curly quote
		$field_name = 'REPLACE( '.$field_name.', "&rdquo;", "")'; 	// right double curly quote
		$field_name = 'REPLACE( '.$field_name.', "&quot;", "")'; 	// quotation mark
		$field_name = 'REPLACE( '.$field_name.', "&ndash;", "")'; 	// en dash
		$field_name = 'REPLACE( '.$field_name.', "&mdash;", "")'; 	// em dash
		$field_name = 'REPLACE( '.$field_name.', "&iexcl;", "")'; 	// inverted exclamation
		$field_name = 'REPLACE( '.$field_name.', "&iquest;", "")'; 	// inverted question mark
		$field_name = 'REPLACE( '.$field_name.', "&laquo;", "")'; 	// guillemets
		$field_name = 'REPLACE( '.$field_name.', "&raquo;", "")'; 	// guillemets
		$field_name = 'REPLACE( '.$field_name.', "&gt;", "")'; 		// greater than
		$field_name = 'REPLACE( '.$field_name.', "&lt;", "")'; 		// less than
		
		return $field_name;
	}
	
	public static function search_by_title_only( $search, &$wp_query ) {
		global $wpdb;
		global $wp_version;
		$q = $wp_query->query_vars;
		if ( empty( $search) || !isset($q['s']))
			return $search; // skip processing - no search term in query
		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$term = esc_sql( like_escape( trim( $q['s'] ) ) );
		} else {
			$term = esc_sql( $wpdb->esc_like( trim( $q['s'] ) ) );
		}
		$term_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $term );
		$search_nospecial = false;
		if ( $term != $term_nospecial ) $search_nospecial = true;
		
		$meta_query = array();
		if (isset( $q['meta_query'] ) )
			$meta_query = (array) $q['meta_query'];
			
		//if ($term != '') { var_dump($meta_query); }
		
		$predictive_search_meta = true;
		$where = array();

		$key_only_queries = array();
		$queries = array();
		
		if (is_array($meta_query) && count($meta_query) > 0) {
			$predictive_search_meta = false;
			
			$meta_table = $wpdb->postmeta;
			$relation = $meta_query['relation'];
			if ($relation != 'AND') $relation = 'OR';
			unset($meta_query['relation']);
			
			foreach ($meta_query as $k => $q) {
				if ( $q['key'] == '_predictive_search_focuskw' ) {
					$predictive_search_meta = true;
				}
				
				$meta_key = isset( $q['key'] ) ? trim( $q['key'] ) : '';
				$meta_type = isset( $q['type'] ) ? strtoupper( $q['type'] ) : 'CHAR';
	
				if ( 'NUMERIC' == $meta_type )
					$meta_type = 'SIGNED';
				elseif ( ! in_array( $meta_type, array( 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' ) ) )
					$meta_type = 'CHAR';
	
				$meta_value = isset( $q['value'] ) ? $q['value'] : null;
	
				if ( isset( $q['compare'] ) )
					$meta_compare = strtoupper( $q['compare'] );
				else
					$meta_compare = is_array( $meta_value ) ? 'IN' : '=';
	
				if ( ! in_array( $meta_compare, array(
					'=', '!=', '>', '>=', '<', '<=',
					'LIKE', 'NOT LIKE',
					'IN', 'NOT IN',
					'BETWEEN', 'NOT BETWEEN',
					'NOT EXISTS'
				) ) )
					$meta_compare = '=';
	
				$alias = $meta_table;
	
				if ( 'NOT EXISTS' == $meta_compare ) {
	
					$where[$k] = ' ' . $alias . '.' . $meta_id_column . ' IS NULL';
	
					continue;
				}
	
	
				$where[$k] = '';
				if ( !empty( $meta_key ) )
					$where[$k] = $wpdb->prepare( "$alias.meta_key = %s", $meta_key );
	
				if ( is_null( $meta_value ) ) {
					continue;
				}
	
				if ( in_array( $meta_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
					if ( ! is_array( $meta_value ) )
						$meta_value = preg_split( '/[,\s]+/', $meta_value );
	
					if ( empty( $meta_value ) ) {
						continue;
					}
				} else {
					$meta_value = trim( $meta_value );
				}
	
				if ( ! empty( $where[$k] ) )
					$where[$k] .= ' AND ';
					
				$meta_value_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $meta_value );
				$search_meta_value_nospecial = false;
				if ( $meta_value != $meta_value_nospecial ) $search_meta_value_nospecial = true;
				
				$where_meta_query = '( ';
				if ( version_compare( $wp_version, '4.0', '<' ) ) {
					$meta_value = like_escape( $meta_value );
					$meta_value_nospecial = like_escape( $meta_value_nospecial );
				} else {
					$meta_value = $wpdb->esc_like( $meta_value );
					$meta_value_nospecial = $wpdb->esc_like( $meta_value_nospecial );
				}
				$where_meta_query .= $wpdb->prepare( WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "CAST($alias.meta_value AS {$meta_type})" )." LIKE '%s' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "CAST($alias.meta_value AS {$meta_type})" )." LIKE '%s' ", $meta_value.'%', '% '.$meta_value.'%' );
				if ( $search_meta_value_nospecial ) $where_meta_query .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "CAST($alias.meta_value AS {$meta_type})" )." LIKE '%s' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "CAST($alias.meta_value AS {$meta_type})" )." LIKE '%s' ", $meta_value_nospecial.'%', '% '.$meta_value_nospecial.'%' );
				$where_meta_query .= ' ) ';
				
				$where[$k] = ' (' . $where[$k] . $where_meta_query . ' ) ';
			}
		}
		
		if ($predictive_search_meta) {
			$where = array_filter( $where );

			if ( empty( $where ) )
				$where = '';
			else
				$where = ' OR (' . implode( "\n{$relation} ", $where ) . ' )';
				
			$search = '';
			if ( isset($q['post_type']) && is_array($q['post_type']) && in_array('wpsc-product', $q['post_type']) && isset($q['meta_key']) && trim($q['meta_key']) == '_wpsc_sku' ) {
				$search .= "( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '{$term}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '% {$term}%')";
				if ( $search_nospecial ) $search .= " OR ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '{$term_nospecial}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '% {$term_nospecial}%')";
			} elseif ( isset($q['post_type']) && !is_array($q['post_type']) && trim($q['post_type']) == 'wpsc-product' && isset($q['meta_key']) && trim($q['meta_key']) == '_wpsc_sku' ) {
				$search .= "( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '{$term}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '% {$term}%')";
				if ( $search_nospecial ) $search .= " OR ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '{$term_nospecial}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->postmeta.meta_value" )." LIKE '% {$term_nospecial}%')";
			} else {
				$search .= "( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->posts.post_title" )." LIKE '{$term}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->posts.post_title" )." LIKE '% {$term}%')";
				if ( $search_nospecial ) $search .= " OR ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->posts.post_title" )." LIKE '{$term_nospecial}%' OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "$wpdb->posts.post_title" )." LIKE '% {$term_nospecial}%')";
			}
			
			if ( ! empty( $search ) ) {
				$search = " AND ({$search} {$where}) ";
			}
		}
		
		return $search;
	}
	
	public static function predictive_posts_orderby( $orderby, &$wp_query ) {
		global $wpdb;
		global $wp_version;
		$q = $wp_query->query_vars;
		if (isset($q['orderby']) && $q['orderby'] == 'predictive' && isset($q['s']) ) {
			if ( version_compare( $wp_version, '4.0', '<' ) ) {
				$term = esc_sql( like_escape( trim( $q['s'] ) ) );
			} else {
				$term = esc_sql( $wpdb->esc_like( trim( $q['s'] ) ) );
			}
			if ( isset($q['post_type']) && is_array($q['post_type']) && in_array('wpsc-product', $q['post_type']) && isset($q['meta_key']) && trim($q['meta_key']) == '_wpsc_sku' )
				$orderby = "$wpdb->postmeta.meta_value NOT LIKE '{$term}%' ASC, $wpdb->postmeta.meta_value ASC";
			elseif ( isset($q['post_type']) && !is_array($q['post_type']) && trim($q['post_type']) == 'wpsc-product' && isset($q['meta_key']) && trim($q['meta_key']) == '_wpsc_sku' ) 
				$orderby = "$wpdb->postmeta.meta_value NOT LIKE '{$term}%' ASC, $wpdb->postmeta.meta_value ASC";
			else
				$orderby = "$wpdb->posts.post_title NOT LIKE '{$term}%' ASC, $wpdb->posts.post_title ASC";
		}
		
		return $orderby;
	}
	
	public static function remove_where_on_focuskw_meta($clauses, $queries, $type, $primary_table, $primary_id_column, $context) {
		$predictive_search_meta = false;
		if ( is_array( $queries) && count($queries) > 0) {
			foreach ($queries as $meta_data) {
				if ( isset($meta_data['key']) && $meta_data['key'] == '_predictive_search_focuskw' ) {
					$predictive_search_meta = true;
					break;
				}
			}
		}
		
		if ($predictive_search_meta) {
			if ( ! $meta_table = _get_meta_table( $type ) ) return $clauses;
			$meta_id_column = esc_sql( $type . '_id' );
			$clauses['where'] = '';
			$clauses['join'] = "LEFT JOIN $meta_table ON $primary_table.$primary_id_column = $meta_table.$meta_id_column";
		}
		return $clauses;
	}
	
	public static function posts_request_unconflict_role_scoper_plugin( $posts_request, &$wp_query ) {
		$posts_request = str_replace('1=2', '2=2', $posts_request);
		
		return $posts_request;
	}
	
	public static function posts_join( $join, &$wp_query ) {
		global $wpdb;
		global $wp_version;
		$q = $wp_query->query_vars;
		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$term = esc_sql( like_escape( trim( $q['s'] ) ) );
		} else {
			$term = esc_sql( $wpdb->esc_like( trim( $q['s'] ) ) );
		}
		if ($term != '') {
			
		}
		
		return $join;
	}
	
	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WPSC_PS_CSS_URL . '/a3_wp_admin.css' );
	}
		
	public static function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != WPSC_PS_NAME) {
			return $links;
		}
		$links[] = '<a href="'.WPSC_PREDICTIVE_SEARCH_DOCS_URI.'" target="_blank">'.__('Documentation', 'wp-e-commerce-predictive-search' ).'</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/wp-e-commerce-predictive-search/" target="_blank">'.__('Support', 'wp-e-commerce-predictive-search' ).'</a>';
		return $links;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="edit.php?post_type=wpsc-product&page=wpsc-predictive-search">' . __( 'Settings', 'wp-e-commerce-predictive-search' ) . '</a>' ), $actions );

		return $actions;
	}
}
?>