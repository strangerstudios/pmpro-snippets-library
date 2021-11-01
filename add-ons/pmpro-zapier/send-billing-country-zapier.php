<?php
/**
 * Send the billing country through to Zapier for added and updated order actions.
 * 
 * title: Send Billing Country to Zapier
 * layout: snippet
 * collection: add-ons, pmpro-zapier
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmproz_send_country_code( $data, $order, $user_id ){

	if( !empty( $_REQUEST['bcountry'] ) ){

		global $pmpro_countries;

		if( isset( $pmpro_countries[$_REQUEST['bcountry']] ) ){
			
			$data['country'] = $pmpro_countries[$_REQUEST['bcountry']];

		}

	} else {

		$data['country'] = 'none';

	}
	
	return $data;
}
add_filter( 'pmproz_added_order_data', 'mypmproz_send_country_code', 10, 3 );
add_filter( 'pmproz_updated_order_data', 'mypmproz_send_country_code', 10, 3 );