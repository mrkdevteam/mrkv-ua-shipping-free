<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_NOVA_POSHTA_ADDRESS'))
{
    /**
     * Class for setup nova poshta warehouse method
     */
    class MRKV_UA_SHIPPING_NOVA_POSHTA_ADDRESS extends WC_Shipping_Method
    {
        public function __construct($instance_id = 0)
        {
            # Set instance id
            $this->instance_id = absint( $instance_id );
            parent::__construct( $instance_id );

            # Set main fields
            $this->id = 'mrkv_ua_shipping_nova-poshta_address';
            $this->method_title = __( 'Nova Poshta Address', 'mrkv-ua-shipping' );
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
                    'default' => __('Nova Poshta Address', 'mrkv-ua-shipping')
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
                    'placeholder' => __('FREE to Nova Poshta Address', 'mrkv-ua-shipping'),
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
    }
}