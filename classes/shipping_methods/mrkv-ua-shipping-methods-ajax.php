<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include nova post menu
require_once 'nova-poshta/ajax/mrkv-ua-shipping-methods-ajax-nova.php';
# Include nova post menu
require_once 'ukr-poshta/ajax/mrkv-ua-shipping-methods-ajax-ukr.php';

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_METHODS_AJAX'))
{
	/**
	 * Class for setup shipping methods ajax
	 */
	class MRKV_UA_SHIPPING_METHODS_AJAX
	{
		/**
		 * Constructor for plugin shipping methods
		 * */
		function __construct()
		{
			# Call ajax nova poshta
			new MRKV_UA_SHIPPING_AJAX_NOVA();
			# Call ajax ukr poshta
			new MRKV_UA_SHIPPING_AJAX_UKR();
			
			add_action( 'wp_ajax_mrkv_ua_ship_clear_log', array($this, 'mrkv_ua_ship_clear_log_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_clear_log', array($this, 'mrkv_ua_ship_clear_log_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_get_order_data', array($this, 'mrkv_ua_ship_get_order_data_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_get_order_data', array($this, 'mrkv_ua_ship_get_order_data_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_create_invoice', array($this, 'mrkv_ua_ship_create_invoice_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_create_invoice', array($this, 'mrkv_ua_ship_create_invoice_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_update_order_data', array($this, 'mrkv_ua_ship_update_order_data_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_update_order_data', array($this, 'mrkv_ua_ship_update_order_data_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_update_invoice_data', array($this, 'mrkv_ua_ship_update_invoice_data_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_update_invoice_data', array($this, 'mrkv_ua_ship_update_invoice_data_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_remove_invoice_data', array($this, 'mrkv_ua_ship_remove_invoice_data_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_remove_invoice_data', array($this, 'mrkv_ua_ship_remove_invoice_data_func') );

			add_action( 'wp_ajax_mrkv_ua_ship_remove_all_invoices', array($this, 'mrkv_ua_ship_remove_all_invoices_func') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_remove_all_invoices', array($this, 'mrkv_ua_ship_remove_all_invoices_func') );

			add_action( 'wp_ajax_mrkv_update_shipping_method', array($this, 'mrkv_update_shipping_method_func') );
			add_action( 'wp_ajax_nopriv_mrkv_update_shipping_method', array($this, 'mrkv_update_shipping_method_func') );
		}

		/**
		 * Method change
		 * */
		public function mrkv_update_shipping_method_func()
		{
			if (isset($_POST['order_id']) && isset($_POST['shipping_method']) && isset($_POST['shipping_method_name'])) 
			{
	        	$order_id = absint($_POST['order_id']);
			    $shipping_method = sanitize_text_field($_POST['shipping_method']);
			    $shipping_method_name = sanitize_text_field($_POST['shipping_method_name']);
			    $order = wc_get_order($order_id);

			    if($order)
			    {
			    	$shipping_methods = $order->get_items('shipping');

			    	if (!empty($shipping_methods)) 
			    	{
			    		foreach ($shipping_methods as $item_id => $shipping_item) 
					    {
					        $shipping_item->set_method_id($shipping_method);
					        $shipping_item->set_method_title($shipping_method_name);
					        $shipping_item->save();
					    }
			    	}
			    	else
			    	{
			    		$shipping_rate = new WC_Shipping_Rate(
				            $shipping_method,
				            $shipping_method_name,
				            0,
				            [],
				            $shipping_method
				        );

				        $shipping_item = new WC_Order_Item_Shipping();
				        $shipping_item->set_props([
				            'method_title' => $shipping_rate->get_label(),
				            'method_id'    => $shipping_rate->get_method_id(),
				            'total'        => wc_format_decimal($shipping_rate->get_cost())
				        ]);
				        $order->add_item($shipping_item);
			    	}

					$order->calculate_totals();
    				$order->save();
			    }
		    }

		    wp_die();
		}

		/**
		 * Clear main log
		 * */
		public function mrkv_ua_ship_clear_log_func()
		{
			if(isset($_POST['shipping']))
			{
				file_put_contents(MRKV_UA_SHIPPING_PLUGIN_PATH . 'logs/' . $_POST['shipping'] . '/debug-' . $_POST['shipping'] . '.log', '');
			}

			wp_die();
		}

		/**
		 * Get order data AJAX
		 * */
		public function mrkv_ua_ship_get_order_data_func()
		{
			if(isset($_POST['order_id']))
			{
				$order = wc_get_order($_POST['order_id']);

				if($order)
				{
					$order_data = $order->get_data();

					if($order_data)
					{
						$first_name = !empty( $order_data['shipping']['first_name'] )
				            ? html_entity_decode(esc_html( $order_data['shipping']['first_name'] ), ENT_QUOTES, 'UTF-8')
				            : html_entity_decode(esc_html( $order_data['billing']['first_name'] ), ENT_QUOTES, 'UTF-8');

			            $last_name = !empty( $order_data['shipping']['last_name'] )
				            ? html_entity_decode(esc_html( $order_data['shipping']['last_name'] ), ENT_QUOTES, 'UTF-8')
				            : html_entity_decode(esc_html( $order_data['billing']['last_name'] ), ENT_QUOTES, 'UTF-8');

			            $args = array(
							'mrkv_ua_ship_invoice_first_name' => $first_name,
							'mrkv_ua_ship_invoice_last_name' => $last_name,
						);

			            $keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
			    		$key = '';
			    		$current_shipping = '';

			            foreach($order->get_shipping_methods() as $shipping)
			            {
			            	foreach($keys_shipping as $key_ship)
							{
								$current_shipping = $shipping->get_method_id();

								if(str_contains($shipping->get_method_id(), $key_ship))
								{
									$key = $key_ship;
								}
								if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
								{
									$key = $key_ship;
									$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
								}
							}
			            }

			            $args['mrkv_ua_ship_key'] = $key;

			            $area_name = $order->get_billing_state() ? $order->get_billing_state() . ', ' : '';
		            	$city_name = $order->get_billing_city() ? $order->get_billing_city() . ', ' : '';
		            	$address_1 = $order->get_billing_address_1() ? $order->get_billing_address_1() . ', ' : '';
		            	$address_2 = $order->get_billing_address_2() ? $order->get_billing_address_2() . ', ' : '';
		            	$postcode = $order->get_billing_postcode() ? __('Index', 'mrkv-ua-shipping') . ' ' . $order->get_billing_postcode() . ', ' : '';
		            	$added_info = $order->get_meta($current_shipping . '_flat') ? $order->get_meta($current_shipping . '_flat') . ', ' : '';

			            $address_to = $area_name . $city_name . $address_1 . $address_2 . $added_info . $postcode;
			            $address_to = rtrim($address_to, ', ');
			            $address_to = htmlspecialchars_decode($address_to);
			            $address_to = stripslashes($address_to);

			            $args['mrkv_ua_ship_invoice_address'] = $address_to;

			            $patronomic = html_entity_decode($order->get_meta($current_shipping . '_patronymic'));
			            $phone = ! empty( $order_data['shipping']['phone'] )
			                ? str_replace( array('+', ' ', '(' , ')', '-'), '', esc_html( $order_data['shipping']['phone'] ) )
			                : str_replace( array('+', ' ', '(' , ')', '-'), '', esc_html( $order_data['billing']['phone'] ) );

		                $len = strlen( '38' );
    					if ( substr( $phone, 0, $len ) === '38' ){
    						$phone = substr( $phone, 2 );
    					}

    					$len = strlen( '+38' );
    					if ( substr( $phone, 0, $len ) === '+38' ){
    						$phone = substr( $phone, 3 );
    					}

    					$len = strlen( '8' );
    					if ( substr( $phone, 0, $len ) === '8' ){
    						$phone = substr( $phone, 1 );
    					}

    					if (strlen($phone) > 9) {
					        $phone = substr($phone, -9);
					    }

					    if (strlen($phone) === 9) {
					        $phone = '0' . $phone;
					    }	

			            $args['mrkv_ua_ship_invoice_patronymic'] = $patronomic;
			            $args['mrkv_ua_ship_invoice_phone'] = $phone;

			            $payer_delivery = '';

			            foreach($order->get_items( 'shipping' ) as $item_id => $item)
			            {
			            	$instance_id = $item->get_instance_id();

			            	$shipping_settings = get_option('woocommerce_' . $current_shipping . '_' . $instance_id . '_settings');

			            	if(isset($shipping_settings['enable_minimum_cost']) && $shipping_settings['enable_minimum_cost'] == 'yes' 
			            		&& isset($shipping_settings['minimum_cost_total']) && $shipping_settings['minimum_cost_total'] <= $order->get_total())
						    {
						        $payer_delivery = 'Sender';
						    }
			            }

			            $args['mrkv_ua_ship_invoice_payer_delivery'] = $payer_delivery;

			            $order_total = $order->get_total();

			            $args['mrkv_ua_ship_invoice_money_transfer_amount'] = $order_total;
			            $args['mrkv_ua_ship_invoice_cost'] = $order_total;

			            if($key == 'nova-poshta')
			            {
		            		$shipping_settings_global = get_option($key . '_m_ua_settings');

		            		$mrkvnp_invoice_prepayment = (isset($shipping_settings_global['shipment']['prepayment']) && $shipping_settings_global['shipment']['prepayment']) ? $shipping_settings_global['shipment']['prepayment'] : 0;
		            		if($order_total > $mrkvnp_invoice_prepayment)
		            		{
		            			$args['mrkv_ua_ship_invoice_money_transfer_amount'] = $order_total - $mrkvnp_invoice_prepayment;
		            		}
			            }

			            $post_pay_cost = '';

			            if($order->get_payment_method() == 'cod' && $payer_delivery != 'Sender'){
			            	$post_pay_cost = $order->get_total();
			            	$args['mrkv_ua_ship_invoice_money_transfer'] = true;
			            }
			            $args['mrkv_ua_ship_invoice_cost_back'] = $post_pay_cost;
			            

			            $weight = 0;
						$length = 0;
						$width = 0;
						$height = 0;

						$dimension_unit = get_option( 'woocommerce_dimension_unit' );
			            foreach ( $order->get_items() as $item_id => $product_item ) 
			            {
			            	$product_id = $product_item->get_variation_id() ? $product_item->get_variation_id() : $product_item->get_product_id();
			            	
			            	$product = wc_get_product($product_id);

							if ( ! $product ) continue;

							$item_length = ( null !== $product->get_length() && $product->get_length()) ? wc_get_dimension( $product->get_length(), 'cm', $dimension_unit ) : 0.00;
		                    $item_width = ( null !== $product->get_width() && $product->get_width()) ? wc_get_dimension( $product->get_width(), 'cm', $dimension_unit ) : 0.00;
		                    $item_height = ( null !== $product->get_height() && $product->get_height()) ? wc_get_dimension( $product->get_height(), 'cm', $dimension_unit ) : 0.00;
		                    $item_weight = ( null !== $product->get_weight() && $product->get_weight()) ? (floatval($product->get_weight()) * intval($product_item->get_quantity())) : 0.00;

	                    	$length = ($item_length > $length) ? $item_length : $length;
	                    	$width = ($item_width > $width) ? $item_width : $width;
	                    	$height = ($item_height > $height) ? $item_height : $height;

                    		$weight += $item_weight;
						}

						$weight_unit = get_option('woocommerce_weight_unit');
						$weight_coef = 1;

						if($key == 'nova-poshta')
						{
							if ( 'g' == $weight_unit ) $weight_coef = 0.001;
				            if ( 'kg' == $weight_unit ) $weight_coef = 1;
				            if ( 'lbs' == $weight_unit ) $weight_coef = 0.45359;
				            if ( 'oz' == $weight_unit ) $weight_coef = 0.02834;
						}
						else
						{
							if ( 'g' == $weight_unit ) $weight_coef = 1;
				            if ( 'kg' == $weight_unit ) $weight_coef = 1000;
				            if ( 'lbs' == $weight_unit ) $weight_coef = 453.59;
				            if ( 'oz' == $weight_unit ) $weight_coef = 28.34;
						}

			            $args['mrkv_ua_ship_invoice_shipment_weight'] = $weight * $weight_coef;
			            $args['mrkv_ua_ship_invoice_shipment_length'] = $length;
			            $args['mrkv_ua_ship_invoice_shipment_width'] = $width;
			            $args['mrkv_ua_ship_invoice_shipment_height'] = $height;

			            if(isset($_POST['description']))
			            {
			            	$description_converted = $this->convert_description($order, $_POST['description']);
			            	$description_converted = preg_replace('/["\/.;]+/', '', $description_converted);
			            	$description_converted = str_replace('pcs', '', $description_converted);
			            	$args['mrkv_ua_ship_invoice_shipment_description'] = $description_converted;
			            }

			            echo json_encode($args);
					}
				}
			}

			wp_die();
		}

		public function convert_description($order, $description) 
		{
			if(str_contains($description, '[order_id]'))
			{
				$description = str_replace( "[order_id]", $order->get_id(), $description );
			}
			if(str_contains($description, '[product_list]'))
			{
				$product_list = '';

				foreach($order->get_items() as $item_id => $item)
				{
					$product_list .= $item->get_name() . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list]", $product_list, $description );
			}
			if(str_contains($description, '[product_list_qa]'))
			{
				$product_list = '';

				foreach($order->get_items() as $item_id => $item)
				{
					$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			        
			        $product = wc_get_product($product_id);

					$product_list .= $item->get_name() . ' '. $product->get_sku() . ' ' . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list_qa]", $product_list, $description );
			}
			if(str_contains($description, '[product_list_a]'))
			{
				$product_list = '';

				foreach($order->get_items() as $item_id => $item)
				{
					$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			        
			        $product = wc_get_product($product_id);

					$product_list .= $product->get_sku() . ' ' . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list_a]", $product_list, $description );
			}
			if(str_contains($description, '[quantity]'))
			{
				$quantity = 0;

				foreach($order->get_items() as $item_id => $item)
				{
					$quantity += $item->get_quantity();
				}
				$description = str_replace( "[quantity]", $quantity, $description );
			}
			if(str_contains($description, '[quantity_p]'))
			{
				$quantity = count($order->get_items());
				$description = str_replace( "[quantity_p]", $quantity, $description );
			}
			
			return $description;
	    }

		public function mrkv_ua_ship_create_invoice_func()
		{
			$message_error = '';
			$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
			$current_ship_key = isset($_POST['current_ship_key']) ? $_POST['current_ship_key'] : '';

			# Check data
			if($order_id && $current_ship_key)
			{
				# Get order object
				$order = wc_get_order($order_id);

				require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $current_ship_key . '/api/mrkv-ua-shipping-api-' . $current_ship_key . '.php';
				$api_class = MRKV_UA_SHIPPING_LIST[$current_ship_key]['api_class'];
				$settings_shipping = get_option($current_ship_key . '_m_ua_settings');
				$mrkv_object_shipping = new $api_class($settings_shipping);

				require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $current_ship_key . '/invoice/mrkv-ua-shipping-' . $current_ship_key . '-invoice.php';
				$invoice_class = MRKV_UA_SHIPPING_LIST[$current_ship_key]['invoice_class'];
				$mrkv_object_invoice = new $invoice_class($order, $_POST, $mrkv_object_shipping, $settings_shipping);

				$result = $mrkv_object_invoice->mrkv_ua_ship_create_invoice();

				echo json_encode($result);
			}
			else
			{
				$message_error = __('Empty order data', 'mrkv-ua-shipping');
			}

			wp_die();
		}

		public function mrkv_ua_ship_update_order_data_func()
		{
			if(isset($_POST['mrkv_order_id']))
			{
				# Get order object
				$order = wc_get_order($_POST['mrkv_order_id']);
				$key = $_POST['mrkv_current_shipping_key'];
				$current_shipping = $_POST['mrkv_current_shipping'];

				foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $field_id => $field_val)
    			{
    				if(isset($_POST[$current_shipping . $field_id]) && isset($field_val['replace']))
    				{
    					if($field_val['replace'] == '_city'){
    						# Add billing city name to Thank you page
				            $order->set_billing_city( $_POST[$current_shipping . $field_id] );
				            # Add shipping city name to Thankyou page
				            $order->set_shipping_city( $_POST[$current_shipping . $field_id] );
    					}
    					elseif($field_val['replace'] == '_state')
    					{
    						$order->set_billing_state(esc_attr($_POST[$current_shipping . $field_id]) );
          					$order->set_shipping_state( esc_attr($_POST[$current_shipping . $field_id]) );
    					}
    					elseif($field_val['replace'] == '_address_1')
    					{
    						$order->set_billing_address_1(esc_attr($_POST[$current_shipping . $field_id]) );
          					$order->set_shipping_address_1( esc_attr($_POST[$current_shipping . $field_id]) );
    					}
    					elseif($field_val['replace'] == '_postcode')
    					{
    						$order->set_billing_postcode(esc_attr($_POST[$current_shipping . $field_id]) );
          					$order->set_shipping_postcode( esc_attr($_POST[$current_shipping . $field_id]) );
    					}
    					elseif($field_val['replace'] == '_address_2')
    					{
    						$order->set_billing_address_2(esc_attr($_POST[$current_shipping . $field_id]) );
          					$order->set_shipping_address_2( esc_attr($_POST[$current_shipping . $field_id]) );
    					}
    					else
    					{
    						$order->update_meta_data( $current_shipping . $field_id, esc_attr($_POST[$current_shipping . $field_id]) );
    					}
    				}
    			}
    			
    			$order->save();
			}

			wp_die();
		}

		public function mrkv_ua_ship_update_invoice_data_func()
		{
			if(isset($_POST['order_id']) && isset($_POST['invoice']))
			{
				# Get order object
				$order = wc_get_order($_POST['order_id']);
				$order->update_meta_data('mrkv_ua_ship_invoice_number', $_POST['invoice']);
				$order->add_order_note(__('Added invoice number','mrkv-ua-shipping') . ': ' . $_POST['invoice'], $is_customer_note = 0, $added_by_user = false);
	        	$order->save();
			}

			wp_die();
		}
		
		public function mrkv_ua_ship_remove_invoice_data_func()
		{
			if(isset($_POST['order_id']))
			{
				# Get order object
				$order = wc_get_order($_POST['order_id']);
				$order->delete_meta_data('mrkv_ua_ship_invoice_number');
				$order->delete_meta_data('novaposhta_ttn');
				$order->delete_meta_data('ukrposhta_ttn');
				$order->add_order_note(__('Invoice removed','mrkv-ua-shipping'), $is_customer_note = 0, $added_by_user = false);
	        	$order->save();
			}

			wp_die();
		}

		public function mrkv_ua_ship_remove_all_invoices_func()
		{
			if(isset($_POST['orders']))
			{
				$orders_id = explode(",", $_POST['orders']);

				foreach($orders_id as $order_id)
				{
					# Get order object
					$order = wc_get_order($order_id);
					$order->delete_meta_data('mrkv_ua_ship_invoice_number');
					$order->delete_meta_data('novaposhta_ttn');
					$order->delete_meta_data('ukrposhta_ttn');
		        	$order->save();
				}
			}

			wp_die();
		}
	}
}