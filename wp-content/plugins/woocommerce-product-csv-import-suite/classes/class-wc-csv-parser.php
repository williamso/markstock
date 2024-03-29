<?php
/**
 * WooCommerce CSV Importer class for managing parsing of CSV files.
 */
class WC_CSV_Parser {

	var $post_type;
	var $reserved_fields;		// Fields we map/handle (not custom fields)
	var $post_defaults;			// Default post data
	var $postmeta_defaults;		// default post meta
	var $postmeta_allowed;		// post meta validation
	var $allowed_product_types;	// Allowed product types

	public function __construct( $post_type = 'product' ) {

		$this->post_type = $post_type;

		$this->reserved_fields = array(
			'id',
			'product_type',
			'post_id',
			'post_type',
			'menu_order',
			'postmeta',
			'post_status',
			'post_title',
			'post_name',
			'comment_status',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_excerpt',
			'post_parent',
			'post_password',
			'sku',
			'downloadable',
			'virtual',
			'visibility',
			'stock',
			'stock_status',
			'backorders',
			'manage_stock',
			'price',
			'sale_price',
			'regular_price',
			'weight',
			'length',
			'width',
			'height',
			'tax_status',
			'tax_class',
			'upsell_ids',
			'crosssell_ids',
			'sale_price_dates_from',
			'sale_price_dates_to',
			'min_variation_price',
			'max_variation_price',
			'min_variation_regular_price',
			'max_variation_regular_price',
			'min_variation_sale_price',
			'max_variation_sale_price',
			'featured',
			'file_path',
			'download_limit',
			'download_expiry',
			'product_url',
			'button_text',
			'default_attributes'
		);

		$this->post_defaults = array(
			'post_type' 	=> $this->post_type,
			'menu_order' 	=> '',
			'postmeta'		=> array(),
			'post_status'	=> 'publish',
			'post_title'	=> '',
			'post_name'		=> '',
			'post_date'		=> '',
			'post_date_gmt'	=> '',
			'post_content'	=> '',
			'post_excerpt'	=> '',
			'post_parent'	=> 0,
			'post_password'	=> '',
			'comment_status'=> 'open'
		);

		$this->postmeta_defaults = array(
			'sku'			=> '',
			'downloadable' 	=> 'no',
			'virtual' 		=> 'no',
			'price' 		=> '',
			'visibility'	=> 'visible',
			'stock'			=> 0,
			'stock_status'	=> 'instock',
			'backorders'	=> 'no',
			'manage_stock'	=> 'no',
			'sale_price'	=> '',
			'regular_price' => '',
			'weight'		=> '',
			'length'		=> '',
			'width'			=> '',
			'height'		=> '',
			'tax_status'	=> 'taxable',
			'tax_class'		=> '',
			'upsell_ids'	=> array(),
			'crosssell_ids' => array(),
			'sale_price_dates_from' => '',
			'sale_price_dates_to' 	=> '',
			'min_variation_price'	=> '',
			'max_variation_price'	=> '',
			'min_variation_regular_price'	=> '',
			'max_variation_regular_price'	=> '',
			'min_variation_sale_price'	=> '',
			'max_variation_sale_price'	=> '',
			'featured'		=> 'no',
			'file_path'		=> '',
			'download_limit'	=> '',
			'download_expiry'	=> '',
			'product_url'	=> '',
			'button_text'	=> ''

		);

		$this->postmeta_allowed = array(
			'downloadable' 	=> array( 'yes', 'no' ),
			'virtual' 		=> array( 'yes', 'no' ),
			'visibility'	=> array( 'visible', 'catalog', 'search', 'hidden' ),
			'stock_status'	=> array( 'instock', 'outofstock' ),
			'backorders'	=> array( 'yes', 'no', 'notify' ),
			'manage_stock'	=> array( 'yes', 'no' ),
			'tax_status'	=> array( 'taxable', 'shipping', 'none' ),
			'featured'		=> array( 'yes', 'no' ),
		);

		$this->allowed_product_types = array(
			'simple', 'variable', 'grouped', 'external'
		);

	}

	function format_data_from_csv( $data, $enc ) {
		return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
	}

