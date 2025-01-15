<?php

$translate_labels = array(
	'nova-poshta' => array(
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
						'label' => __('Warehouse/poshtomat', 'mrkv-ua-shipping'),
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
						'placeholder' => __('Enter the street...', 'mrkv-ua-shipping'),
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
			),
			'mrkv_ua_shipping_ukr-poshta_international' => array(
				'class' => 'MRKV_UA_SHIPPING_UKR_POSHTA_INTERNATIONAL',
				'slug' => 'mrkv_ua_shipping_ukr-poshta_international',
				'filename' => 'mrkv-ua-shipping-method-ukr-poshta-international',
				'checkout_fields' => array(
					'_postcode' => array(
						'type' => 'text',
						'autocomplete' => 'on',
						'required' => true,
						'label' => __('Postal code', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the postal code', 'mrkv-ua-shipping'),
						'replace' => '_postcode'
					),
					'_region' => array(
						'type' => 'text',
						'autocomplete' => 'on',
						'required' => true,
						'label' => __('Region', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the region', 'mrkv-ua-shipping'),
						'replace' => '_state'
					),
					'_city' => array(
						'type' => 'text',
						'autocomplete' => 'on',
						'required' => true,
						'label' => __('City', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the city', 'mrkv-ua-shipping'),
						'replace' => '_city'
					),
					'_street' => array(
						'type' => 'text',
						'autocomplete' => 'on',
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the street', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
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
);