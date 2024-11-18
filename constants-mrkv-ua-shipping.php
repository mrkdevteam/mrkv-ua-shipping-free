<?php
# Get plugin data
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugData = get_plugin_data(MRKV_UA_SHIPPING_PLUGIN_FILE);

# Constans 

# Directories
define('MRKV_UA_SHIPPING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP', plugin_dir_path(__FILE__) . 'templates');
define('MRKV_UA_SHIPPING_PLUGIN_PATH_SHIP', plugin_dir_path(__FILE__) . 'classes/shipping_methods');

# Links
define('MRKV_UA_SHIPPING_PLUGIN_DIR', plugin_dir_url(__FILE__));
define('MRKV_UA_SHIPPING_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets');
define('MRKV_UA_SHIPPING_IMG_URL', plugin_dir_url(__FILE__) . 'assets/images');

# Data
define('MRKV_UA_SHIPPING_NAME', $plugData['Name']);
define('MRKV_UA_SHIPPING_PLUGIN_VERSION', $plugData['Version']);
define('MRKV_UA_SHIPPING_PLUGIN_TEXT_DOMAIN', 'mrkv-ua-shipping');

define('MRKV_UA_SHIPPING_LIST', array(
	'nova-poshta' => array(
		'name' => __('Nova Poshta', 'mrkv-ua-shipping'),
		'description' => __('Add shipping method, calculate shipping costs, create and manage shipments, both manually and automatically. Using API 2.0 and connects directly to the Nova Poshta server for fast and secure user experience.', 'mrkv-ua-shipping'),
		'invoice_class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_INVOICE',
		'api_class' => 'MRKV_UA_SHIPPING_API_NOVA_POSHTA',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_NOVA_POSHTA',
		'pages' => array(
			'invoices' => __('My shipments', 'mrkv-ua-shipping')
		),
		'invoice_links' => array(
			'invoice_pdf' => 'https://my.novaposhta.ua/orders/printDocument/orders[]/',
			'invoice_sticker' => 'https://my.novaposhta.ua/orders/printMarkings/orders[]/',
			'invoice_link_end' => '/type/pdf/apiKey/',
		),
		'old_slugs' => array(
			'mrkv_ua_shipping_nova-poshta' => 'nova_poshta_shipping_method',
			'mrkv_ua_shipping_nova-poshta_poshtamat' => 'nova_poshta_shipping_method_poshtomat',
			'mrkv_ua_shipping_nova-poshta_address'=> 'npttn_address_shipping_method'
		),
		'old_ttn_slug' => 'novaposhta_ttn',
		'method' => array(
			'mrkv_ua_shipping_nova-poshta' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA',
				'slug' => 'mrkv_ua_shipping_nova-poshta',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta',
				'checkout_fields' => array(
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state'
					),
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_warehouse_ref',
						'old_slug' => 'np_warehouse_ref'
					),
					'_warehouse_number' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_postcode'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_poshtamat' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_POSHTAMAT',
				'slug' => 'mrkv_ua_shipping_nova-poshta_poshtamat',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-poshtamat',
				'checkout_fields' => array(
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state'
					),
					'_name' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the poshtomat', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Poshtomat', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_warehouse_ref',
						'old_slug' => 'np_warehouse_ref'
					),
					'_number' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_postcode'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_address' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_ADDRESS',
				'slug' => 'mrkv_ua_shipping_nova-poshta_address',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-address',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						'exclude' => true,
						'order_edit' => true
					),
					'_patronymic_enabled' => array(
						'type' => 'hidden',
						'autocomplete' => 'on'
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state'
					),
					'_street' => array(
						'type' => 'text',
						'autocomplete' => 'on',
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter three or more letters of the street name', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_street_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_street_ref'
					),
					'_house' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of house', 'mrkv-ua-shipping'),
						'replace' => '_address_2'
					),
					'_flat' => array(
						'type' => 'text',
						'required' => false,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_address_ref'
					)
				)
			)
		)
	),
	'ukr-poshta' => array(
		'name' => __('UkrPoshta', 'mrkv-ua-shipping'),
		'description' => __('Works with both domestic and international shipments. Add shipping method, calculate shipping costs, create and manage shipments, both manually and automatically. Get 10% off when creating shipments with our plugin.', 'mrkv-ua-shipping'),
		'api_class' => 'MRKV_UA_SHIPPING_API_UKR_POSHTA',
		'invoice_class' => 'MRKV_UA_SHIPPING_UKR_POSHTA_INVOICE',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_UKR_POSHTA',
		'pages' => array(
			'invoices' => __('My shipments', 'mrkv-ua-shipping')
		),
		'invoice_links' => array(
			'invoice_pdf' => 'https://www.ukrposhta.ua/ecom/0.0.1/shipments/',
			'invoice_sticker' => '',
			'invoice_link_end' => '/sticker?token=',
		),
		'old_slugs' => array(
			'mrkv_ua_shipping_ukr-poshta' => 'ukrposhta_shippping',
			'mrkv_ua_shipping_ukr-poshta_address' => 'ukrposhta_address_shippping',
		),
		'old_ttn_slug' => 'ukrposhta_ttn',
		'method' => array(
			'mrkv_ua_shipping_ukr-poshta' => array(
				'class' => 'MRKV_UA_SHIPPING_UKR_POSHTA',
				'slug' => 'mrkv_ua_shipping_ukr-poshta',
				'filename' => 'mrkv-ua-shipping-method-ukr-poshta',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						'order_edit' => true,
						'exclude' => true,
					),
					'_patronymic_enabled' => array(
						'type' => 'hidden',
						'autocomplete' => 'on'
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state'
					),
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_postcode'
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_address_ref'
					)
				)
			),
			'mrkv_ua_shipping_ukr-poshta_address' => array(
				'class' => 'MRKV_UA_SHIPPING_UKR_POSHTA_ADDRESS',
				'slug' => 'mrkv_ua_shipping_ukr-poshta_address',
				'filename' => 'mrkv-ua-shipping-method-ukr-poshta-address',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						'order_edit' => true
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state'
					),
					'_area_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_state_id'
					),
					'_district_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_district_id'
					),
					'_street' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the street', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_street_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_street_ref'
					),
					'_house' => array(
						'type' => 'select',
						'autocomplete' => 'on',
						'options' => array('' => __('Choose the house', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'replace' => '_address_2'
					),
					'_house_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_postcode'
					),
					'_flat' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'on',
						'replace' => '_address_ref'
					)
				)
			)
		)
	)
));