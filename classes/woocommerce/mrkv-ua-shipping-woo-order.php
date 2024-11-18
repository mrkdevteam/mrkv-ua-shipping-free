<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOO_ORDER'))
{
	/**
	 * Class for setup WOO settings order
	 */
	class MRKV_UA_SHIPPING_WOO_ORDER
	{
		/**
		 * Constructor for WOO settings order
		 * */
		function __construct()
		{
			add_action('add_meta_boxes', array( $this, 'mrkv_ua_ship_add_meta_boxes' ));

			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'mrkv_ua_ship_order_editable_billing' ) );
			add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'mrkv_ua_ship_order_editable_billing' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'mrkv_ua_ship_save_admin_order_billing' ) );
		}

		public function mrkv_ua_ship_add_meta_boxes()
		{
			# Check hpos
	        if(class_exists( CustomOrdersTableController::class )){
	            $screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
	            ? wc_get_page_screen_id( 'shop-order' )
	            : 'shop_order';
	        }
	        else{
	            $screen = 'shop_order';
	        }

	        if (isset($_GET["post"]) || isset($_GET["id"])) 
	        {
            
	            $order_id = '';
	            if(isset($_GET["post"])){
	                $order_id = $_GET["post"];    
	            }
	            else{
	                $order_id = $_GET["id"];
	            }

	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
		    		$key = '';
		    		$current_shipping = '';

			    	foreach($order->get_shipping_methods() as $shipping)
		            {
		            	foreach($keys_shipping as $key_ship)
						{

							if(str_contains($shipping->get_method_id(), $key_ship))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
							if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
						}
		            }

		            if($key)
		            {
		            	add_meta_box('mrkv_ua_shipping_data_box', MRKV_UA_SHIPPING_LIST[$key]['name'], array( $this, 'mrkv_ua_shipping_add_plugin_meta_box' ), $screen, 'side', 'core');
		            }
		            else
		            {
		            	add_meta_box('mrkv_ua_shipping_data_box', __('MRKV UA Shipping', 'mrkv-ua-shipping'), array( $this, 'mrkv_ua_shipping_changer_add_plugin_meta_box' ), $screen, 'side', 'core');
		            }
	            }
	        }
		}

		public function mrkv_ua_shipping_changer_add_plugin_meta_box()
		{
			if (isset($_GET["post"]) || isset($_GET["id"])) 
	        {
            
	            $order_id = '';
	            if(isset($_GET["post"])){
	                $order_id = $_GET["post"];    
	            }
	            else{
	                $order_id = $_GET["id"];
	            }

	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	$available_methods = WC()->shipping->load_shipping_methods();
	            	?>
	            		<h3><?php echo esc_html__('Change shipping method', 'mrkv-ua-shipping'); ?></h3>
	            		<div class="mrkv-ua-shipping-line-choose-method">
	            			<select name="mrkv_shipping_method" id="mrkv_ua_shipping_method">
		            			<option value=""><?php echo esc_html__('Choose method', 'mrkv-ua-shipping'); ?></option>
		            			<?php 
		            				foreach ($available_methods as $method_id => $method) 
		            				{
		            					if(str_contains($method_id, 'mrkv_ua_shipping'))
		            					{
		            						echo '<option value="' . esc_attr($method_id) . '" ' . selected($current_shipping_method, $method_id, false) . '>';
									        echo esc_html($method->get_method_title());
									        echo '</option>';
		            					}
		            				}
		            			?>
		            		</select>
		            		<div class="mrkv-ua-shupping-change-method button"><?php echo esc_html__('Change method', 'mrkv-ua-shipping'); ?></div>
	            		</div>
	            		<script>
	            			jQuery(document).ready(function($) {
	            				jQuery('.mrkv-ua-shupping-change-method').on('click', function() 
	            				{
	            					var shippingMethod = jQuery('#mrkv_ua_shipping_method').val();
	            					var shippingMethodName = jQuery('#mrkv_ua_shipping_method option:selected').text();
                					var orderId = '<?php echo $order_id; ?>';

                					if(shippingMethod)
                					{
                						jQuery.ajax({
					                    url: '<?php echo admin_url( "admin-ajax.php" ) ?>',
					                    method: 'POST',
					                    data: {
					                        action: 'mrkv_update_shipping_method',
					                        order_id: orderId,
					                        shipping_method: shippingMethod,
					                        shipping_method_name: shippingMethodName,
					                    },
					                    success: function(response) 
					                    {
					                        location.reload();
					                    }
					                });
                					}
            					});
	            			});
	            		</script>
	            	<?php
	            }
	        }
		}

		public function mrkv_ua_shipping_add_plugin_meta_box()
		{
			if (isset($_GET["post"]) || isset($_GET["id"])) 
	        {
            
	            $order_id = '';
	            if(isset($_GET["post"])){
	                $order_id = $_GET["post"];    
	            }
	            else{
	                $order_id = $_GET["id"];
	            }

	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
		    		$key = '';
		    		$current_shipping = '';

			    	foreach($order->get_shipping_methods() as $shipping)
		            {
		            	foreach($keys_shipping as $key_ship)
						{

							if(str_contains($shipping->get_method_id(), $key_ship))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
							if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
							{
								$key = $key_ship;
								$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
							}
						}
		            }

		            $m_ua_active_plugins = get_option('m_ua_active_plugins');
	            	$enabled_shipping = ($m_ua_active_plugins && isset($m_ua_active_plugins[$key]['enabled']) && $m_ua_active_plugins[$key]['enabled']) == 'on' ? true : false;

		            if($key && $enabled_shipping)
		    		{
		    			$mrkv_ua_ship_invoice = $order->get_meta('mrkv_ua_ship_invoice_number');

		    			if(!$mrkv_ua_ship_invoice)
		    			{
		    				$mrkv_ua_ship_invoice = $order->get_meta(MRKV_UA_SHIPPING_LIST[$key]['old_ttn_slug']);
		    			}

			            if($mrkv_ua_ship_invoice){
			            	?>
			            		<h3><?php echo __('Invoice number', 'mrkv-ua-shipping'); ?></h3>
			            		<div class="mrkv_ua_ship_global__invoice"><?php echo $mrkv_ua_ship_invoice; ?></div>
			            		<hr class="mrkv-hr-sidebar">
			            		<h3><?php echo __('Invoice action', 'mrkv-ua-shipping'); ?></h3>
			            		<div class="mrkv_ua_invoice_action_list">
			            			<?php 
			            				$shipping_settings = get_option($key . '_m_ua_settings');
			            				switch($key)
			            				{
			            					case 'nova-poshta':
				            					?>
				            						<a target="blanc" href="<?php echo MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_pdf'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key'] ?>">
				            							<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/printer-icon.svg" alt="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>">
				            							<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>
			            							</a>
				            						<a target="blanc" href="<?php echo MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_sticker'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key'] ?>">
				            							<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/sticker-icon.svg" alt="<?php echo __('Print sticker', 'mrkv-ua-shipping'); ?>" title="<?php echo __('Print sticker', 'mrkv-ua-shipping'); ?>">
				            							<?php echo __('Print sticker', 'mrkv-ua-shipping'); ?>
			            							</a>
				            					<?php
			            					break;
			            					case 'ukr-poshta':
				            					if($current_shipping == 'mrkv_ua_shipping_ukr-poshta_international')
				            					{
				            						?>
				            						<a class="mrkv_ua_ship_print_inv_ukr" data-form="form-ukr-poshta-ttn-international">
				            							<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/printer-icon.svg" alt="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>">
			            								<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>
			            							</a>
				            						<?php
				            					}
				            					else
				            					{
				            						?>
					            						<a class="mrkv_ua_ship_print_inv_ukr" data-form="form-ukr-poshta-ttn">
					            							<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/printer-icon.svg" alt="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>">
				            								<?php echo __('Print invoice', 'mrkv-ua-shipping'); ?>
				            							</a>
					            					<?php
				            					}
			            					break;
			            				}
			            			?>
			            			<a class="mrkv_ua_ship_send_remove_ttn">
	        							<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/trash-icon.svg" alt="<?php echo __('Remove ttn', 'mrkv-ua-shipping'); ?>" title="<?php echo __('Remove ttn', 'mrkv-ua-shipping'); ?>">
	        							<?php echo __('Remove ttn', 'mrkv-ua-shipping'); ?>
	    							</a>
			            		</div>
			            	<?php
			            }
			            else{
			            	?>
			            	<a>
			            		<div data-method="<?php echo $current_shipping; ?>" data-ship="<?php echo $key; ?>" data-order-id="<?php echo $order->get_id(); ?>" class="mrkv_ua_ship_global_create__invoice">
			            			<img src="<?php echo MRKV_UA_SHIPPING_IMG_URL . '/global'; ?>/add-square-icon.svg" alt="<?php echo $order->get_shipping_method(); ?>" title="<?php echo $order->get_shipping_method(); ?>">
			            			<span><?php echo __('Create Invoice', 'mrkv-ua-shipping'); ?></span>
			            			<div class="mrkv_ua_ship_create_invoice__loader"></div>
			            		</div>
			            	</a>
			            	<?php
			            }

			            ?>
			            	<hr class="mrkv-hr-sidebar">
			            	<h3><?php echo __('Custom Invoice number', 'mrkv-ua-shipping'); ?></h3>
		            		<input type="text" name="custom_invoice_number" minlength="13" value="<?php echo $mrkv_ua_ship_invoice; ?>">
		            		<div class="mrkv_ua_ship_custom_invoice button">
		            			<?php echo __('Save Invoice', 'mrkv-ua-shipping'); ?>
					            <div class="mrkv_ua_ship_create_invoice__loader"></div>
		            		</div>
		            		<hr class="mrkv-hr-sidebar">
			            	<h3><?php echo __('Change address', 'mrkv-ua-shipping'); ?></h3>
			            	<div class="mrkv_ua_ship_edit_data">
			            		<input type="hidden" name="mrkv_order_id" value="<?php echo $order_id; ?>">
			            		<input type="hidden" name="mrkv_current_shipping" value="<?php echo $current_shipping; ?>">
			            		<input type="hidden" name="mrkv_current_shipping_key" value="<?php echo $key; ?>">
					            <?php
						            foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $id => $field_val)
				            		{
				            			if(isset($field_val['replace']))
				            			{
				            				if($field_val['replace'] == '_city')
				            				{
									            $default_value = $order->get_shipping_city();
					    					}
					    					elseif($field_val['replace'] == '_state')
					    					{
					    						$default_value = $order->get_shipping_state();
					    					}
					    					elseif($field_val['replace'] == '_address_1')
					    					{
					    						$default_value = $order->get_shipping_address_1();
					    					}
					    					elseif($field_val['replace'] == '_postcode')
					    					{
					    						$default_value = $order->get_shipping_postcode();
					    					}
					    					elseif($field_val['replace'] == '_address_2')
					    					{
					    						$default_value = $order->get_shipping_address_2();
					    					}
					    					else
					    					{
					    						$default_value = $order->get_meta($current_shipping . $id);

					    						if(!$default_value && isset($field_val['old_slug']))
					    						{
					    							$default_value = $order->get_meta($field_val['old_slug']);
					    						}
					    					}

				            				$field_val['default'] = $default_value;
				            			}

				            			if($field_val['default'] && $field_val['type'] == 'select')
					        			{
					        				$field_val['options'][$default_value] = $default_value;
					        			}

										woocommerce_form_field($current_shipping . $id, $field_val);
						            }

					            ?>
					            <div class="mrkv_ua_shipping_change_field_address button">
					            	<?php echo __('Save Field', 'mrkv-ua-shipping'); ?>
					            	<div class="mrkv_ua_ship_create_invoice__loader"></div>
					            </div>
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

		public function mrkv_ua_ship_order_editable_billing($order)
		{
			$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
    		$key = '';
    		$current_shipping = '';

	    	foreach($order->get_shipping_methods() as $shipping)
            {
            	foreach($keys_shipping as $key_ship)
				{

					if(str_contains($shipping->get_method_id(), $key_ship))
					{
						$key = $key_ship;
						$current_shipping = $shipping->get_method_id();
					}
				}
            }

            if($key && $current_shipping)
            {
            	foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $key_field => $field_value)
            	{
            		if(isset($field_value['order_edit']) && $field_value['order_edit'])
            		{
            			$billing_data    = $order->get_meta( $current_shipping . $key_field );

					    printf('<div class="address"><p%s><strong>%s:</strong> %s</p></div>
					        <div class="edit_address">', 
					        '',
					        $field_value['label'],
					        $billing_data 
					    );

					    woocommerce_wp_text_input( array(
					        'id'            => $current_shipping . $key_field,
					        'label'         => $field_value['label'],
					        'wrapper_class' => 'form-field-wide',
					        'class'         => '',
					        'style'         => 'width:100%',
					        'value'         => $billing_data,
					    ) );

					    echo '</div>';
            		}
            	}
            }
		}

		public function mrkv_ua_ship_save_admin_order_billing($order_id)
		{
			$order = wc_get_order($order_id);

			if($order)
			{
				$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
	    		$key = '';
	    		$current_shipping = '';

		    	foreach($order->get_shipping_methods() as $shipping)
	            {
	            	foreach($keys_shipping as $key_ship)
					{

						if(str_contains($shipping->get_method_id(), $key_ship))
						{
							$key = $key_ship;
							$current_shipping = $shipping->get_method_id();
						}
					}
	            }

	            if($key && $current_shipping)
	            {
	            	$order = wc_get_order( $order_id );

	            	if($order)
	            	{
	            		foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $key_field => $field_value)
		            	{
		            		if(isset($field_value['order_edit']) && $field_value['order_edit'])
		            		{
							    if ( isset($_POST[ $current_shipping . $key_field ]) ) 
							    {
							        $order->update_meta_data( $current_shipping . $key_field, wc_clean( wp_unslash( $_POST[ $current_shipping . $key_field ] ) ) );
							        $order->save();
							    }
		            		}
		            	}
	            	}
	            }
			}
		}
	}
}