<?php
/**
 * Change Membership Level on Cancellation or Expiration (Allow cancellation from default level).
 *
 * Change the Level ID on line 20 to the level you want members to have assigned
 * to them after cancellation or expiration. Will allow members to the "cancel level" to cancel from that though.
 *
 * title: Change Membership Level on Cancellation or Expiration
 * layout: snippet
 * collection: membership-levels
 * category: expiration, cancellation
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_after_change_membership_level_default_level( $level_id, $user_id ) {
	// set this to the id of the level you want to give members when they cancel
	$cancel_level_id = 1;

	// if we see this global set, then another gist is planning to give the user their level back
	global $pmpro_next_payment_timestamp;
	if ( ! empty( $pmpro_next_payment_timestamp ) ) {
		return;
	}

	// are they cancelling?
	if ( $level_id == 0 ) {
		// check if they are cancelling from level $cancel_level_id
		global $wpdb;
		$last_level_id = $wpdb->get_var( "SELECT membership_id FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user_id . "' ORDER BY id DESC" );
		if ( $last_level_id == $cancel_level_id ) {
			return; // let them cancel
		}

		// otherwise give them level $cancel_level_id instead
		pmpro_changeMembershipLevel( $cancel_level_id, $user_id );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_after_change_membership_level_default_level', 10, 2 );
