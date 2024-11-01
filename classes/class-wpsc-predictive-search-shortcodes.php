<?php
/**
 * WPSC Predictive Search Hook Filter
 *
 * Hook anf Filter into ecommerce plugin
 *
 * Table Of Contents
 *
 * parse_shortcode_search_widget()
 * add_search_widget_icon()
 * add_search_widget_mce_popup()
 * parse_shortcode_search_result()
 * get_product_sku()
 * get_product_price()
 * get_product_price_dropdown()
 * get_product_addtocart()
 * get_product_categories()
 * get_post_categories()
 * get_product_tags()
 * get_post_tags()
 * display_search()
 * get_result_search_page()
 * get_product_items()
 * get_product_sku_items()
 * get_post_items()
 * get_page_items()
 * get_pcategory_items()
 * get_ptag_items()
 */
class WPSC_Predictive_Search_Shortcodes 
{
	public static function parse_shortcode_search_widget($attributes) {
		$items_search_default = WPSC_Predictive_Search_Widgets::get_items_search();
		$items_array = array();
		
		extract(array_merge(array(
			 'show_price' => 1,
			 'character_max' => 100,
			 'style' => '',
			 'wrap'	=> 'false',
			 'search_box_text' => '',
        ), $attributes));
		
		foreach ($items_search_default as $key => $data) {
			if (isset(${$key.'_items'}) )
				$items_array[$key] = ${$key.'_items'};
			else 
				$items_array[$key] = $data['number'];
		}
		
		$widget_id = rand(100, 10000);
		
		$break_div = '<div style="clear:both;"></div>';
		if ($wrap == 'true') $break_div = '';
		
		if ( trim($search_box_text) == '' ) $search_box_text = get_option('ecommerce_search_box_text');
		
		return WPSC_Predictive_Search_Widgets::wpscps_results_search_form($widget_id, $items_array, $character_max, $style, 1, $search_box_text, $show_price).$break_div;
	}
	
	public static function add_search_widget_icon($context){
		$image_btn = WPSC_PS_IMAGES_URL . "/ps_icon.png";
		$out = '<a href="#TB_inline?width=670&height=500&modal=false&inlineId=search_widget_shortcode" class="thickbox" title="'.__('Insert WP e-Commerce Predictive Search Shortcode', 'wp-e-commerce-predictive-search' ).'"><img class="search_widget_shortcode_icon" src="'.$image_btn.'" alt="'.__('Insert WP e-Commerce Predictive Search Shortcode', 'wp-e-commerce-predictive-search' ).'" /></a>';
		return $context . $out;
	}
	
