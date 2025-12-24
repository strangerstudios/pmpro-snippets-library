<?php
/**
 * This recipe will geocode custom adress fields after checkout.
 *
 * title: How to Geocode Any Address Fields During Checkout
 * layout: snippet
 * collection: pmpro-member-directory
 * category: checkout, user-fields, maps
 * link: https://www.paidmembershipspro.com/how-to-geocode-address-fields-during-checkout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Hide map address fields on checkout page.
 */
function my_pmpromd_checkout_hide_map_address_fields() {
	global $pmpro_field_groups;

	$field_group_name = esc_html__( 'Directory and Profile Preferences', 'pmpro-member-directory' );

	unset( $pmpro_field_groups[ $field_group_name ] );
}
add_action( 'pmpro_checkout_boxes', 'my_pmpromd_checkout_hide_map_address_fields', 5 );

/**
 * Stop processing default map address fields after checkout.
 */
function my_pmpromd_disable_map_address_geocoding_after_checkout() {
	remove_action( 'pmpro_after_checkout', 'pmpromd_map_process_map_address_after_checkout', 10 );
}
add_action( 'init', 'my_pmpromd_disable_map_address_geocoding_after_checkout', 5 );

/**
 * Geocode custom address fields after checkout and save the location data.
 */
function my_pmpromd_process_custom_address_after_checkout( $user_id ) {

	// No user ID provided, lets get the current user.
	if ( ! $user_id ) {
		// Current user isn't logged in for some reason, bail.
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}
	}

	// Lets make sure the site has a Maps API key set to continue further.
	if ( ! get_option( 'pmpro_pmpromd_maps_api_key' ) ) {
		return;
	}

	// Let's see if the fields are set in the request and geocode the address passed in.
	if ( ! empty( $_REQUEST['address_street_name'] ) ) {

		// Create an array of the member's address.
		$member_address = array(
			'street'  => ( ! empty( $_REQUEST['address_street_name'] ) ) ? sanitize_text_field( $_REQUEST['address_street_name'] ) : '',
			'city'    => ( ! empty( $_REQUEST['address_city'] ) ) ? sanitize_text_field( $_REQUEST['address_city'] ) : '',
			'state'   => ( ! empty( $_REQUEST['address_state'] ) ) ? sanitize_text_field( $_REQUEST['address_state'] ) : '',
			'zip'     => ( ! empty( $_REQUEST['address_zip'] ) ) ? sanitize_text_field( $_REQUEST['address_zip'] ) : '',
			'country' => ( ! empty( $_REQUEST['address_country'] ) ) ? sanitize_text_field( $_REQUEST['address_country'] ) : '',
		);

		// Opt in the member for display on the map.
		$member_address['optin'] = true;

		// Let's build the address array to geocode.
		$coordinates = pmpromd_geocode_map_address( $member_address );

		if ( is_array( $coordinates ) ) {
			if ( ! empty( $coordinates['lat'] ) && ! empty( $coordinates['lng'] ) ) {
				$member_address['latitude']  = $coordinates['lat'];
				$member_address['longitude'] = $coordinates['lng'];

				// Only add to user meta if the address has been geocoded.
				update_user_meta( $user_id, 'pmpromd_pin_location', $member_address );
			} else {
				// Cleanup pin location.
				delete_user_meta( $user_id, 'pmpromd_pin_location' );
			}
		}
	} else {
		// Cleanup pin location.
		delete_user_meta( $user_id, 'pmpromd_pin_location' );
	}
}
add_action( 'pmpro_after_checkout', 'my_pmpromd_process_custom_address_after_checkout' );
