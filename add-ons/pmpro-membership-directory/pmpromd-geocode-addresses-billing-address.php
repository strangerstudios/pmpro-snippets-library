<?php
/**
 * Geocodes member addresses based on billing address fields in order meta
 * 
 * Run /wp-admin/?pmpromm_process_users=true to run the script.
 * 
 * Change line 36 to increase batch sizes. Note that the Google Maps Geocode API
 * has a daily limit of 2 000 requests.
 * 
 * title: Geocode member addresses based on billing address fields in order meta
 * layout: snippet-example
 * collection: pmpro-member-directory
 * category: geocoding, maps
 * link: https://www.paidmembershipspro.com/style-your-membership-map/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 */

function mypmpromm_batch_process_addresses_override(){

	if( !empty( $_REQUEST['pmpromm_process'] ) ){
		mypmpromm_batch_process_addresses();
	}

}
add_action( 'admin_init', 'mypmpromm_batch_process_addresses_override' );

function mypmpromm_batch_process_addresses(){

	global $wpdb;

	$batch_size = 30; //Number of members to geocode at a time

	//Original Membership Maps API key option
	$api_key = get_option( 'pmpro_pmpromm_api_key' );

	if( empty( $api_key ) ) {
		//New Member Directory API key option
		$api_key = get_option( 'pmpro_pmpromd_maps_api_key' );
	}

	if( empty( $api_key ) ) {
		//No API key found, exit
		exit('No API key found');
	}   

	$sql = "
		SELECT ord.user_id, ord.billing_street, ord.billing_city, ord.billing_state, ord.billing_country, ord.billing_zip 
		FROM $wpdb->pmpro_membership_orders ord
		LEFT JOIN $wpdb->usermeta um_lat 
			ON um_lat.user_id = ord.user_id 
			AND um_lat.meta_key = 'pmpro_lat'
		LEFT JOIN $wpdb->usermeta um_pin 
			ON um_pin.user_id = ord.user_id 
			AND um_pin.meta_key = 'pmpromd_pin_location'
		WHERE (um_lat.user_id IS NULL OR um_lat.meta_value IS NULL)
		AND (um_pin.user_id IS NULL OR um_pin.meta_value IS NULL)
		AND ord.billing_street != '' 
		AND ord.status = 'success'
		LIMIT $batch_size
	";

	$results = $wpdb->get_results( $sql );

	if( !$results ) {
		exit('No members found to process');
	}

	foreach( $results as $result ){

		$member_address = array(
			'street' 	=> $result->billing_street,
			'city' 		=> $result->billing_city,
			'state' 	=> $result->billing_state,
			'country'   => $result->billing_country,
			'zip' 		=> $result->billing_zip
		);

		if( function_exists( 'pmpromd_geocode_map_address' ) ) {
			//New version of Member Directory active 2.1+, use its geocode function
			$coordinates = pmpromd_geocode_map_address( $member_address );
			
			//Geocode was successful, add to user meta                
			if( is_array( $coordinates ) ){
				
				$member_address['optin'] = true;
				
				$member_address['latitude'] = $coordinates['lat'];
				$member_address['longitude'] = $coordinates['lng'];
				
				update_user_meta( $result->user_id, 'pmpromd_pin_location', $member_address );

				update_user_meta( $result->user_id, 'pmpromd_map_optin', true );
				update_user_meta( $result->user_id, 'pmpromd_street_name', $member_address['street'] );
				update_user_meta( $result->user_id, 'pmpromd_city', $member_address['city'] );
				update_user_meta( $result->user_id, 'pmpromd_state', $member_address['state'] );
				update_user_meta( $result->user_id, 'pmpromd_zip', $member_address['zip'] );
				update_user_meta( $result->user_id, 'pmpromd_country', $member_address['country'] );

			}   
		} else if( function_exists( 'pmpromm_geocode_address' ) ) {
			//Use older geocode function from Membership Maps
			$coordinates = pmpromm_geocode_address( $member_address );
			//Geocode was successful, add to user meta
			if( is_array( $coordinates ) ){
				update_user_meta( intval( $result->user_id ), 'pmpro_lat', $coordinates['lat'] );
				update_user_meta( intval( $result->user_id ), 'pmpro_lng', $coordinates['lng'] );
			}
			
		}
		

	}

	exit('End');
	
}