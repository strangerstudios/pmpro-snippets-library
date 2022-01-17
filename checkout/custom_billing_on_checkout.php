<?php
/**
 * This recipe will geocode the custom billing fields we've created during checkout.
 * 
 * title: Add custom field to checkout
 * layout: snippet-example
 * collection: checkout
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmpromm_checkout_override(){
	//Remove this hook otherwise we'll geocode the billing address instead
	remove_action( 'pmpro_after_checkout', 'pmpromm_after_checkout', 10, 2 );
}
add_action( 'init', 'mypmpromm_checkout_override' );

function mypmpromm_custom_billing_fields_checkout( $user_id, $morder ){

	//Requires PMPro Membrship Maps and PMPro Shipping Add On Active
	if( !function_exists( 'pmpromm_geocode_address' ) ){
		return;
	}

	$saddress1 = get_user_meta( $user_id, "pmpro_baddress1", true );
	$saddress2 = get_user_meta( $user_id, "pmpro_baddress2", true );
	$scity = get_user_meta( $user_id, "pmpro_bcity", true );
	$sstate = get_user_meta( $user_id, "pmpro_bstate", true );
	$szipcode = get_user_meta( $user_id, "pmpro_bzipcode", true );

	if( !empty( $saddress1 ) ){
		
		$member_address = array(
			'street' 	=> $saddress1 .' '.$saddress2,
			'city' 		=> $scity,
			'state' 	=> $sstate,
			'zip' 		=> $szipcode
		);

		$coordinates = pmpromm_geocode_address( $member_address );

		if( is_array( $coordinates ) ){
			if( !empty( $coordinates['lat'] ) && !empty( $coordinates['lng'] ) ){
				update_user_meta( $user_id, 'pmpro_lat', $coordinates['lat'] );
				update_user_meta( $user_id, 'pmpro_lng', $coordinates['lng'] );
			}
		}	

	}

}
add_action( 'pmpro_after_checkout', 'mypmpromm_custom_billing_fields_checkout', 10, 2 );
