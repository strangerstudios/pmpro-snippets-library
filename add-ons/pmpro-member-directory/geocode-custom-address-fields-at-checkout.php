<?php
/**
 * This recipe will geocode custom address fields after checkout.
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
 * Remove default map address fields.
 */
function my_pmpromd_disable_map_address_user_fields() {
	remove_action( 'init', 'pmpromd_add_user_fields' );
}
add_action( 'init', 'my_pmpromd_disable_map_address_user_fields', 5 );

/**
 * Stop geocoding default map address fields after checkout and profile update.
 */
function my_pmpromd_disable_map_address_geocoding() {
	remove_action( 'pmpro_after_checkout', 'pmpromd_map_process_map_address_after_checkout', 10 );
	remove_action( 'pmpro_personal_options_update', 'pmpromd_geocode_map_address_for_user', 10 );
	remove_action( 'personal_options_update', 'pmpromd_geocode_map_address_for_user', 10 );
	remove_action( 'edit_user_profile_update', 'pmpromd_geocode_map_address_for_user', 10 );
}
add_action( 'init', 'my_pmpromd_disable_map_address_geocoding', 5 );

/**
 * Function to save the user's pin location to user meta.
 */
function my_pmpromd_save_custom_address_marker_location_for_user( $user_id ) {

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

	// Let's see if the address field is present in the request and geocode the address passed in.
	if ( array_key_exists( 'address_street_name', $_REQUEST ) ) {
		if ( ! empty( $_REQUEST['address_street_name'] ) ) {

			// Create an array of the member's address.
			$member_address = array(
				'street'  => ( ! empty( $_REQUEST['address_street_name'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['address_street_name'] ) ) : '',
				'city'    => ( ! empty( $_REQUEST['address_city'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['address_city'] ) ) : '',
				'state'   => ( ! empty( $_REQUEST['address_state'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['address_state'] ) ) : '',
				'zip'     => ( ! empty( $_REQUEST['address_zip'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['address_zip'] ) ) : '',
				'country' => ( ! empty( $_REQUEST['address_country'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['address_country'] ) ) : '',
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
					// Cleanup pin location if the address could not be geocoded.
					delete_user_meta( $user_id, 'pmpromd_pin_location' );
				}
			}
		} else {
			// Address field was submitted but left empty; cleanup pin location.
			delete_user_meta( $user_id, 'pmpromd_pin_location' );
		}
	}
}

/**
 * Geocode the custom address fields after checkout and save the location data.
 */
function my_pmpromd_map_process_custom_map_address_after_checkout( $user_id ) {
	my_pmpromd_save_custom_address_marker_location_for_user( $user_id );
}
add_action( 'pmpro_after_checkout', 'my_pmpromd_map_process_custom_map_address_after_checkout', 10 );

/**
 * Geocode the custom address fields when saving/updating a user profile.
 */
function my_pmpromd_geocode_custom_map_address_on_profile_update( $user_id ) {
	my_pmpromd_save_custom_address_marker_location_for_user( $user_id );
}
add_action( 'pmpro_personal_options_update', 'my_pmpromd_geocode_custom_map_address_on_profile_update', 10 );
add_action( 'personal_options_update', 'my_pmpromd_geocode_custom_map_address_on_profile_update', 10 );
add_action( 'edit_user_profile_update', 'my_pmpromd_geocode_custom_map_address_on_profile_update', 10 );

/**
 * Geocode the custom address fields when saved on the Member Edit panel.
 */
function my_pmpromd_geocode_custom_map_address_on_member_edit_panel_save() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// If we're saving a member edit panel and the custom address field is set in the request.
	if ( isset( $_REQUEST['pmpro_member_edit_panel'] ) && isset( $_REQUEST['address_street_name'] ) ) {

		// Get the panel slug that was submitted.
		$panel_slug = empty( $_REQUEST['pmpro_member_edit_panel'] ) ? '' : sanitize_text_field( $_REQUEST['pmpro_member_edit_panel'] );
		if ( empty( $panel_slug ) ) {
			return;
		}

		// Bail if we do not have a verified nonce.
		if ( empty( $_REQUEST['pmpro_member_edit_saved_panel_nonce'] ) || ! wp_verify_nonce( $_REQUEST['pmpro_member_edit_saved_panel_nonce'], 'pmpro_member_edit_saved_panel_' . $panel_slug ) ) {
			return;
		}

		// Update the member's address.
		my_pmpromd_save_custom_address_marker_location_for_user( $_REQUEST['user_id'] );
	}
}
add_action( 'admin_init', 'my_pmpromd_geocode_custom_map_address_on_member_edit_panel_save' );
