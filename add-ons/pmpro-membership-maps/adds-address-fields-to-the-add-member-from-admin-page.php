<?php

/**
 * Add billing fields to the Add Member from Admin Add On page for geocoding with Membership Maps
 *
 * title: Add custom fields to Add Member from Admin Add On
 * layout: snippet
 * collection: pmpro-membership-maps
 * category: custom-fields, pmpro-add-member-admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Add the custom fields to the UI.
function my_pmpro_add_member_add_address_fields( $user, $user_id ) {
	?>
	<tr id="address">
		<th><label for="company">Address</label></th>
		<td><input type="text" id="address1" name="address1"></td>
	</tr>
	<tr id="city_name">
		<th><label for="company">City</label></th>
		<td><input type="text" id="city_name" name="city_name"></td>
	</tr>
	<tr id="zip_code">
		<th><label for="company">Zip Code</label></th>
		<td><input type="text" id="addrzip_codeezip_codess1" name="zip_code"></td>
	</tr>
	<tr id="country">
		<th><label for="company">Country</label></th>
		<td><input type="text" id="country" name="country"></td>
	</tr>
	<?php
}
add_action( 'pmpro_add_member_fields', 'my_pmpro_add_member_add_address_fields', 10, 2 );

// Handle the geocoding on the custom fields from my_pmpro_add-member_add_address_fields function.
function my_pmpro_add_member_addon_geocode_addresses( $user_id, $user ) {

	if ( ! function_exists( 'pmpromm_geocode_address' ) ) {
		return;
	}

	$member_address = array(
		'street'  => ( ! empty( $_REQUEST['street_name'] ) ) ? sanitize_text_field( $_REQUEST['street_name'] ) : get_user_meta( $user_id, 'street_name', true ),
		'city'    => ( ! empty( $_REQUEST['city_name'] ) ) ? sanitize_text_field( $_REQUEST['city_name'] ) : get_user_meta( $user_id, 'city_name', true ),
		'zip'     => ( ! empty( $_REQUEST['zip_code'] ) ) ? sanitize_text_field( $_REQUEST['zip_code'] ) : get_user_meta( $user_id, 'zip_code', true ),
		'country' => ( ! empty( $_REQUEST['country'] ) ) ? sanitize_text_field( $_REQUEST['country'] ) : get_user_meta( $user_id, 'country', true ),
	);

	$coordinates = pmpromm_geocode_address( $member_address );

	if ( is_array( $coordinates ) ) {
		update_user_meta( $user_id, 'pmpro_lat', $coordinates['lat'] );
		update_user_meta( $user_id, 'pmpro_lng', $coordinates['lng'] );
	}

}
add_action( 'pmpro_add_member_added', 'my_pmpro_add_member_addon_geocode_addresses', 10, 2 );
