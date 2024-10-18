<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOO_ORDERS'))
{
	/**
	 * Class for setup WOO settings orders
	 */
	class MRKV_UA_SHIPPING_WOO_ORDERS
	{
		/**
		 * Constructor for WOO settings orders
		 * */
		function __construct()
		{
			if(class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) &&  OrderUtil::custom_orders_table_usage_is_enabled()){
	            add_filter('manage_woocommerce_page_wc-orders_columns', array( $this, 'mrkv_ua_ship_woo_custom_column' ));
	            add_action('manage_woocommerce_page_wc-orders_custom_column', array( $this, 'mrkv_ua_ship_woo_column_get_data_hpos' ), 20, 2 );
	        }
	        else{
	            add_filter('manage_edit-shop_order_columns', array( $this, 'mrkv_ua_ship_woo_custom_column' ));
	            add_action('manage_shop_order_posts_custom_column', array( $this, 'mrkv_ua_ship_woo_column_get_data' ));
	        }

	        add_action('admin_footer', array($this, 'mrkv_ua_ship_form_create_invoice'));
		}

		public function mrkv_ua_ship_form_create_invoice()
		{
			global $pagenow, $typenow;

	    	if(($pagenow == 'admin.php' || $pagenow == 'post.php') && ('shop_order' === $typenow || (isset($_GET['page']) && $_GET['page'] == 'wc-orders')) || (isset($_GET['post_type']) && $_GET['post_type'] == 'shop_order'))
	    	{
				# Include template
				include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/orders/mrkv-ua-ship-popup.php';
			}
		}

		/**
	     * Creating custom column at woocommerce order page
	     * @param array Columns
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_custom_column($columns)
	    {
	        $columns['mrkv_ua_invoice'] = __('Invoice', 'mrkv-ua-shipping');
	        $columns['mrkv_ua_shipping'] = __('Shipping', 'mrkv-ua-shipping');

	        return $columns;
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_column_get_data($column)
	    {
	        global $post;
	        $the_order = '';

	        if($post && $post->ID)
	        {
	        	$the_order = wc_get_order( $post->ID );
	        }

	        $this->mrkv_ua_ship_columns_content($column, $the_order);
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_column_get_data_hpos($column, $the_order)
	    {
	    	$this->mrkv_ua_ship_columns_content($column, $the_order);
	    }

	    private function mrkv_ua_ship_columns_content($column, $the_order)
	    {
	    	if($the_order)
	    	{
	    		$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
	    		$key = '';
	    		$current_shipping = '';

		    	foreach($the_order->get_shipping_methods() as $shipping)
	            {
	            	foreach($keys_shipping as $key_ship)
					{
						$current_shipping = $shipping->get_method_id();

						if(str_contains($shipping->get_method_id(), $key_ship))
						{
							$key = $key_ship;
						}
						if(in_array($current_shipping, MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
						{
							$key = $key_ship;
							$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
						}
					}
	            }

	            $m_ua_active_plugins = get_option('m_ua_active_plugins');
	            $enabled_shipping = ($m_ua_active_plugins && isset($m_ua_active_plugins[$key]['enabled']) && $m_ua_active_plugins[$key]['enabled']) == 'on' ? true : false;

		    	if ($column == 'mrkv_ua_invoice') 
		    	{
		    		if($key && $enabled_shipping)
		    		{
		    			$mrkv_ua_ship_invoice = $the_order->get_meta('mrkv_ua_ship_invoice_number');

		    			if(!$mrkv_ua_ship_invoice)
		    			{
		    				$mrkv_ua_ship_invoice = $the_order->get_meta(MRKV_UA_SHIPPING_LIST[$key]['old_ttn_slug']);
		    			}

			            if($mrkv_ua_ship_invoice){
			            	?>
			            		<div class="mrkv_ua_ship_global__invoice"><?php echo $mrkv_ua_ship_invoice; ?></div>
			            	<?php
			            }
			            else{
			            	?>
			            	<a>
			            		<div data-method="<?php echo $current_shipping; ?>" data-ship="<?php echo $key; ?>" data-order-id="<?php echo $the_order->get_id(); ?>" class="mrkv_ua_ship_global_create__invoice">
			            			<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/add-square-icon.svg" alt="<?php echo $the_order->get_shipping_method(); ?>" title="<?php echo $the_order->get_shipping_method(); ?>">
			            			<span><?php echo __('Create', 'mrkv-ua-shipping'); ?></span>
			            			<div class="mrkv_ua_ship_create_invoice__loader"></div>
			            		</div>
			            	</a>
			            	<?php
			            }
		    		}
		    		else
		    		{
		    			?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<span>-</span>
	            			</div>
	            		<?php
		    		}
		        }

		        if ($column == 'mrkv_ua_shipping') 
		        {
		            if($key && $enabled_shipping)
	            	{
	            		?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/' . $key; ?>/logo-mini.png" alt="<?php echo $the_order->get_shipping_method(); ?>" title="<?php echo $the_order->get_shipping_method(); ?>">
	            			</div>
	            		<?php
	            	}
	            	else
	            	{
	            		?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<span>-</span>
	            			</div>
	            		<?php
	            	}
		        }
	    	}
	    }
	}
}