<?php
/**
 * Set the "Name" field for BuddyPress Extended Profile field and the WordPress Display Name at Membership Checkout
 * 
 * title: Set Display Name for BuddyPress Profile "Name" Field and WP Display Name
 * layout: snippet-example
 * collection: pmpro-buddypress
 * category: buddypress, buddyboss
 * link: https://www.paidmembershipspro.com/set-display-name-for-buddypress-profile-at-membership-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_first_last_display_name( $user_id, $morder ) {
	// Get user's first and last name.
	$first_name = get_user_meta( $user_id, 'first_name', true );
	$last_name  = get_user_meta( $user_id, 'last_name', true );

	if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
		$display_name = trim( $first_name . ' ' . $last_name );
	} elseif ( ! empty( $first_name ) ) {
		$display_name = trim( $first_name );
	}

	if ( ! empty( $display_name ) ) {
		// Should set "display_name" as well as the BuddyPress Profile field name.
		$args = array(
			'ID'           => $user_id,
			'display_name' => $display_name,
		);

		// Update WP user display name.
		wp_update_user( $args );

		// Update the 'Name' xprofile Field (field ID 1).
		if ( function_exists( 'xprofile_set_field_data' ) ) {
			xprofile_set_field_data( 1, $user_id, $display_name );
		}
	}
}
add_action( 'pmpro_after_checkout', 'my_first_last_display_name', 20, 2 );