	//Action target that displays the popup to insert a form to a post/page
	public static function add_search_widget_mce_popup(){
		$items_search_default = WPSC_Predictive_Search_Widgets::get_items_search();
		?>
		<script type="text/javascript">
			function wpsc_search_widget_add_shortcode(){
				var number_items = '';
				<?php foreach ($items_search_default as $key => $data) {?>
				var wpsc_search_<?php echo $key ?>_items = '<?php echo $key ?>_items="' + jQuery("#wpsc_search_<?php echo $key ?>_items").val() + '" ';
				number_items += wpsc_search_<?php echo $key ?>_items;
				<?php } ?>
				var wpsc_search_show_price = 0;
				if ( jQuery('#wpsc_search_show_price').is(":checked") ) {
					var wpsc_search_show_price = 1;
				}
				var wpsc_search_text_lenght = jQuery("#wpsc_search_text_lenght").val();
				var wpsc_search_align = jQuery("#wpsc_search_align").val();
				var wpsc_search_width = jQuery("#wpsc_search_width").val();
				var wpsc_search_padding_top = jQuery("#wpsc_search_padding_top").val();
				var wpsc_search_padding_bottom = jQuery("#wpsc_search_padding_bottom").val();
				var wpsc_search_padding_left = jQuery("#wpsc_search_padding_left").val();
				var wpsc_search_padding_right = jQuery("#wpsc_search_padding_right").val();
				var wpsc_search_box_text = jQuery("#wpsc_search_box_text").val();
				var wpsc_search_style = '';
				var wrap = '';
				if (wpsc_search_align == 'center') wpsc_search_style += 'float:none;margin:auto;display:table;';
				else if (wpsc_search_align == 'left-wrap') wpsc_search_style += 'float:left;';
				else if (wpsc_search_align == 'right-wrap') wpsc_search_style += 'float:right;';
				else wpsc_search_style += 'float:'+wpsc_search_align+';';
				
				if(wpsc_search_align == 'left-wrap' || wpsc_search_align == 'right-wrap') wrap = 'wrap="true"';
				
				if (parseInt(wpsc_search_width) > 0) wpsc_search_style += 'width:'+parseInt(wpsc_search_width)+'px;';
				if (parseInt(wpsc_search_padding_top) >= 0) wpsc_search_style += 'padding-top:'+parseInt(wpsc_search_padding_top)+'px;';
				if (parseInt(wpsc_search_padding_bottom) >= 0) wpsc_search_style += 'padding-bottom:'+parseInt(wpsc_search_padding_bottom)+'px;';
				if (parseInt(wpsc_search_padding_left) >= 0) wpsc_search_style += 'padding-left:'+parseInt(wpsc_search_padding_left)+'px;';
				if (parseInt(wpsc_search_padding_right) >= 0) wpsc_search_style += 'padding-right:'+parseInt(wpsc_search_padding_right)+'px;';
				var win = window.dialogArguments || opener || parent || top;
				win.send_to_editor('[ecommerce_search_widget ' + number_items + ' show_price="'+wpsc_search_show_price+'" character_max="'+wpsc_search_text_lenght+'" style="'+wpsc_search_style+'" '+wrap+ ' search_box_text="'+wpsc_search_box_text+'"]');
			}
			
			
		</script>
		<style type="text/css">
		#TB_ajaxContent{width:auto !important;}
		#TB_ajaxContent p {
			padding:2px 0;	
			margin:6px 0;
		}
		.field_content {
			padding:0 0 0 40px;
		}
		.field_content label{
			width:150px;
			float:left;
			text-align:left;
		}
		.a3-view-docs-button {
			background-color: #FFFFE0 !important;
			border: 1px solid #E6DB55 !important;
			border-radius: 3px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			color: #21759B !important;
			outline: 0 none;
			text-shadow:none !important;
			font-weight:normal !important;
			font-family: sans-serif;
			font-size: 12px;
			text-decoration: none;
			padding: 3px 8px;
			position: relative;
			margin-left: 4px;
			white-space:nowrap;
		}
		.a3-view-docs-button:hover {
			color: #D54E21 !important;
		}
		@media screen and ( max-width: 782px ) {
			#wpsc_search_box_text {
				width:100% !important;	
			}
		}
		@media screen and ( max-width: 480px ) {
			.a3_ecommerce_search_exclude_item {
				float:none !important;
				display:block;
			}
		}
		</style>
		<div id="search_widget_shortcode" style="display:none;">
		  <div>
			<h3><?php _e('Customize the Predictive Search Shortcode', 'wp-e-commerce-predictive-search' ); ?> <a class="add-new-h2 a3-view-docs-button" target="_blank" href="<?php echo WPSC_PREDICTIVE_SEARCH_DOCS_URI; ?>#section-16" ><?php _e('View Docs', 'wp-e-commerce-predictive-search' ); ?></a></h3>
			<div style="clear:both"></div>
			<div class="field_content">
            	<?php foreach ($items_search_default as $key => $data) { ?>
                <p><label for="wpsc_search_<?php echo $key ?>_items"><?php echo $data['name']; ?>:</label> <input style="width:100px;" size="10" id="wpsc_search_<?php echo $key ?>_items" name="wpsc_search_<?php echo $key ?>_items" type="text" value="<?php echo $data['number'] ?>" /> <span class="description"><?php _e('Number of', 'wp-e-commerce-predictive-search' ); echo ' '.$data['name'].' '; _e('results to show in dropdown', 'wp-e-commerce-predictive-search' ); ?></span></p> 
                <?php } ?>
                <p><label for="wpsc_search_show_price"><?php _e('Price', 'wp-e-commerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="wpsc_search_show_price" name="wpsc_search_show_price" value="1" /> <span class="description"><?php _e('Show Product prices', 'wp-e-commerce-predictive-search' ); ?></span></p>
            	<p><label for="wpsc_search_text_lenght"><?php _e('Characters', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="wpsc_search_text_lenght" name="wpsc_search_text_lenght" type="text" value="100" /> <span class="description"><?php _e('Number of product description characters', 'wp-e-commerce-predictive-search' ); ?></span></p>
                <p><label for="wpsc_search_align"><?php _e('Alignment', 'wp-e-commerce-predictive-search' ); ?>:</label> <select style="width:100px" id="wpsc_search_align" name="wpsc_search_align"><option value="none" selected="selected"><?php _e('None', 'wp-e-commerce-predictive-search' ); ?></option><option value="left-wrap"><?php _e('Left - wrap', 'wp-e-commerce-predictive-search' ); ?></option><option value="left"><?php _e('Left - no wrap', 'wp-e-commerce-predictive-search' ); ?></option><option value="center"><?php _e('Center', 'wp-e-commerce-predictive-search' ); ?></option><option value="right-wrap"><?php _e('Right - wrap', 'wp-e-commerce-predictive-search' ); ?></option><option value="right"><?php _e('Right - no wrap', 'wp-e-commerce-predictive-search' ); ?></option></select> <span class="description"><?php _e('Horizontal aliginment of search box', 'wp-e-commerce-predictive-search' ); ?></span></p>
                <p><label for="wpsc_search_width"><?php _e('Search box width', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="wpsc_search_width" name="wpsc_search_width" type="text" value="200" />px</p>
                <p><label for="wpsc_search_box_text"><?php _e('Search box text message', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:300px;" size="10" id="wpsc_search_box_text" name="wpsc_search_box_text" type="text" value="<?php echo get_option('ecommerce_search_box_text'); ?>" /></p>
                <p><label for="wpsc_search_padding"><strong><?php _e('Padding', 'wp-e-commerce-predictive-search' ); ?></strong>:</label><br /> 
				<label for="wpsc_search_padding_top" style="width:auto; float:none"><?php _e('Above', 'wp-e-commerce-predictive-search' ); ?>:</label><input style="width:50px;" size="10" id="wpsc_search_padding_top" name="wpsc_search_padding_top" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="wpsc_search_padding_bottom" style="width:auto; float:none"><?php _e('Below', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="wpsc_search_padding_bottom" name="wpsc_search_padding_bottom" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="wpsc_search_padding_left" style="width:auto; float:none"><?php _e('Left', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="wpsc_search_padding_left" name="wpsc_search_padding_left" type="text" value="0" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="wpsc_search_padding_right" style="width:auto; float:none"><?php _e('Right', 'wp-e-commerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="wpsc_search_padding_right" name="wpsc_search_padding_right" type="text" value="0" />px
                </p>
			</div>
            <p><input type="button" class="button-primary" value="<?php _e('Insert Shortcode', 'wp-e-commerce-predictive-search' ); ?>" onclick="wpsc_search_widget_add_shortcode();"/>&nbsp;&nbsp;&nbsp;
            <a class="button" style="" href="#" onclick="tb_remove(); return false;"><?php _e('Cancel', 'wp-e-commerce-predictive-search' ); ?></a>
			</p>
		  </div>
		</div>
<?php
	}
	
	public static function parse_shortcode_search_result($attributes) {
		$search_results = '';
		global $ecommerce_search_page_id;
		global $wp_query;
		
		$ecommerce_search_enable_google_analytic = get_option( 'ecommerce_search_enable_google_analytic', 'no' ); 
		$ecommerce_search_google_analytic_id = trim( get_option( 'ecommerce_search_google_analytic_id', '' ) ); 
		$ecommerce_search_google_analytic_query_parameter = trim( get_option( 'ecommerce_search_google_analytic_query_parameter', 'ps' ) ); 
		
		$search_keyword = '';
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = stripslashes( strip_tags( urldecode( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['rs'] ) );
		
		$search_result_google_tracking = '';
		if ( $ecommerce_search_enable_google_analytic == 'yes' && $ecommerce_search_google_analytic_id != '' ) {
			ob_start();
	?>
		<!-- Google Analytics -->
		<script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '<?php echo $ecommerce_search_google_analytic_id; ?>', 'auto');
        ga('send', 'pageview', {
		  'page': '<?php echo add_query_arg( array( $ecommerce_search_google_analytic_query_parameter => $search_keyword ) , get_permalink( $ecommerce_search_page_id ) ); ?>',
		  'title': '<?php echo get_the_title( $ecommerce_search_page_id ); ?>'
		});
        </script>
        <!-- End Google Analytics -->
    <?php
			$search_result_google_tracking = ob_get_clean();
		}
		
		$search_results .= $search_result_google_tracking;
		$search_results .= WPSC_Predictive_Search_Shortcodes::display_search();
    	return $search_results;
    }
	
	public static function get_product_sku($product_id, $show_sku=true) {
		$product_sku_output = '';
		if ($show_sku) {
			$product_sku = esc_attr( stripslashes( get_post_meta($product_id, '_wpsc_sku', true) ) );
			$product_sku_output = '<div class="rs_rs_sku">'. $product_sku. '</div>';
		}
		
		return $product_sku_output;
	}
	
	public static function get_product_price($product_id, $show_price=true) {
		$product_price_output = '';
		if ($show_price) {
			$variable_price = WPSC_Predictive_Search::get_product_variation_price_available($product_id);
			if ($variable_price > 0) {
				$variable_price = apply_filters( 'wpsc_do_convert_price', $variable_price );
				$args = array(
						'display_as_html' => false,
						'display_decimal_point' => true
				);
				$product_price_output = '<div class="rs_rs_price">'.__('Price from', 'wp-e-commerce-predictive-search' ).': ';
				$product_price_output .= '<span class="currentprice pricedisplay">'.wpsc_currency_display( $variable_price, $args ).'</span></div>';
			} else {
				$price = $full_price = get_post_meta( $product_id, '_wpsc_price', true );
		
				$special_price = get_post_meta( $product_id, '_wpsc_special_price', true );
			
				if ( ( $full_price > $special_price ) && ( $special_price > 0 ) )
					$price = $special_price;
			
				$price = apply_filters( 'wpsc_do_convert_price', $price );
				$full_price = apply_filters( 'wpsc_do_convert_price', $full_price );
				$args = array(
						'display_as_html' => false,
						'display_decimal_point' => true
				);
				if($price > 0){
					$product_price_output = '<div class="rs_rs_price">'.__('Price', 'wp-e-commerce-predictive-search' ).': ';
					if ( ( $full_price > $special_price ) && ( $special_price > 0 ) )
						$product_price_output .= '<span class="oldprice">'.wpsc_currency_display( $full_price, $args ).'</span> ';
					$product_price_output .= '<span class="currentprice pricedisplay">'.wpsc_currency_display( $price, $args ).'</span></div>';
				}
			}
		}
		
		return $product_price_output;
	}
	
	public static function get_product_price_dropdown($product_id) {
		$product_price_output = '';
			$variable_price = WPSC_Predictive_Search::get_product_variation_price_available($product_id);
			if ($variable_price > 0) {
				$variable_price = apply_filters( 'wpsc_do_convert_price', $variable_price );
				$args = array(
						'display_as_html' => false,
						'display_decimal_point' => true
				);
				$product_price_output = '<span class="rs_price">'.__('Price from', 'wp-e-commerce-predictive-search' ).': ';
				$product_price_output .= '<span class="currentprice pricedisplay">'.wpsc_currency_display( $variable_price, $args ).'</span></span>';
			} else {
				$price = $full_price = get_post_meta( $product_id, '_wpsc_price', true );
		
				$special_price = get_post_meta( $product_id, '_wpsc_special_price', true );
			
				if ( ( $full_price > $special_price ) && ( $special_price > 0 ) )
					$price = $special_price;
			
				$price = apply_filters( 'wpsc_do_convert_price', $price );
				$full_price = apply_filters( 'wpsc_do_convert_price', $full_price );
				$args = array(
						'display_as_html' => false,
						'display_decimal_point' => true
				);
				if($price > 0){
					$product_price_output = '<span class="rs_price">'.__('Price', 'wp-e-commerce-predictive-search' ).': ';
					if ( ( $full_price > $special_price ) && ( $special_price > 0 ) )
						$product_price_output .= '<span class="oldprice">'.wpsc_currency_display( $full_price, $args ).'</span> ';
					$product_price_output .= '<span class="currentprice pricedisplay">'.wpsc_currency_display( $price, $args ).'</span></span>';
				}
			}
		
		return $product_price_output;
	}
	
	public static function get_product_addtocart($product_id, $show_addtocart=true) {
		$product_addtocart_output = '';
		if ($show_addtocart) {
			ob_start();
			//include('class-wpsc-add-to-cart.php');
			$product_addtocart_html = ob_get_clean();
			$product_addtocart_output = '<div class="rs_rs_addtocart">'. $product_addtocart_html. '</div>';
		}
		
		return $product_addtocart_output;
	}
	
	public static function get_product_categories($product_id, $show_categories=true) {
		$product_cats_output = '';
		if ($show_categories) {
			$product_cats = wp_get_object_terms( $product_id, 'wpsc_product_category' );
						
			if ( $product_cats && ! is_wp_error( $product_cats ) ) {
				$product_cat_links = array();
				foreach ( $product_cats as $product_cat ) {
					$product_cat_links[] = '<a href="' .get_term_link($product_cat->slug, 'wpsc_product_category') .'">'.$product_cat->name.'</a>';
				}
				if (count($product_cat_links) > 0)
					$product_cats_output = '<div class="rs_rs_cat posted_in">'.__('Category', 'wp-e-commerce-predictive-search' ).': '.join( ", ", $product_cat_links ).'</div>';
			}
		}
		
		return $product_cats_output;
	}
	
	public static function get_post_categories($id, $show_categories=true) {
		$cats_output = '';
		if ($show_categories) {
			
			$categories = get_the_category( $id );
						
			if ( $categories && ! is_wp_error( $categories ) ) {
				$cat_links = array();
				foreach ( $categories as $category ) {
					$cat_links[] = '<a href="' .get_category_link($category->term_id ) .'">'.$category->name.'</a>';
				}
				if (count($cat_links) > 0)
					$cats_output = '<div class="rs_rs_cat posted_in">'.__('Category', 'wp-e-commerce-predictive-search' ).': '.join( ", ", $cat_links ).'</div>';
			}
		}
		
		return $cats_output;
	}
	
	public static function get_product_tags($product_id, $show_tags=true) {
		$product_tags_output = '';
		if ($show_tags) {
			$product_tags = get_the_terms( $product_id, 'product_tag' );
						
			if ( $product_tags && ! is_wp_error( $product_tags ) ) {
				$product_tag_links = array();
				foreach ( $product_tags as $product_tag ) {
					$product_tag_links[] = '<a href="' .get_term_link($product_tag->slug, 'product_tag') .'">'.$product_tag->name.'</a>';
				}
				if (count($product_tag_links) > 0)
					$product_tags_output = '<div class="rs_rs_tag tagged_as">'.__('Tags', 'wp-e-commerce-predictive-search' ).': '.join( ", ", $product_tag_links ).'</div>';
			}
		}
		
		return $product_tags_output;
	}
	
	public static function get_post_tags($id, $show_tags=true) {
		$tags_output = '';
		if ($show_tags) {
			$post_tags = get_the_tags( $id );
						
			if ( $post_tags && ! is_wp_error( $post_tags ) ) {
				$tag_links = array();
				foreach ( $post_tags as $tag ) {
					$tag_links[] = '<a href="' .get_tag_link($tag->term_id) .'">'.$tag->name.'</a>';
				}
				if (count($tag_links) > 0)
					$tags_output = '<div class="rs_rs_tag tagged_as">'.__('Tags', 'wp-e-commerce-predictive-search' ).': '.join( ", ", $tag_links ).'</div>';
			}
		}
		
		return $tags_output;
	}
	
	public static function display_search() {
		global $wp_query;
		global $wpdb;
		global $wp_version;
		global $wpsc_predictive_id_excludes;
		$items_search_default = WPSC_Predictive_Search_Widgets::get_items_search();
		$p = 0;
		$row = 10;
		$search_keyword = '';
		$search_in = 'product';
		$search_other = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$extra_parameter_product = '';
		$extra_parameter_product_admin = '';
		$extra_parameter_post = '';
		$extra_parameter_post_admin = '';
		if ( get_option('ecommerce_search_result_items') > 0  ) $row = get_option('ecommerce_search_result_items');
		
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = stripslashes( strip_tags( urldecode( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['rs'] ) );
		
		if (isset($wp_query->query_vars['pcat'])) $pcat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['pcat'] ) ) );
		else if (isset($_REQUEST['pcat']) && trim($_REQUEST['pcat']) != '') $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		
		if (isset($wp_query->query_vars['ptag'])) $ptag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['ptag'] ) ) );
		else if (isset($_REQUEST['ptag']) && trim($_REQUEST['ptag']) != '') $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		
		if (isset($wp_query->query_vars['scat'])) $cat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['scat'] ) ) );
		else if (isset($_REQUEST['scat']) && trim($_REQUEST['scat']) != '') $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		
		if (isset($wp_query->query_vars['stag'])) $tag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['stag'] ) ) );
		else if (isset($_REQUEST['stag']) && trim($_REQUEST['stag']) != '') $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		
		if (isset($wp_query->query_vars['search-in'])) $search_in = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-in'] ) ) );
		else if (isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = stripslashes( strip_tags( $_REQUEST['search_in'] ) );
		
		if (isset($wp_query->query_vars['search-other'])) $search_other = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-other'] ) ) );
		else if (isset($_REQUEST['search_other']) && trim($_REQUEST['search_other']) != '') $search_other = stripslashes( strip_tags( $_REQUEST['search_other'] ) );
		
		$args_product = array();
		if ($pcat_slug != '') {
			$args_product['tax_query'] = array( array('taxonomy' => 'wpsc_product_category', 'field' => 'slug', 'terms' => $pcat_slug) );
			$extra_parameter_product_admin .= '&pcat='.$pcat_slug;
			if (get_option('permalink_structure') == '') 
				$extra_parameter_product .= '&pcat='.$pcat_slug;
			else
				$extra_parameter_product .= '/pcat/'.$pcat_slug;
		} elseif($ptag_slug != '') {
			$args_product['tax_query'] = array( array('taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug) );
			$extra_parameter_product_admin .= '&ptag='.$ptag_slug;
			if (get_option('permalink_structure') == '')
				$extra_parameter_product .= '&ptag='.$ptag_slug;
			else
				$extra_parameter_product .= '/ptag/'.$ptag_slug;
		}
		
		$args_post = array();
		if ($cat_slug != '') {
			$args_post['category_name'] = $cat_slug;
			$extra_parameter_post_admin .= '&scat='.$cat_slug;
			if (get_option('permalink_structure') == '') 
				$extra_parameter_post .= '&scat='.$cat_slug;
			else
				$extra_parameter_post .= '/scat/'.$cat_slug;
		} elseif($tag_slug != '') {
			$args_post['tag'] = $tag_slug;
			$extra_parameter_post_admin .= '&stag='.$tag_slug;
			if (get_option('permalink_structure') == '') 
				$extra_parameter_post .= '&stag='.$tag_slug;
			else
				$extra_parameter_post .= '/stag/'.$tag_slug;
		}
		
		$start = $p * $row;
		$end_row = $row;
		$total_items = 0;
		
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
		if ($search_keyword != '' && $search_in != '') {
			$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
			$search_nospecial = false;
			if ( $search_keyword != $search_keyword_nospecial ) $search_nospecial = true;
			
			$wpscps_get_result_search_page = wp_create_nonce("wpscps-get-result-search-page");
			
			$search_other_list = $search_other_list_original = explode(",", $search_other);
			if ( is_array($search_other_list) && count($search_other_list) > 0 ) {
				$key_remove = array_search($search_in, $search_other_list );
				if ($key_remove !== false)
					unset($search_other_list[$key_remove]);
			} else {
				$search_other_list = array();
				$search_other_list_original = array();
			}
			
			$search_other_combine = array_merge( (array)$search_in, $search_other_list);
			
			$current_results_html = '';
			$have_current_result = false;
			
			if ( version_compare( $wp_version, '4.0', '<' ) ) {
				$search_keyword_esc = like_escape( $search_keyword );
				$search_keyword_nospecial = like_escape( $search_keyword_nospecial );
			} else {
				$search_keyword_esc = $wpdb->esc_like( $search_keyword );
				$search_keyword_nospecial = $wpdb->esc_like( $search_keyword_nospecial );
			}
			
			if ( count($search_other_combine) > 0 ) {
				foreach ($search_other_combine as $search_other_item) {
					
					if ( $search_other_item == 'product') {
						$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
						
						$args = array_merge($args, $args_product);
						
						if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
						
						$search_products = get_posts($args);
									
						if ( $search_products && count($search_products) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_products);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_product_items($search_products, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
					elseif ( $search_other_item == 'p_sku') {
						$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'meta_key' => '_wpsc_sku', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
						
						$args = array_merge($args, $args_product);
						
						$search_products = get_posts($args);
									
						if ( $search_products && count($search_products) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_products);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_product_sku_items($search_products, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
					elseif ( $search_other_item == 'post') {
						$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'post', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_posts'], 'suppress_filters' => FALSE);
						
						$args = array_merge($args, $args_post);
						
						if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
																
						$search_posts = get_posts($args);
									
						if ( $search_posts && count($search_posts) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_posts);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_post_items($search_posts, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
					elseif ( $search_other_item == 'page') {
						$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'page', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_pages'], 'suppress_filters' => FALSE);
						
						if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
										
						$search_pages = get_posts($args);
									
						if ( $search_pages && count($search_pages) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_pages);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_page_items($search_pages, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
					elseif ( $search_other_item == 'p_cat') {
						$number_items = $row+1;
						
						$where = "tt.taxonomy IN ('wpsc_product_category') ";
						$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
						if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
						$where .= ")";
				
						if ( trim($wpsc_predictive_id_excludes['exclude_p_categories']) != '') 
							$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_categories']).") ";
						$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
						$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT $start,$number_items";
						
						$search_cats = $wpdb->get_results($query);
									
						if ( $search_cats && count($search_cats) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_cats);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_pcategory_items($search_cats, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
					elseif ( $search_other_item == 'p_tag') {
						$number_items = $row+1;
						
						$where = "tt.taxonomy IN ('product_tag') ";
						$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
						if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
						$where .= ")";
						
						if ( trim($wpsc_predictive_id_excludes['exclude_p_tags']) != '') 
							$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_tags']).") ";
						$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
						$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT $start,$number_items";
						
						$search_tags = $wpdb->get_results($query);
									
						if ( $search_tags && count($search_tags) > 0 ){
							if ( !$have_current_result ) {
								$total_items = count($search_tags);
								$have_current_result = true;
								$search_in = $search_other_item;
								$current_results_html .= WPSC_Predictive_Search_Shortcodes::get_ptag_items($search_tags, $end_row);
							}
						} else {
							$key_remove = array_search($search_other_item, $search_other_list_original );
							if ($key_remove !== false)
								unset($search_other_list_original[$key_remove]);
						}
					}
					
				}
				
				$search_other = implode(",", $search_other_list_original);
				$key_remove = array_search($search_in, $search_other_list_original );
				if ($key_remove !== false)
					unset($search_other_list_original[$key_remove]);
			}
			
			$html = '<style type="text/css">
					.rs_result_heading{margin:15px 0;}
					.ajax-wait{display: none; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px; background:url("'.WPSC_PS_IMAGES_URL.'/ajax-loader.gif") no-repeat center center #EDEFF4; opacity: 1;text-align:center;}
					.ajax-wait img{margin-top:14px;}
					.p_data,.r_data,.q_data{display:none;}
					.rs_date{color:#777;font-size:small;}
					.rs_result_row{width:100%;float:left;margin:0px 0 10px;padding :0px 0 10px; 6px;border-bottom:1px solid #c2c2c2;}
					.rs_result_row:hover{opacity:1;}
					.rs_rs_avatar{width:64px;margin-right:10px;overflow: hidden;float:left; text-align:center;}
					.rs_rs_avatar img{width:100%;height:auto; padding:0 !important; margin:0 !important; border: none !important;}
					.rs_rs_name{margin-left:0px;}
					.rs_content{margin-left:74px;}
					.rs_more_result{display:none;width:240px;text-align:center;position:fixed;bottom:50%;left:50%;margin-left:-125px;background-color: black;opacity: .75;color: white;padding: 10px;border-radius:10px;-webkit-border-radius: 10px;-moz-border-radius: 10px}
					.rs_rs_price .oldprice{text-decoration:line-through; font-size:80%;}
					.rs_result_others { margin-bottom:20px; }
					.rs_result_others_heading {font-weight:bold;} 
					</style>';
			
			$html .= '<p class="rs_result_heading">'.__('Viewing all', 'wp-e-commerce-predictive-search' ).' <strong>'.$items_search_default[$search_in]['name'].'</strong> '.__('search results for your search query', 'wp-e-commerce-predictive-search' ).' <strong>'.$search_keyword.'</strong></p>';
			
				if ( count($search_other_list_original) > 0) {
					$html .= '<div class="rs_result_others"><div class="rs_result_others_heading">'.__('Sort Search Results by', 'wp-e-commerce-predictive-search' ).'</div>';
					if (get_option('permalink_structure') == '')
						$other_link_search = get_permalink(get_option('ecommerce_search_page_id')).'&rs='. urlencode($search_keyword) .$extra_parameter_product.$extra_parameter_post;
					else
						$other_link_search = rtrim( get_permalink(get_option('ecommerce_search_page_id')), '/' ).'/keyword/'. urlencode($search_keyword) .$extra_parameter_product.$extra_parameter_post;
					$line_vertical = '';
					foreach ($search_other_list_original as $search_other_item) {
						if (get_option('permalink_structure') == '')
							$html .= $line_vertical.'<span class="rs_result_other_item"><a href="'.$other_link_search.'&search_in='.$search_other_item.'&search_other='.$search_other.'" alt="">'.$items_search_default[$search_other_item]['name'].'</a></span>';
						else
							$html .= $line_vertical.'<span class="rs_result_other_item"><a href="'.$other_link_search.'/search-in/'.$search_other_item.'/search-other/'.$search_other.'" alt="">'.$items_search_default[$search_other_item]['name'].'</a></span>';
						$line_vertical = ' | ';
					}
					$html .= '</div>';
				}
				
			$html .= $current_results_html;	
			
			if ( $total_items > $row ) {
				$html .= '<div id="search_more_rs"></div><div style="clear:both"></div><div id="rs_more_check"></div><div class="rs_more_result"><span class="p_data">'.($p + 1).'</span><img src="'.WPSC_PS_IMAGES_URL.'/more-results-loader.gif" /><div><em>'.__('Loading More Results...', 'wp-e-commerce-predictive-search' ).'</em></div></div>';
				$html .= "<script>jQuery(document).ready(function() {
var search_rs_obj = jQuery('#rs_more_check');
var is_loading = false;

function auto_click_more() {
	if (is_loading == false) {
		var visibleAtTop = search_rs_obj.offset().top + search_rs_obj.height() >= jQuery(window).scrollTop();
		var visibleAtBottom = search_rs_obj.offset().top <= jQuery(window).scrollTop() + jQuery(window).height();
		if (visibleAtTop && visibleAtBottom) {
			is_loading = true;
			jQuery('.rs_more_result').fadeIn('normal');
			var p_data_obj = jQuery('.rs_more_result .p_data');
			var p_data = p_data_obj.html();
			p_data_obj.html('');
			var urls = '&psp='+p_data+'&row=".$row."&q=".$search_keyword.$extra_parameter_product.$extra_parameter_post."&search_in=".$search_in."&action=wpscps_get_result_search_page&security=".$wpscps_get_result_search_page."';
			jQuery.post('". admin_url( 'admin-ajax.php', 'relative' )."', urls, function(theResponse){
				if(theResponse != ''){
					var num = parseInt(p_data)+1;
					p_data_obj.html(num);
					jQuery('#search_more_rs').append(theResponse);
					is_loading = false;
					jQuery('.rs_more_result').fadeOut('normal');
				}else{
					jQuery('.rs_more_result').html('<em>".__('No More Results to Show', 'wp-e-commerce-predictive-search' )."</em>').fadeOut(2000);
				}
			});
			return false;
		}
	}
}
jQuery(window).scroll(function(){
	auto_click_more();
});
auto_click_more();
});</script>";
			} elseif ($total_items == 0) {
				$html .= '<p style="text-align:center">'.__('Nothing Found! Please refine your search and try again.', 'wp-e-commerce-predictive-search' ).'</p>';
			}
			
			return $html;
		}
	}
	
	public static function get_result_search_page() {
		add_filter( 'posts_search', array('WPSC_Predictive_Search_Hook_Filter', 'search_by_title_only'), 500, 2 );
		add_filter( 'posts_orderby', array('WPSC_Predictive_Search_Hook_Filter', 'predictive_posts_orderby'), 500, 2 );
		add_filter( 'get_meta_sql', array('WPSC_Predictive_Search_Hook_Filter', 'remove_where_on_focuskw_meta'), 500, 6 );
		add_filter( 'posts_request', array('WPSC_Predictive_Search_Hook_Filter', 'posts_request_unconflict_role_scoper_plugin'), 500, 2);
		global $wpdb;
		global $wp_version;
		global $wpsc_predictive_id_excludes;
		$p = 1;
		$row = 10;
		$search_keyword = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$extra_parameter_product = '';
		$extra_parameter_post ='';
		$search_in = 'product';
		
		if (isset($_REQUEST['psp']) && $_REQUEST['psp'] > 0) $p = stripslashes( strip_tags( $_REQUEST['psp'] ) );
		if (isset($_REQUEST['row']) && $_REQUEST['row'] > 0) $row = stripslashes( strip_tags( $_REQUEST['row'] ) );
		if (isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['q'] ) );
		if (isset($_REQUEST['pcat']) && trim($_REQUEST['pcat']) != '') $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		if (isset($_REQUEST['ptag']) && trim($_REQUEST['ptag']) != '') $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		if (isset($_REQUEST['scat']) && trim($_REQUEST['scat']) != '') $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		if (isset($_REQUEST['stag']) && trim($_REQUEST['stag']) != '') $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		if (isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = stripslashes( strip_tags( $_REQUEST['search_in'] ) );
		
		$args_product = array();
		if ($pcat_slug != '') {
			$args_product['tax_query'] = array( array('taxonomy' => 'wpsc_product_category', 'field' => 'slug', 'terms' => $pcat_slug) );
			if (get_option('permalink_structure') == '')
				$extra_parameter_product .= '&pcat='.$pcat_slug;
			else
				$extra_parameter_product .= '/pcat/'.$pcat_slug;
		} elseif($ptag_slug != '') {
			$args_product['tax_query'] = array( array('taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug) );
			if (get_option('permalink_structure') == '')
				$extra_parameter_product .= '&ptag='.$ptag_slug;
			else
				$extra_parameter_product .= '/ptag/'.$ptag_slug;
		}
		
		$args_post = array();
		if ($cat_slug != '') {
			$args_post['category_name'] = $cat_slug;
			if (get_option('permalink_structure') == '')
				$extra_parameter_post .= '&scat='.$cat_slug;
			else
				$extra_parameter_post .= '/scat/'.$cat_slug;
		} elseif($tag_slug != '') {
			$args_post['tag'] = $tag_slug;
			if (get_option('permalink_structure') == '')
				$extra_parameter_post .= '&stag='.$tag_slug;
			else
				$extra_parameter_post .= '/stag/'.$tag_slug;
		}
		
		$start = $p * $row;
		$end = $start + $row;
		$end_row = $row;
		$total_items = 0;
		$html = '';
		
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
		
		if ($search_keyword != '' && $search_in != '') {
			$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
			$search_nospecial = false;
			if ( $search_keyword != $search_keyword_nospecial ) $search_nospecial = true;
			
			if ( version_compare( $wp_version, '4.0', '<' ) ) {
				$search_keyword_esc = like_escape( $search_keyword );
				$search_keyword_nospecial = like_escape( $search_keyword_nospecial );
			} else {
				$search_keyword_esc = $wpdb->esc_like( $search_keyword );
				$search_keyword_nospecial = $wpdb->esc_like( $search_keyword_nospecial );
			}
			
			if ( $search_in == 'product') {
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
				
				$args = array_merge($args, $args_product);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
				
				$search_products = get_posts($args);
							
				if ( $search_products && count($search_products) > 0 ){
					$total_items = count($search_products);
					$html .= WPSC_Predictive_Search_Shortcodes::get_product_items($search_products, $end_row);
				}
			}
			
			elseif ( $search_in == 'p_sku') {
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'wpsc-product', 'post_status' => 'publish', 'meta_key' => '_wpsc_sku', 'exclude' => $wpsc_predictive_id_excludes['exclude_products'], 'suppress_filters' => FALSE);
				
				$args = array_merge($args, $args_product);
				
				$search_products = get_posts($args);
							
				if ( $search_products && count($search_products) > 0 ){
					$total_items = count($search_products);
					$html .= WPSC_Predictive_Search_Shortcodes::get_product_sku_items($search_products, $end_row);
				}
			}
			
			elseif ( $search_in == 'post') {
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'post', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_posts'], 'suppress_filters' => FALSE);
				
				$args = array_merge($args, $args_post);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
														
				$search_posts = get_posts($args);
							
				if ( $search_posts && count($search_posts) > 0 ){
					$total_items = count($search_posts);
					$html .= WPSC_Predictive_Search_Shortcodes::get_post_items($search_posts, $end_row);
				}
			}
			
			elseif ( $search_in == 'page') {
				$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'page', 'post_status' => 'publish', 'exclude' => $wpsc_predictive_id_excludes['exclude_pages'], 'suppress_filters' => FALSE);
								
				$search_pages = get_posts($args);
				
				if ( count($meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;
							
				if ( $search_pages && count($search_pages) > 0 ){
					$total_items = count($search_pages);
					$html .= WPSC_Predictive_Search_Shortcodes::get_page_items($search_pages, $end_row);
				}
			}
			
			elseif ( $search_in == 'p_cat') {
				$number_items = $row+1;
				
				$where = "tt.taxonomy IN ('wpsc_product_category') ";
				$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
				if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
				$where .= ")";
				
				if ( trim($wpsc_predictive_id_excludes['exclude_p_categories']) != '') 
					$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_categories']).") ";
				$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
				$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT $start,$number_items";
				
				$search_cats = $wpdb->get_results($query);
							
				if ( $search_cats && count($search_cats) > 0 ){
					$total_items = count($search_cats);
					$html .= WPSC_Predictive_Search_Shortcodes::get_pcategory_items($search_cats, $end_row);
				}
			}
			
			elseif ( $search_in == 'p_tag') {
				$number_items = $row+1;
				
				$where = "tt.taxonomy IN ('product_tag') ";
				$where .= $wpdb->prepare( " AND ( ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_esc . '%', '% '. $search_keyword_esc . '%');
				if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WPSC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
				$where .= ")";
				
				if ( trim($wpsc_predictive_id_excludes['exclude_p_tags']) != '') 
					$where .= " AND t.term_id NOT IN (".trim($wpsc_predictive_id_excludes['exclude_p_tags']).") ";
				$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword_esc . '%');
				$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE {$where} ORDER BY {$orderby} LIMIT $start,$number_items";
				
				$search_tags = $wpdb->get_results($query);
							
				if ( $search_tags && count($search_tags) > 0 ){
					$total_items = count($search_tags);
					$html .= WPSC_Predictive_Search_Shortcodes::get_ptag_items($search_tags, $end_row);
				}
			}
			
			if ( $total_items <= $row ) {
				$html .= '';
			}
			
			echo $html;
		}
		die();
	}
	
	public static function get_product_items($search_products, $end_row=10) {
		$show_sku = false;
		$show_price = false;
		$show_addtocart = false;
		$show_categories = false;
		$show_tags = false;
		if ( get_option('ecommerce_search_sku_enable') == '' || get_option('ecommerce_search_sku_enable') == 1 ) $show_sku = true;
		if ( get_option('ecommerce_search_price_enable') == '' || get_option('ecommerce_search_price_enable') == 1 ) $show_price = true;
		//if ( get_option('ecommerce_search_addtocart_enable') == '' || get_option('ecommerce_search_addtocart_enable') == 1 ) $show_addtocart = true;
		if ( get_option('ecommerce_search_categories_enable') == '' || get_option('ecommerce_search_categories_enable') == 1 ) $show_categories = true;
		if ( get_option('ecommerce_search_tags_enable') == '' || get_option('ecommerce_search_tags_enable') == 1 ) $show_tags = true;
		
		$html = '<div class="rs_ajax_search_content">';
		$text_lenght = get_option('ecommerce_search_text_lenght');
		foreach ( $search_products as $product ) {
			$link_detail = get_permalink($product->ID);
			$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($product->ID,'product-thumbnails',64,64);	
			$product_sku_output = WPSC_Predictive_Search_Shortcodes::get_product_sku($product->ID, $show_sku);		
			$product_price_output = WPSC_Predictive_Search_Shortcodes::get_product_price($product->ID, $show_price);
			$product_addtocart_output = WPSC_Predictive_Search_Shortcodes::get_product_addtocart($product->ID, $show_addtocart);
			$product_cats_output = WPSC_Predictive_Search_Shortcodes::get_product_categories($product->ID, $show_categories);
			$product_tags_output = WPSC_Predictive_Search_Shortcodes::get_product_tags($product->ID, $show_tags);
			$product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $product->post_content ) ) ),$text_lenght,'...');
			if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $product->post_excerpt ) ) ),$text_lenght,'...');
			
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.stripslashes( $product->post_title).'</span></a>'.$product_sku_output.$product_price_output.$product_addtocart_output.'<div class="rs_rs_description">'.$product_description.'</div>'.$product_cats_output.$product_tags_output.'</div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';
		
		return $html;
	}
	
	public static function get_product_sku_items($search_products, $end_row=10) {
		$show_price = false;
		$show_addtocart = false;
		$show_categories = false;
		$show_tags = false;
		if ( get_option('ecommerce_search_price_enable') == '' || get_option('ecommerce_search_price_enable') == 1 ) $show_price = true;
		//if ( get_option('ecommerce_search_addtocart_enable') == '' || get_option('ecommerce_search_addtocart_enable') == 1 ) $show_addtocart = true;
		if ( get_option('ecommerce_search_categories_enable') == '' || get_option('ecommerce_search_categories_enable') == 1 ) $show_categories = true;
		if ( get_option('ecommerce_search_tags_enable') == '' || get_option('ecommerce_search_tags_enable') == 1 ) $show_tags = true;
		
		$html = '<div class="rs_ajax_search_content">';
		$text_lenght = get_option('ecommerce_search_text_lenght');
		foreach ( $search_products as $product ) {
			$link_detail = get_permalink($product->ID);
			$product_sku = esc_attr( stripslashes( get_post_meta($product->ID, '_wpsc_sku', true) ) );
			$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($product->ID,'product-thumbnails',64,64);			
			$product_price_output = WPSC_Predictive_Search_Shortcodes::get_product_price($product->ID, $show_price);
			$product_addtocart_output = WPSC_Predictive_Search_Shortcodes::get_product_addtocart($product->ID, $show_addtocart);
			$product_cats_output = WPSC_Predictive_Search_Shortcodes::get_product_categories($product->ID, $show_categories);
			$product_tags_output = WPSC_Predictive_Search_Shortcodes::get_product_tags($product->ID, $show_tags);
			$product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $product->post_content ) ) ),$text_lenght,'...');
			if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words(strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $product->post_excerpt ) ) ),$text_lenght,'...');
			
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.$product_sku.', '.stripslashes( $product->post_title).'</span></a>'.$product_price_output.$product_addtocart_output.'<div class="rs_rs_description">'.$product_description.'</div>'.$product_cats_output.$product_tags_output.'</div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';
		
		return $html;
	}
	
	public static function get_post_items($search_posts, $end_row=10) {
		$show_price = false;
		$show_categories = false;
		$show_tags = false;
		if ( get_option('ecommerce_search_price_enable') == '' || get_option('ecommerce_search_price_enable') == 1 ) $show_price = true;
		if ( get_option('ecommerce_search_categories_enable') == '' || get_option('ecommerce_search_categories_enable') == 1 ) $show_categories = true;
		if ( get_option('ecommerce_search_tags_enable') == '' || get_option('ecommerce_search_tags_enable') == 1 ) $show_tags = true;
		
		$html = '<div class="rs_ajax_search_content">';
		$text_lenght = get_option('ecommerce_search_text_lenght');
		foreach ( $search_posts as $item ) {
			$link_detail = get_permalink($item->ID);
			$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($item->ID,'product-thumbnails',64,64);
			$cats_output = WPSC_Predictive_Search_Shortcodes::get_post_categories($item->ID, $show_categories);
			$tags_output = WPSC_Predictive_Search_Shortcodes::get_post_tags($item->ID, $show_tags);
			$product_description = WPSC_Predictive_Search::wpscps_limit_words( strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $item->post_content) ) ),$text_lenght,'...');
			if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words( strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $item->post_excerpt) ) ),$text_lenght,'...');
			
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.stripslashes( $item->post_title).'</span></a><div class="rs_rs_description">'.$product_description.'</div>'.$cats_output.$tags_output.'</div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';	
		
		return $html;
	}
	
	public static function get_page_items($search_pages, $end_row=10) {
		$html = '<div class="rs_ajax_search_content">';
		$text_lenght = get_option('ecommerce_search_text_lenght');
		foreach ( $search_pages as $item ) {
			$link_detail = get_permalink($item->ID);
			$avatar = WPSC_Predictive_Search::wpscps_get_product_thumbnail($item->ID,'product-thumbnails',64,64);
			$product_description = WPSC_Predictive_Search::wpscps_limit_words( strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $item->post_content)  ) ),$text_lenght,'...');
			if (trim($product_description) == '') $product_description = WPSC_Predictive_Search::wpscps_limit_words( strip_tags( WPSC_Predictive_Search::strip_shortcodes( strip_shortcodes( $item->post_excerpt) ) ),$text_lenght,'...');
			
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.stripslashes( $item->post_title).'</span></a><div class="rs_rs_description">'.$product_description.'</div></div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';	
		
		return $html;
	}
	
	public static function get_pcategory_items($search_cats, $end_row=10) {
		$html = '<div class="rs_ajax_search_content">';
		foreach ( $search_cats as $item ) {
			$link_detail = get_term_link($item->slug, 'wpsc_product_category');
			$avatar = WPSC_Predictive_Search::get_product_cat_thumbnail($item->term_id,64,64);
																									
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.stripslashes( $item->name).'</span></a><div class="rs_rs_description">'.WPSC_Predictive_Search::wpscps_limit_words( strip_tags( $item->description ),get_option('ecommerce_search_text_lenght'),'...').'</div></div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';
		
		return $html;
	}
	
	public static function get_ptag_items($search_tags, $end_row=10) {
		$html = '<div class="rs_ajax_search_content">';
		foreach ( $search_tags as $item ) {
			$link_detail = get_term_link($item->slug, 'product_tag');
			$avatar = WPSC_Predictive_Search::get_product_cat_thumbnail($item->term_id,64,64);
																									
			$html .= '<div class="rs_result_row"><span class="rs_rs_avatar">'.$avatar.'</span><div class="rs_content"><a href="'.$link_detail.'"><span class="rs_rs_name">'.stripslashes( $item->name).'</span></a><div class="rs_rs_description">'.WPSC_Predictive_Search::wpscps_limit_words( strip_tags( $item->description ),get_option('ecommerce_search_text_lenght'),'...').'</div></div></div>';
						
			$html .= '<div style="clear:both"></div>';
			$end_row--;
			if ($end_row < 1) break;
		}
		$html .= '</div>';
		
		return $html;
	}
}
?>
