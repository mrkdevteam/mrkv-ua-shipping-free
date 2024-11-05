<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_AJAX_NOVA'))
{
	/**
	 * Class for setup shipping methods ajax nova poshta
	 */
	class MRKV_UA_SHIPPING_AJAX_NOVA
	{
		/**
		 * Constructor for plugin shipping methods ajax nova poshta
		 * */
		function __construct()
		{
			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_area', array($this, 'get_nova_poshta_area') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_area', array($this, 'get_nova_poshta_area') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_city', array($this, 'get_nova_poshta_city') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_city', array($this, 'get_nova_poshta_city') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_warehouse', array($this, 'get_nova_poshta_warehouse') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_warehouse', array($this, 'get_nova_poshta_warehouse') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_street', array($this, 'get_nova_poshta_street') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_street', array($this, 'get_nova_poshta_street') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_sender_get_address_ref', array($this, 'get_sender_get_address_ref') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_sender_get_address_ref', array($this, 'get_sender_get_address_ref') );
		}

		/**
		 * Get Nova poshta Area
		 * */
		public function get_nova_poshta_area()
		{
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? $_POST['name'] : '';

			$args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getAreas',
            	'methodProperties' => array(
            		'FindByString' => $key_search .'%',
            		'Limit' => '10'
            	)
	        );

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['Description']
	        		);
	        	}

	        	# Return object
	        	echo json_encode($areas);
	        }
	        else
	        {
	        	echo json_encode(array(array(
	        		'value' => 'none',
        			'label' => __('No results for your request', 'mrkv-ua-shipping')
	        	)));
	        }

			wp_die();
		}

		/**
		 * Get Nova poshta City
		 * */
		public function get_nova_poshta_city()
		{
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? $_POST['name'] : '';

			$args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getCities',
            	'methodProperties' => array(
            		'FindByString' => $key_search .'%',
            		'Limit' => '50'
            	)
	        );

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['Description'],
	        			'area' => $area['AreaDescription']
	        		);
	        	}

	        	# Return object
	        	echo json_encode($areas);
	        }
	        else
	        {
	        	echo json_encode(array(array(
	        		'value' => 'none',
        			'label' => __('No results for your request', 'mrkv-ua-shipping')
	        	)));
	        }

			wp_die();
		}

		/**
		 * Get Nova poshta Warehouse
		 * */
		public function get_nova_poshta_warehouse()
		{
			$settings_method = get_option('nova-poshta_m_ua_settings');
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings_method);

			$key_search = isset($_POST['name']) ? $_POST['name'] : '';
			$city_ref = isset($_POST['ref']) ? $_POST['ref'] : '';
			$warehouse_type = isset($_POST['warehouse_type']) ? $_POST['warehouse_type'] : '';
			$search_by = isset($_POST['search_by']) ? $_POST['search_by'] : '';
			$exclude_post = '';
			
			if($warehouse_type == 'none'){
				$exclude_post = true;
				$warehouse_type = '';
			}

			$args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getWarehouses',
            	'methodProperties' => array(
            		'CityRef' => $city_ref,
            	)
	        );

	        if($search_by)
	        {
	        	$args['methodProperties']['FindByString'] = '';
	        	$args['methodProperties']['WarehouseId'] = $key_search;
	        }
	        else
	        {
	        	$args['methodProperties']['FindByString'] = '%' . $key_search .'%';
	        }

	        if($warehouse_type)
	        {
	        	$args['methodProperties']['TypeOfWarehouseRef'] = $warehouse_type;
	        }

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();
	        	$skip_weight = isset($_POST['warehouse_type']) ? true : false;

	        	$areas[] = array(
        			'value' => '',
        			'label' => __('Choose the warehouse', 'mrkv-ua-shipping'),
        			'number' => ''
        		);

	        	if($skip_weight)
	        	{
	        		$weight = 0;
		        	$volume_weight = 0.00;
	                $dimension_unit = get_option( 'woocommerce_dimension_unit' );
	                foreach(WC()->cart->get_cart() as $cart_item => $cart_value)
	                {
	                    $item_length = ( null !== $cart_value['data']->get_length() && $cart_value['data']->get_length()) ? wc_get_dimension( $cart_value['data']->get_length(), 'cm', $dimension_unit ) : 0.00;
	                    $item_width = ( null !== $cart_value['data']->get_width() && $cart_value['data']->get_width()) ? wc_get_dimension( $cart_value['data']->get_width(), 'cm', $dimension_unit ) : 0.00;
	                    $item_height = ( null !== $cart_value['data']->get_height() && $cart_value['data']->get_height()) ? wc_get_dimension( $cart_value['data']->get_height(), 'cm', $dimension_unit ) : 0.00;

	                    $volume_weight += $item_length * $item_width * $item_height / 4000;
	                }

	                if((!$volume_weight) && isset($settings_method['shipment']['volume']) && $settings_method['shipment']['volume'])
	                {
	                    $volume_weight = floatval($settings_method['shipment']['volume']);
	                }

	                $weight_coef = 1;

	                $weight_unit = get_option('woocommerce_weight_unit');

		            if ( 'g' == $weight_unit ) $weight_coef = 0.001;
		            if ( 'kg' == $weight_unit ) $weight_coef = 1;
		            if ( 'lbs' == $weight_unit ) $weight_coef = 0.45359;
		            if ( 'oz' == $weight_unit ) $weight_coef = 0.02834;

	                $actual_weight = ( WC()->cart->cart_contents_weight > 0 ) ? WC()->cart->cart_contents_weight * $weight_coef : 0.00;

	                if((!$actual_weight) && isset($settings_method['shipment']['volume']) && $settings_method['shipment']['volume'])
	                {
	                    $actual_weight = floatval($settings_method['shipment']['volume']);
	                }

	                $weight = max( $actual_weight, $volume_weight );
	        	}

	        	foreach($obj['data'] as $area)
	        	{
	        		$skip = false;

	        		if($skip_weight && intval($area['TotalMaxWeightAllowed']) > 0 && intval($area['TotalMaxWeightAllowed']) < $weight)
	        		{
	        			$skip = true;
	        		}
	        		elseif($skip_weight && intval($area['PlaceMaxWeightAllowed']) > 0 && intval($area['PlaceMaxWeightAllowed']) < $weight)
	        		{
						$skip = true;
	        		}

	        		if($exclude_post && $area['TypeOfWarehouse'] == 'f9316480-5f2d-425d-bc2c-ac7cd29decf0')
					{
						$skip = true;
					}

	        		if(!$skip)
	        		{
	        			$areas[] = array(
		        			'value' => $area['Ref'],
		        			'label' => $area['Description'],
		        			'number' => $area['Number']
		        		);
	        		}
	        	}

	        	# Return object
	        	echo json_encode($areas);
	        }
	        else
	        {
	        	echo json_encode(array(array(
	        		'value' => 'none',
        			'label' => __('No results for your request', 'mrkv-ua-shipping')
	        	)));
	        }

			wp_die();
		}

		/**
		 * Get Nova poshta Street
		 * */
		public function get_nova_poshta_street()
		{
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? $_POST['name'] : '';
			$city_ref = isset($_POST['ref']) ? $_POST['ref'] : '';

			$args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getStreet',
            	'methodProperties' => array(
            		'FindByString' => '%' . $key_search .'%',
            		'CityRef' => $city_ref,
            		'Limit' => '10'
            	)
	        );

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['Description']
	        		);
	        	}

	        	# Return object
	        	echo json_encode($areas);
	        }
	        else
	        {
	        	echo json_encode(array(array(
	        		'value' => 'none',
        			'label' => __('No results for your request', 'mrkv-ua-shipping')
	        	)));
	        }

			wp_die();
		}

		/**
	     * Get Sender Address Ref
	     * */
	    public function get_sender_get_address_ref()
	    {
	    	require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-sender-nova-poshta.php';
			$mrkv_sender_object_nova_poshta = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($mrkv_object_nova_poshta);

			$sender_street_ref = isset($_POST['sender_street_ref']) ? $_POST['sender_street_ref'] : '';
			$sender_building_number = isset($_POST['sender_building_number']) ? $_POST['sender_building_number'] : '';
			$sender_flat = isset($_POST['sender_flat']) ? $_POST['sender_flat'] : '';

	        # Send request
	        $ref = $mrkv_sender_object_nova_poshta->get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat);
	        $ref = str_replace('"', "", $ref);

	        if($ref)
	        {
	        	# Return object
	        	echo json_encode($ref);
	        }

	        wp_die();
	    }
	}
}