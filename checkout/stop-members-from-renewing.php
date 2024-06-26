<?php
/**
 * Stop members from renewing their current membership level.
 *
 * title: Stop members from renewing.
 * layout: snippet
 * collection: checkout
 * category: renewals
 * link: https://www.paidmembershipspro.com/how-to-stop-members-from-renewing-their-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 */

function stop_members_from_renewing( $okay ) {

	// If something else isn't okay, stop from running this code further.
	if ( ! $okay ) {
		return $okay;
	}

	// If the user doesn't have a membership level carry on with checkout.
	if ( ! pmpro_hasMembershipLevel() ) {
		return $okay;
	}

	// Get level for checkout.
	$checkout_level = pmpro_getLevelAtCheckout();
	$level_id = $checkout_level->id;

	// Check if the user's current membership level is the same for checking out.
	if ( pmpro_hasMembershipLevel( $level_id ) ) {
		$okay = false;
		pmpro_setMessage( 'This is your current membership level. Please select a different membership level.', 'pmpro_error' );
	}

	return $okay;

}
add_filter( 'pmpro_registration_checks', 'stop_members_from_renewing', 10, 1 );
