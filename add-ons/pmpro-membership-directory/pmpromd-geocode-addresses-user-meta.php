<?php
/**
 * Geocodes member addresses based on user meta
 * 
 * Run /wp-admin/?pmpromm_process_users=true to run the script.
 * 
 * Note that the Google Maps Geocode API has a daily limit of 2 000 requests.
 * 
 * title: Geocode member addresses based on user meta
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

function mypmpromm_batch_process_addresses_override_users(){

	if( !empty( $_REQUEST['pmpromm_process_users'] ) ){
		mypmpromm_batch_process_addresses_users();
	}

}
add_action( 'admin_init', 'mypmpromm_batch_process_addresses_override_users' );

function mypmpromm_batch_process_addresses_users(){

	global $wpdb;

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

	$users = get_users();

	if( empty( $users ) ){
		//No users found, exit
		exit( 'No users found' );
	}
	
	foreach( $users as $result ){

		$member_address = array(
			'street' 	=> get_user_meta( $result->ID, 'pmpro_baddress1', true ).' '.get_user_meta( $result->ID, 'pmpro_baddress2', true ),
			'city' 		=> get_user_meta( $result->ID, 'pmpro_bcity', true ),
			'state' 	=> get_user_meta( $result->ID, 'pmpro_bstate', true ),
			'country'   => get_user_meta( $result->ID, 'pmpro_bcountry', true ),
			'zip' 		=> get_user_meta( $result->ID, 'pmpro_bzipcode', true )
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