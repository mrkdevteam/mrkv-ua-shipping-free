<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_CALCULATE_UKR_POSHTA'))
{
	/**
	 * Class for setup ukr poshta api calculate
	 */
	class MRKV_UA_SHIPPING_CALCULATE_UKR_POSHTA
	{
		/**
		 * @param object MRKV_UA_SHIPPING_API_UKR_POSHTA
		 * */
		private $ukr_poshta_api;

		/**
		 * Constructor for nova poshta api sender
		 * */
		function __construct($ukr_poshta_api)
		{
			# Set fields
			$this->ukr_poshta_api = $ukr_poshta_api;
		}

		/**
		 * Get all sender contacts list
		 * @return 
		 * */
		public function calculate_shipping_cost($address_from, $address_to, $weight, $service_type, $cost, $cargo_type, $length) 
	    {
	    	# Set arguments
	        $args = array(
	            "weight" => $weight,
		        "length" => $length,
		        "addressFrom" => array(
		            "postcode"  => $address_from
		        ),
		        "addressTo" => array(
		            "postcode"  => $address_to
		        ),
		        "type"  => $cargo_type,
		        "deliveryType"  => $service_type,
		        "declaredPrice" => $cost,
		        "validate" => 'true'
	        );

	        # Send request
	        $obj = $this->ukr_poshta_api->send_post_request_curl('ecom/0.0.1/domestic/delivery-price', 'POST', $args );

	        if(isset($obj['deliveryPrice']))
	        {
	        	# Return object
	        	return $obj['deliveryPrice'];
	        }
	        else
	        {
	        	return '';
	        }
	    }

	    /**
		 * Get all sender contacts list
		 * @return 
		 * */
		public function calculate_shipping_internal_cost($args) 
	    {
	        # Send request
	        $obj = $this->ukr_poshta_api->send_post_request_curl('ecom/0.0.1/international/delivery-price', 'POST', $args );

	        if(isset($obj['deliveryPrice']))
	        {
	        	# Return object
	        	return $obj['deliveryPrice'];
	        }
	        else
	        {
	        	return '';
	        }
	    }
	}
}