	function parse_data( $file, $delimiter ) {

		// Set locale
		$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
		if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
		@ini_set( 'auto_detect_line_endings', true );

		$parsed_data = array();

		// Put all CSV data into an associative array
		if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) :

			$header = fgetcsv( $handle, 0, $delimiter );
			
		    while ( ( $postmeta = fgetcsv( $handle, 0, $delimiter ) ) !== FALSE ) {
	            $row = array();
	            foreach ( $header as $key => $heading ) {

	            	// Heading is the lowercase version of the column name

	            		$s_heading = strtolower( $heading );

	            	// Check if this heading is being mapped to a different field

	            		if ( isset( $_POST['map_to'][$s_heading] ) ) {
	            			if ( $_POST['map_to'][$s_heading] == 'import_as_meta' ) {

	            				$s_heading = 'meta:' . $s_heading;

	            			} elseif ( $_POST['map_to'][$s_heading] == 'import_as_images' ) {

	            				$s_heading = 'images';

	            			} else {
	            				$s_heading = esc_attr( $_POST['map_to'][$s_heading] );
	            			}
	            		}

	            		if ( $s_heading == '' ) continue;

	            	// Add the heading to the parsed data

	               		$row[$s_heading] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';

	               	// Raw Headers stores the actual column name in the CSV

	            		$raw_headers[ $s_heading ] = $heading;
	            }

	            $parsed_data[] = $row;

	            unset( $postmeta, $row );
		    }
		    fclose( $handle );

		endif;

