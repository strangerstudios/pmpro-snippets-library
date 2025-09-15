<?php
/**
 * Geocodes member addresses based on user meta
 * 
 * Run /wp-admin/?pmpromd_process_users=true to run the script.
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
function mypmpromd_batch_process_addresses_override_users(){

	if ( ! empty( $_REQUEST['pmpromd_process_users'] ) && current_user_can( 'manage_options' ) ) {
		mypmpromd_batch_process_addresses_users();
	}

}
add_action( 'admin_init', 'mypmpromd_batch_process_addresses_override_users' );

// Helper function to batch process user addresses.
function mypmpromd_batch_process_addresses_users(){
	
	// Get maps API key
	$api_key = get_option( 'pmpro_pmpromd_maps_api_key' );
	
	if ( empty( $api_key ) ) {
		//No API key found, exit
		exit('No API key found');
	}

	$users = get_users();

	if ( empty( $users ) ) {
		//No users found, exit
		exit( 'No users found' );
	}
	
	foreach ( $users as $result ) {

		// Skip any users that may already have updated pmpro member directory user field data. Remove this check if you want to reprocess all users regardless of existing data.
		if ( ! empty( get_user_meta( $result->ID, 'pmpromd_street_name', true ) ) ) {
			continue;
		}

		$member_address = array(
			'street' 	=> get_user_meta( $result->ID, 'pmpro_baddress1', true ).' '.get_user_meta( $result->ID, 'pmpro_baddress2', true ),
			'city' 		=> get_user_meta( $result->ID, 'pmpro_bcity', true ),
			'state' 	=> get_user_meta( $result->ID, 'pmpro_bstate', true ),
			'country'   => get_user_meta( $result->ID, 'pmpro_bcountry', true ),
			'zip' 		=> get_user_meta( $result->ID, 'pmpro_bzipcode', true )
		);
		
		if ( function_exists( 'pmpromd_geocode_map_address' ) ) {
			//New version of Member Directory active 2.1+, use its geocode function
			$coordinates = pmpromd_geocode_map_address( $member_address );
			
			//Geocode was successful, add to user meta                
			if ( is_array( $coordinates ) ) {

				$member_address['optin'] = true;
				
				$member_address['latitude'] = $coordinates['lat'];
				$member_address['longitude'] = $coordinates['lng'];
				
				update_user_meta( $result->ID, 'pmpromd_pin_location', $member_address );

				update_user_meta( $result->ID, 'pmpromd_map_optin', true );
				update_user_meta( $result->ID, 'pmpromd_street_name', $member_address['street'] );
				update_user_meta( $result->ID, 'pmpromd_city', $member_address['city'] );
				update_user_meta( $result->ID, 'pmpromd_state', $member_address['state'] );
				update_user_meta( $result->ID, 'pmpromd_zip', $member_address['zip'] );
				update_user_meta( $result->ID, 'pmpromd_country', $member_address['country'] );

			}   
		} elseif ( function_exists( 'pmpromm_geocode_address' ) ) {
			//Use older geocode function from Membership Maps
			$coordinates = pmpromm_geocode_address( $member_address );
			//Geocode was successful, add to user meta
			if( is_array( $coordinates ) ){
				update_user_meta( intval( $result->ID ), 'pmpro_lat', $coordinates['lat'] );
				update_user_meta( intval( $result->ID ), 'pmpro_lng', $coordinates['lng'] );
			}
			
		}
		
	}
	exit('End');
}