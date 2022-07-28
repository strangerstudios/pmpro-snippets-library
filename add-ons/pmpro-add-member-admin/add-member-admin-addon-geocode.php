<?php
/**
 * Geocodebilling fields when adding a member using the Add Member From Admin Add On
 *
title: Add Geocodebilling fields
layout: Snippet
collection: billing-fields
category: geocode-billing 


 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 */
function mypmpro_add_member_addon_geocode_addresses( $user_id, $user ){

	if( !function_exists( 'pmpromm_geocode_address' ) ){
		return;
	}

	$member_address = array(
		'street' =>  ( !empty( $_REQUEST['street_name'] ) ) ? $_REQUEST['street_name'] : get_user_meta( $user_id, 'street_name', true ),
		'city' => ( !empty( $_REQUEST['city_name'] ) ) ? $_REQUEST['city_name'] : get_user_meta( $user_id, 'city_name', true ),
		'zip' => ( !empty( $_REQUEST['zip_code'] ) ) ? $_REQUEST['zip_code'] : get_user_meta( $user_id, 'zip_code', true ),
		'country' => ( !empty( $_REQUEST['country'] ) ) ? $_REQUEST['country'] : get_user_meta( $user_id, 'country', true )
	);

	$coordinates = pmpromm_geocode_address( $member_address );

	if( is_array( $coordinates ) ){
		update_user_meta( $user_id, 'pmpro_lat', $coordinates['lat'] );
		update_user_meta( $user_id, 'pmpro_lng', $coordinates['lng'] );
	}


}
add_action( 'pmpro_add_member_added', 'mypmpro_add_member_addon_geocode_addresses', 10, 2 );
