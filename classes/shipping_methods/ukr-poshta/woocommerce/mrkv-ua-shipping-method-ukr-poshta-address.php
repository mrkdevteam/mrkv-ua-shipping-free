<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_UKR_POSHTA_ADDRESS'))
{
	/**
	 * Class for setup ukr poshta address method
	 */
	class MRKV_UA_SHIPPING_UKR_POSHTA_ADDRESS extends WC_Shipping_Method
	{
		public function __construct($instance_id = 0)
        {
         	# Set instance id
            $this->instance_id = absint( $instance_id );
            parent::__construct( $instance_id );

            # Set main fields
            $this->id = 'mrkv_ua_shipping_ukr-poshta_address';
            $this->method_title = __( 'UkrPoshta Address', 'mrkv-ua-shipping' );
            $this->method_description = $this->get_description();

            # Set supports
            $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );

            $this->init();

            # Get setting values
            $this->title = $this->get_option( 'title' );
            $this->enabled = true;

            # Set enabled
            $this->enabled = $this->get_option( 'enabled' );
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init()
        {
            $this->init_form_fields();
            $this->init_settings();
            
            # Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields()
        {
            $this->instance_form_fields = array(
               'title' => array(
                    'title' => __('This controls the title which the user sees during checkout', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'description' => '',
                    'default' => __('UkrPoshta Address', 'mrkv-ua-shipping')
                ),
                'enable_cost' => array(
                    'title' => __('Enable Price for Delivery', 'mrkv-ua-shipping'),
                    'label' => __('If checked, shipping price will be add for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'enable_fix_cost' => array(
                    'title' => __('Enable Fixed Price for Delivery', 'mrkv-ua-shipping'),
                    'label' => __('If checked, fixed price will be set for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'fix_cost_total' => array(
                    'title' => __('Fixed shipping price', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-ua-shipping'),
                    'description' => '',
                    'default' => 0.00
                ),
                'enable_minimum_cost' => array(
                    'title' => __('Enable Minimum amount for free shipping', 'mrkv-ua-shipping'),
                    'label' => __('If checked, Minimum amount for free shipping will be set for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'minimum_cost_total' => array(
                    'title' => __('Minimum amount for free shipping', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-ua-shipping'),
                    'description' => '',
                    'default' => 0.00
                ),
                'free_shipping_text' => array(
                    'title' => __('Text with free delivery', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('FREE to UkrPoshta Address', 'mrkv-ua-shipping'),
                    'description' => '',
                ),
            );
        }

        /**
         * calculate_shipping function.
         *
         * @access public
         *
         * @param array $package
         */
        public function calculate_shipping($package = array())
        {
        	# Create rate
        	$rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => 0.00,
                'calc_tax' => 'per_item'
            );

            if($this->get_option('enable_cost') && $this->get_option('enable_cost') == 'yes' && $this->get_option('enable_fix_cost') != 'yes')
            {
                $settings_method = get_option('ukr-poshta_m_ua_settings');

                require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
                $mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA($settings_method);
                require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-calculate-ukr-poshta.php';
                $mrkv_calculate_ukr_poshta = new MRKV_UA_SHIPPING_CALCULATE_UKR_POSHTA($mrkv_object_ukr_poshta);

                $city_recipient = '04071';
                $city_sender = (isset($settings_method['sender']['warehouse']['name']) && $settings_method['sender']['warehouse']['name']) ? $settings_method['sender']['warehouse']['name'] : '47743';
                $weight = 0.00;
                $length = 0.00;
                $cost = WC()->cart->get_subtotal();
                $service_type = 'W2D';
                $cargo_type = (isset($settings_method['shipment']['type']) && $settings_method['shipment']['type']) ? $settings_method['shipment']['type'] : 'EXPRESS';

                if(isset( $_POST['post_data'] ))
                {
                    parse_str( $_POST['post_data'], $post_data );
                    
                    if(isset($post_data[$this->id . '_house_ref']) && $post_data[$this->id . '_house_ref'])
                    {
                        $city_recipient = $post_data[$this->id . '_house_ref'];
                    }
                }
                else
                {
                    $city_recipient = get_user_meta( get_current_user_id(), $this->id . '_house_ref' , true  );
                }

                $weight_coef = $this->convert_weight_unit();
                $weight = ( WC()->cart->cart_contents_weight > 0 ) ? WC()->cart->cart_contents_weight * $weight_coef : 0.00;

                if((!$weight) && isset($settings_method['shipment']['weight']) && $settings_method['shipment']['weight'])
                {
                    $weight = $settings_method['shipment']['weight'];
                }

                $dimension_unit = get_option( 'woocommerce_dimension_unit' );

                foreach(WC()->cart->get_cart() as $cart_item => $cart_value)
                {
                    $item_length = ( null !== $cart_value['data']->get_length() && $cart_value['data']->get_length()) ? wc_get_dimension( $cart_value['data']->get_length(), 'cm', $dimension_unit ) : 0.00;
                    $item_width = ( null !== $cart_value['data']->get_width() && $cart_value['data']->get_width()) ? wc_get_dimension( $cart_value['data']->get_width(), 'cm', $dimension_unit ) : 0.00;
                    $item_height = ( null !== $cart_value['data']->get_height() && $cart_value['data']->get_height()) ? wc_get_dimension( $cart_value['data']->get_height(), 'cm', $dimension_unit ) : 0.00;

                    $product_length = intval( ceil( floatval(max($item_length, $item_width, $item_height))));

                    if($product_length > $length)
                    {
                        $length = intval( ceil( floatval(max($item_length, $item_width, $item_height))));
                    }
                }

                if((!$length) && isset($settings_method['shipment']['length']) && $settings_method['shipment']['length'])
                {
                    $length = intval( ceil($settings_method['shipment']['length']));
                }

                $result_calculate = $mrkv_calculate_ukr_poshta->calculate_shipping_cost($city_sender, $city_recipient, $weight, $service_type, $cost, $cargo_type, $length);

                if($result_calculate)
                {
                    $rate['cost'] = $result_calculate;
                }
                else{
                    $rate['cost'] = 0.00;
                }
            }

            if($this->get_option('enable_fix_cost') && $this->get_option('enable_fix_cost') == 'yes')
            {
                $rate['cost'] = $this->get_option('fix_cost_total');
            }

            if($this->get_option('enable_minimum_cost') && $this->get_option('enable_minimum_cost') == 'yes')
            {
                $woo_cart_total = WC()->cart->get_subtotal();

                if($woo_cart_total >= $this->get_option('minimum_cost_total'))
                {
                    $rate['cost'] = 0.00;

                    if($this->get_option('free_shipping_text'))
                    {
                        $rate['label'] = $this->get_option('free_shipping_text');
                    }
                }
            }

        	# Set rate
            $this->add_rate($rate);
        }

        /**
         * Is this method available?
         * @param array $package
         * @return bool
         */
        public function is_available($package)
        {
            return $this->is_enabled();
        }

        /**
         * @return string
         */
        private function get_description()
        {
            return '';
        }

        public function convert_weight_unit() {

            $weight_unit = get_option('woocommerce_weight_unit');

            if ( 'g' == $weight_unit ) return 1;
            if ( 'kg' == $weight_unit ) return 1000;
            if ( 'lbs' == $weight_unit ) return 453.59;
            if ( 'oz' == $weight_unit ) return 28.34;
        }
	}
}