		return array( $parsed_data, $raw_headers );
	}

	function parse_product( $item ) {
		global $WC_CSV_Product_Import, $wpdb;

		$this->row++;

		$terms_array = $attributes = $default_attributes = $gpf_data = $postmeta = $product = array();

		// Merging
		$merging = ( ! empty( $_GET['merge'] ) && $_GET['merge'] ) ? true : false;

		// Post ID field mapping
		$post_id = ( ! empty($item['id'] ) ) ? $item['id'] : 0;
		$post_id = ( ! empty($item['post_id'] ) ) ? $item['post_id'] : $post_id;

		if ( $merging ) {

			$product['merging'] = true;

			$WC_CSV_Product_Import->log->add( sprintf( __('> Row %s - preparing for merge.', 'wc_csv_import'), $this->row ) );

			// Required fields
			if ( ! $post_id && empty( $item['sku'] ) ) {

				$WC_CSV_Product_Import->log->add( __( '> > Cannot merge without id or sku. Importing instead.', 'wc_csv_import') );

				$merging = false;

			} else {

				// Check product exists
				if ( $post_id ) {

					$query = "SELECT ID FROM $wpdb->posts WHERE ID = %s AND post_type='" . $this->post_type . "';";

					$found_product_id = $wpdb->get_var( $wpdb->prepare($query, array( $post_id ) ) );

					if ( ! $found_product_id ) {

						$WC_CSV_Product_Import->log->add( sprintf(__( '> > Skipped. Cannot find %s with id %s.', 'wc_csv_import'), $this->post_type, $post_id) );
						return false;

					}

					$post_id = $found_product_id;

					$WC_CSV_Product_Import->log->add( sprintf(__( '> > Found product with ID %s.', 'wc_csv_import'), $post_id) );

				} else {

					// Check product to merge exists
					$found_product_id = $wpdb->get_var($wpdb->prepare("
						SELECT $wpdb->posts.ID
					    FROM $wpdb->posts
					    LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
					    WHERE $wpdb->posts.post_type = '" . $this->post_type . "'
					    AND $wpdb->posts.post_status IN ( 'publish', 'private', 'draft' )
					    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
					 ", $item['sku']));

					if ( ! $found_product_id ) {
						$WC_CSV_Product_Import->log->add( sprintf(__( '> > Skipped. Cannot find product with sku %s. Importing instead.', 'wc_csv_import'), $item['sku']) );

						$merging = false;

					} else {

						$post_id = $found_product_id;

						$WC_CSV_Product_Import->log->add( sprintf(__( '> > Found product with ID %s.', 'wc_csv_import'), $post_id) );

					}
				}

				$product['merging'] = true;

			}

		}

		if ( ! $merging ) {

			$product['merging'] = false;

			$WC_CSV_Product_Import->log->add( sprintf( __('> Row %s - preparing for import.', 'wc_csv_import'), $this->row ) );

			// Required fields
			if ( $this->post_type == 'product' && empty( $item['post_title'] ) ) {
				$WC_CSV_Product_Import->log->add( __( '> > Skipped. No post_title set for new product.', 'wc_csv_import') );
				return false;
			}

		}

		$product['post_id'] = $post_id;

		// Get post fields
		foreach ( $this->post_defaults as $column => $default ) {
			if ( isset( $item[ $column ] ) ) $product[ $column ] = $item[ $column ];
		}

		// Get custom fields
		foreach ( $this->postmeta_defaults as $column => $default ) {
			if ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];
			elseif ( isset( $item['_' . $column] ) )
				$postmeta[$column] = (string) $item['_' . $column];
		}

		// Check custom fields are valid
		foreach ( $postmeta as $meta_key => $meta_value ) {

			if ( isset( $this->postmeta_allowed[$meta_key] ) && ! in_array( $meta_value, $this->postmeta_allowed[$meta_key] ) ) {

				$meta_value = $this->postmeta_defaults[$meta_key];

			}

		}

		if ( $merging ) {

			// Don't merge with defaults - if its not set, we won't modify the current value
			// Get old attributes to merge with
			$attributes = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) ) );
			$default_attributes = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_default_attributes', true ) ) );
			$gpf_data = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_woocommerce_gpf_data', true ) ) );

		} else {

			// Merge post meta with defaults
			$product = wp_parse_args( $product, $this->post_defaults );
			$postmeta = wp_parse_args( $postmeta, $this->postmeta_defaults );

		}

		// Handle special meta fields
		if ( $this->post_type == 'product_variation' ) {

			// price
			if ( $merging ) {
				if ( ! isset( $postmeta['price'] ) ) $postmeta['price'] = get_post_meta( $post_id, '_price', true );
				if ( ! isset( $postmeta['sale_price'] ) ) $postmeta['sale_price'] = get_post_meta( $post_id, '_sale_price', true );
			}

		} else {

			// price
			if ( $merging ) {
				if ( ! isset( $postmeta['regular_price'] ) ) $postmeta['regular_price'] = get_post_meta( $post_id, '_regular_price', true );
				if ( ! isset( $postmeta['sale_price'] ) ) $postmeta['sale_price'] = get_post_meta( $post_id, '_sale_price', true );
			}

			if ( isset( $postmeta['regular_price'] ) && isset( $postmeta['sale_price'] ) && $postmeta['sale_price'] !== '' ) {
				$price = min( $postmeta['sale_price'], $postmeta['regular_price']);
				$postmeta['price'] = $price;
			} elseif ( isset( $postmeta['regular_price'] ) ) {
				$postmeta['price'] = $postmeta['regular_price'];
			}

			// Reset dynamically generated meta
			$postmeta['min_variation_price'] = $postmeta['max_variation_price']	= $postmeta['min_variation_regular_price'] =$postmeta['max_variation_regular_price'] = $postmeta['min_variation_sale_price'] = $postmeta['max_variation_sale_price'] = '';
		}

		// upsells
		if ( isset( $postmeta['upsell_ids'] ) && ! is_array( $postmeta['upsell_ids'] ) ) {
			$ids = array_filter( array_map( 'trim', explode('|', $postmeta['upsell_ids'] ) ) );
			$postmeta['upsell_ids'] = $ids;
		}

		// crosssells
		if ( isset( $postmeta['crosssell_ids'] ) && ! is_array( $postmeta['crosssell_ids'] ) ) {
			$ids = array_filter( array_map( 'trim', explode('|', $postmeta['crosssell_ids'] ) ) );
			$postmeta['crosssell_ids'] = $ids;
		}

		// Sale dates
		if ( isset( $postmeta['sale_price_dates_from'] ) ) {
			$postmeta['sale_price_dates_from'] = empty( $postmeta['sale_price_dates_from'] ) ? '' : strtotime( $postmeta['sale_price_dates_from'] );
		}

		if ( isset( $postmeta['sale_price_dates_to'] ) ) {
			$postmeta['sale_price_dates_to'] = empty( $postmeta['sale_price_dates_to'] ) ? '' : strtotime( $postmeta['sale_price_dates_to'] );
		}

		// Put set core product postmeta into product array
		foreach ( $postmeta as $key => $value ) {
			$product['postmeta'][] = array( 'key' 	=> '_' . esc_attr($key), 'value' => $value );
		}

		/**
		 * Handle other columns
		 */
		foreach ( $item as $key => $value ) {

			if ( ! $value ) continue;

			/**
			 * Handle meta: columns - import as custom fields
			 */
			if ( strstr( $key, 'meta:' ) ) {

				//if ( in_array( trim( str_replace( 'meta:', '', $key ) ), $this->reserved_fields ) ) continue; // Skip if reserved

				// Get meta key name
				$meta_key = ( isset( $WC_CSV_Product_Import->raw_headers[$key] ) ) ? $WC_CSV_Product_Import->raw_headers[$key] : $key;
				$meta_key = trim( str_replace( 'meta:', '', $meta_key ) );

				// Add to postmeta array
				$product['postmeta'][] = array(
					'key' 	=> esc_attr( $meta_key ),
					'value' => $value
				);
			}

			/**
			 * Handle meta: columns - import as custom fields
			 */
			elseif ( strstr( $key, 'tax:' ) ) {

				// Get taxonomy
				$taxonomy = trim( str_replace( 'tax:', '', $key ) );

				// Exists?
				if ( ! taxonomy_exists( $taxonomy ) ) {
					$WC_CSV_Product_Import->log->add( sprintf( __('> > Skipping taxonomy "%s" - it does not exist.', 'wc_csv_import'), $taxonomy ) );
					continue;
				}

				// Get terms - ID => parent
				$terms 			= array();
				$raw_terms 		= explode( '|', $value );
				$raw_terms 		= array_map( 'trim', $raw_terms );

				// Handle term hierachy (>)
				foreach ( $raw_terms as $raw_term ) {

					if ( strstr( $raw_term, '>' ) ) {

						$raw_term = explode( '>', $raw_term );
						$raw_term = array_map( 'trim', $raw_term );

						$parent = 0;
						$loop = 0;

						foreach ( $raw_term as $term ) {
						
							$loop ++;
						
							// Check term existance
							$term_exists 	= term_exists( $term, $taxonomy, $parent );
							$term_id 		= is_array( $term_exists ) ? $term_exists['term_id'] : 0;
							
							if ( ! $term_id ) {
								$t = wp_insert_term( trim( $term ), $taxonomy, array( 'parent' => $parent ) );
								
								if ( ! is_wp_error( $t ) ) {
									$term_id = $t['term_id'];
								} else {
									$WC_CSV_Product_Import->log->add( sprintf( __( '> > Failed to import term %s %s', 'wc_csv_import' ), esc_html($term), esc_html($taxonomy) ) );
									break;
								}
							}
							
							if ( ! $term_id )
								break;
							
							// Add to product terms, ready to set if this is the final term
							if ( sizeof( $raw_term ) == $loop )
								$terms[] = $term_id;
								
							$parent = $term_id;
						}

					} else {
					
						// Check term existance
						$term_exists 	= term_exists( $raw_term, $taxonomy, 0 );
						$term_id 		= is_array( $term_exists ) ? $term_exists['term_id'] : 0;
						
						if ( ! $term_id ) {
							$t = wp_insert_term( trim( $raw_term ), $taxonomy );
							
							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
							} else {
								$WC_CSV_Product_Import->log->add( sprintf( __( '> > Failed to import term %s %s', 'wc_csv_import' ), esc_html($raw_term), esc_html($taxonomy) ) );
								break;
							}
						}
						
						// Store terms for later insertion
						if ( $term_id )
							$terms[] = $term_id;

					}

				}

				// Any defined?
				if ( sizeof( $terms ) == 0 ) {
					continue;
				}

				// Product type check
				if ( $taxonomy == 'product_type' ) {
					$term_id = $terms[0];

					$term = get_term_by( 'id', $term_id, 'product_type' );

					if ( ! in_array( $term->name, $this->allowed_product_types ) ) {
						$WC_CSV_Product_Import->log->add( sprintf( __('> > > Product type "%s" not allowed - using simple.', 'wc_csv_import'), $term->name ) );
						$term = get_term_by( 'slug', 'simple', 'product_type' );
						$terms = array( $term->term_id );
					} else {
						$terms = array( $term_id );
					}
				}

				// Add to array
				$terms_array[] = array(
					'taxonomy' 	=> $taxonomy,
					'terms'		=> $terms
				);
			}

			/**
			 * Handle Attributes
			 */
			elseif ( strstr( $key, 'attribute:' ) ) {
				
				$attribute_key 	= sanitize_title( trim( str_replace( 'attribute:', '', $key ) ) );
				$attribute_name = str_replace( 'attribute:', '', $WC_CSV_Product_Import->raw_headers[ $key ] );

				if ( ! $attribute_key ) continue;

				// Taxonomy
				if ( substr( $attribute_key, 0, 3 ) == 'pa_' ) {

					$taxonomy = $attribute_key;

					// Exists?
					if ( ! taxonomy_exists( $taxonomy ) ) {

						$WC_CSV_Product_Import->log->add( sprintf( __('> > Attribute taxonomy "%s" does not exist. Adding it.', 'wc_csv_import'), $taxonomy ) );

						$nicename = strtolower( sanitize_title( str_replace( 'pa_', '', $taxonomy ) ) );

						$exists_in_db = $wpdb->get_var( "SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '" . $nicename . "';" );

						if ( ! $exists_in_db ) {

							// Create the taxonomy
							$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", array( 'attribute_name' => $nicename, 'attribute_type' => 'select' ), array( '%s', '%s' ) );

						}

						// Register the taxonomy now so that the import works!
						register_taxonomy( $taxonomy,
					        array( 'product', 'product_variation' ),
					        array(
					            'hierarchical' => true,
					            'show_ui' => false,
					            'query_var' => true,
					            'rewrite' => false,
					        )
					    );

					}

					// Get terms
					$terms = array();
					$raw_terms = explode( '|', $value );
					$raw_terms = array_filter(array_map( 'trim', $raw_terms ));

					if ( sizeof( $raw_terms ) > 0 ) {
						
						foreach ( $raw_terms as $raw_term ) {
							// Check term existance
							$term_exists 	= term_exists( $raw_term, $taxonomy, 0 );
							$term_id 		= is_array( $term_exists ) ? $term_exists['term_id'] : 0;
							
							if ( ! $term_id ) {
								$t = wp_insert_term( trim( $raw_term ), $taxonomy );
								
								if ( ! is_wp_error( $t ) ) {
									$term_id = $t['term_id'];
								} else {
									$WC_CSV_Product_Import->log->add( sprintf( __( '> > Failed to import term %s %s', 'wc_csv_import' ), esc_html($raw_term), esc_html($taxonomy) ) );
									break;
								}
							}
							
							if ( $term_id )
								$terms[] = $term_id;
						}
					
						// Add to array
						$terms_array[] = array(
							'taxonomy' 	=> $taxonomy,
							'terms'		=> $terms
						);

						$position		= ( isset( $attributes[$taxonomy]['position'] ) ) ? $attributes[$taxonomy]['position'] : 0;
						$is_visible 	= ( isset( $attributes[$taxonomy]['is_visible'] ) ) ? $attributes[$taxonomy]['is_visible'] : 1;
						$is_variation 	= ( isset( $attributes[$taxonomy]['is_variation'] ) ) ? $attributes[$taxonomy]['is_variation'] : 0;

						// Set attribute
						$attributes[$taxonomy] = array(
							'name' 			=> $taxonomy,
							'value' 		=> null,
							'position' 		=> $position,
							'is_visible' 	=> $is_visible,
							'is_variation' 	=> $is_variation,
							'is_taxonomy' 	=> 1
						);
					}

				} else {

					if ( ! $value || ! $attribute_key ) continue;

					$position		= ( isset( $attributes[$taxonomy]['position'] ) ) ? $attributes[$taxonomy]['position'] : 0;
					$is_visible 	= ( isset( $attributes[$taxonomy]['is_visible'] ) ) ? $attributes[$taxonomy]['is_visible'] : 1;
					$is_variation 	= ( isset( $attributes[$taxonomy]['is_variation'] ) ) ? $attributes[$taxonomy]['is_variation'] : 0;

					// Set attribute
					$attributes[$attribute_key] = array(
						'name' 			=> $attribute_name,
						'value' 		=> $value,
						'position' 		=> $position,
						'is_visible' 	=> $is_visible,
						'is_variation' 	=> $is_variation,
						'is_taxonomy' 	=> 0
					);

				}

			}

			/**
			 * Handle Attributes Data - position|is_visible|is_variation
			 */
			elseif ( strstr( $key, 'attribute_data:' ) ) {

				$attribute_key = sanitize_title( trim( str_replace( 'attribute_data:', '', $key ) ) );

				if ( ! $attribute_key ) continue;

				$values 	= explode( '|', $value );
				$position 	= ( isset( $values[0] ) ) ? (int) $values[0] : 0;
				$visible 	= ( isset( $values[1] ) ) ? (int) $values[1] : 1;
				$variation 	= ( isset( $values[2] ) ) ? (int) $values[2] : 0;

				if ( ! isset( $attributes[$attribute_key] ) ) $attributes[$attribute_key] = array();

				$attributes[$attribute_key]['position']		= $position;
				$attributes[$attribute_key]['is_visible']		= $visible;
				$attributes[$attribute_key]['is_variation']	= $variation;
			}

			/**
			 * Handle Attributes Default Values
			 */
			elseif ( strstr( $key, 'attribute_default:' ) ) {

				$attribute_key = sanitize_title( trim( str_replace( 'attribute_default:', '', $key ) ) );

				if ( ! $attribute_key ) continue;

				$default_attributes[ $attribute_key ] = $value;
			}

			/**
			 * Handle gpf: google product feed columns
			 */
			elseif ( strstr( $key, 'gpf:' ) ) {

				$gpf_key = trim( str_replace( 'gpf:', '', $key ) );

				if ( ! is_array( $gpf_data ) )
					$gpf_data = array(
						'availability' 	=> '',
						'condition' 	=> '',
						'brand' 		=> '',
						'product_type' 	=> '',
						'google_product_category' => '',
						'gtin' 			=> '',
						'mpn' 	 		=> '',
						'gender' 	 	=> '',
						'age_group' 	=> '',
						'color' 		=> '',
						'size' 			=> ''
					);

				$gpf_data[$gpf_key] = $value;

			}

			/**
			 * Handle parent_sku column for variations
			 */
			elseif ( strstr( $key, 'parent_sku' ) ) {

				$found_product_id = $wpdb->get_var($wpdb->prepare("
					SELECT $wpdb->posts.ID
				    FROM $wpdb->posts
				    LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
				    WHERE $wpdb->posts.post_type = 'product'
				    AND $wpdb->posts.post_status IN ( 'publish', 'private', 'draft' )
				    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
				 ", $value));

				if ( $found_product_id )
					$product['post_parent'] = $found_product_id;

			}

		}

		// Remove empty attribues
		foreach ( $attributes as $key => $value ) {
			if ( ! isset($value['name']) ) unset( $attributes[$key] );
		}

		/**
		 * Handle images
		 */
		if ( ! empty( $item['images'] ) ) {
			$images = explode( '|', $item['images'] );
		} else {
			$images = '';
		}

		$product['postmeta'][] 	= array( 'key' 	=> '_default_attributes', 'value' => $default_attributes );
		$product['attributes'] 	= $attributes;
		$product['gpf_data'] 	= $gpf_data;
		$product['images'] 		= $images;
		$product['terms'] 		= $terms_array;
		$product['sku']			= ( ! empty( $item['sku'] ) ) ? $item['sku'] : '';

		//$this->posts[] = $product;

		unset( $item, $terms_array, $postmeta, $attributes, $gpf_data, $images );

		return $product;
	}


}