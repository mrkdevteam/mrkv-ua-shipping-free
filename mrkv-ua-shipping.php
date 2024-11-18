<?php
/**
 * Plugin Name: Morkva UA Shipping
 * Plugin URI: https://morkva.co.ua/product-category/plugins/
 * Description: 2-in-1: Nova Poshta and Ukrposhta delivery services. Create shipping methods and shipments easily
 * Version: 1.0.14
 * Author: MORKVA
 * Text Domain: mrkv-ua-shipping
 * Domain Path: /i18n/
 * WC requires at least: 3.8
 * WC tested up to: 7.8
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

# Global File
define('MRKV_UA_SHIPPING_PLUGIN_FILE', __FILE__);

# Include CONSTANTS
require_once 'constants-mrkv-ua-shipping.php';

# Check if Woo plugin activated
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
{
    # Include plugin settings
    require_once 'classes/mrkv-ua-shipping-run.php'; 

    # Setup plugin settings
    new MRKV_UA_SHIPPING_RUN();
}