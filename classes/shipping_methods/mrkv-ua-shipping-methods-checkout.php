<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include checkout settings shipping validation
require_once 'mrkv-ua-shipping-methods-checkout-validation.php'; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_METHODS_CHECKOUT'))
{
	/**
	 * Class for setup shipping methods checkout
	 */
	class MRKV_UA_SHIPPING_METHODS_CHECKOUT
	{
		/**
		 * @param array Active shipping list
		 * */
		private $active_shipping;

		/**
		 * Constructor for plugin shipping methods
		 * */
		function __construct()
		{
			add_action('woocommerce_before_checkout_form', array($this, 'mrkv_add_checkout_custom_shipping_fields'));
			add_filter( 'woocommerce_cart_shipping_packages', array($this, 'wc_shipping_rate_cache_invalidation'), 100 );

			new MRKV_UA_SHIPPING_METHODS_CHECKOUT_VALIDATION();
		}

		public function mrkv_add_checkout_scripts($settings)
		{
			# Custom style and script
	        wp_enqueue_style('front-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/css/front/front-mrkv-ua-shipping.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION);
	        wp_enqueue_script('front-mrkv-ua-shipping-select2', MRKV_UA_SHIPPING_ASSETS_URL . '/js/global/select2.min.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);
	        wp_enqueue_script('front-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/js/front/front-mrkv-ua-shipping.js', array('jquery','jquery-ui-autocomplete'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

	        $args = array('ajax_url' => admin_url( 'admin-ajax.php' ));

	        if(!empty($this->active_shipping))
	        {
	        	foreach($this->active_shipping as $method_key => $method_list)
	        	{
	        		# Include settings checkout by shipping
					include $method_key . '/checkout/mrkv-ua-shipping-checkout.php';

					wp_enqueue_script('front-mrkv-ua-shipping-' . $method_key, MRKV_UA_SHIPPING_ASSETS_URL . '/js/front/front-mrkv-ua-shipping-' . $method_key .'.js', array('jquery','jquery-ui-autocomplete', 'front-mrkv-ua-shipping-select2'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

					wp_localize_script('front-mrkv-ua-shipping-' . $method_key, 'mrkv_ua_ship_helper', $args);
	        	}
	        }
		}

		public function mrkv_add_checkout_custom_shipping_fields()
		{
		    $this->active_shipping = array();
		    $plugin_shipping = array();
		    $has_shipping_mrkv = false;

		    foreach(MRKV_UA_SHIPPING_LIST as $key => $shipping)
		    {
		    	$plugin_shipping[] = $key;
		    }

		    foreach(WC_Shipping_Zones::get_zones() as $zone)
		    {
		    	foreach($zone['shipping_methods'] as $shipping)
		    	{
		    		foreach($plugin_shipping as $key)
		    		{
		    			if(str_contains($shipping->id, 'mrkv_ua_shipping_' . $key))
			    		{
			    			if($shipping->is_enabled())
			    			{
			    				$this->active_shipping[$key]['methods'][$shipping->id]['slug'] = $shipping->id;
			    				$this->active_shipping[$key]['methods'][$shipping->id]['instance_id'] = $shipping->instance_id;
			    				$has_shipping_mrkv = true;
			    			}
			    		}
		    		}
		    	}
		    }

		    $default_zone = new WC_Shipping_Zone(0);
			$default_zone_methods = $default_zone->get_shipping_methods();

		    foreach ( $default_zone_methods as $shipping )
		    {
			    foreach($plugin_shipping as $key)
	    		{
	    			if(str_contains($shipping->id, 'mrkv_ua_shipping_' . $key))
		    		{
		    			if($shipping->is_enabled())
		    			{
		    				$this->active_shipping[$key]['methods'][$shipping->id]['slug'] = $shipping->id;
		    				$this->active_shipping[$key]['methods'][$shipping->id]['instance_id'] = $shipping->instance_id;
		    				$has_shipping_mrkv = true;
		    			}
		    		}
	    		}
			}

		    foreach($this->active_shipping as $key => $shipping)
		    {
		    	$settings = get_option($key . '_m_ua_settings');

				if(isset($settings['checkout']['position']) && $settings['checkout']['position'] != '')
				{
					$this->active_shipping[$key]['position'] = $settings['checkout']['position'];
				}
				else
				{
					$this->active_shipping[$key]['position'] = 'woocommerce_before_order_notes';
				}

				$this->active_shipping[$key]['settings'] = $settings;

				add_action($this->active_shipping[$key]['position'], array($this, 'mrkv_ua_ship_inject_shipping_fields'));
		    }

		    if($has_shipping_mrkv)
		    {
		    	$this->mrkv_add_checkout_scripts($settings);
		    }
		}

		/**
		 * Get content
		 * */
		public function mrkv_ua_ship_inject_shipping_fields()
		{
			foreach($this->active_shipping as $key => $shipping)
			{
				if(current_filter() == $shipping['position'])
				{
					global $wp_filter;
				    
				    $saved_filters = isset($wp_filter['woocommerce_form_field_hidden']) && isset($wp_filter['woocommerce_form_field_hidden']->callbacks)
		            ? $wp_filter['woocommerce_form_field_hidden']->callbacks
		            : null;

			        if ($saved_filters) {
			            remove_all_filters('woocommerce_form_field_hidden');
			        }

					foreach($shipping['methods'] as $method => $method_data)
					{
						?>
					    	<div id="<?php echo esc_html($method); ?>_fields" class="<?php echo esc_html($method); ?>-fields mrkv_ua_shipping_checkout_fields">
					        	<div id="<?php echo esc_html($method); ?>-shipping-info">
					        		<?php 
						        		foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$method]['checkout_fields'] as $id => $args)
						        		{
						        			$is_enabled_address_data = (isset($shipping['settings']['checkout']['hide_saving_data']) && $shipping['settings']['checkout']['hide_saving_data'] == 'on') ? true : false;

						        			if(is_user_logged_in() && $is_enabled_address_data)
						        			{
						        				$default_value = get_user_meta( get_current_user_id(), $method . $id , true  );
						        			}
						        			else
						        			{
						        				$default_value = '';
						        			}

						        			if($default_value && $args['type'] == 'select')
						        			{
						        				$args['options'][$default_value] = $default_value;
						        				$args['custom_attributes']['data-default'] = $default_value;
						        			}

						        			if(isset($args['label']) && function_exists( 'pll_translate_string' ))
						        			{
						        				$args['label'] = esc_html__( $args['label'], 'mrkv-ua-shipping' );
						        			}

						        			if(isset($args['placeholder']) && function_exists( 'pll_translate_string' ))
						        			{
						        				$args['placeholder'] = esc_html__( $args['placeholder'], 'mrkv-ua-shipping' );
						        			}

						        			if(isset($args['options']) && !empty($args['options']) && function_exists( 'pll_translate_string' ))
						        			{
						        				foreach($args['options'] as $key_option => $value_option)
						        				{
						        					$args['options'] = array('' => esc_html__($value_option, 'mrkv-ua-shipping')); 
						        				}
						        			}

						        			woocommerce_form_field($method . $id, $args, $default_value);
						        		}
					        		?>
					    		</div>
							</div>
					    <?php
					}

					if ($saved_filters && isset($wp_filter['woocommerce_form_field_hidden'])) {
			            $wp_filter['woocommerce_form_field_hidden']->callbacks = $saved_filters;
			        }
				}
			}
		}

		public function wc_shipping_rate_cache_invalidation($packages){
			foreach ( $packages as &$package ) {
				$package['rate_cache'] = wp_rand();
			}

			return $packages;
		}
	}
}