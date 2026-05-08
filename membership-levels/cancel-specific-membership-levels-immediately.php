<?php
/**
 * Cancel specific levels immediately instead of extending until the end of the billing cycle.
 *
 * title: Cancel Specific Recurring Levels Immediately
 * layout: snippet
 * collection: membership-levels
 * category: cancellation
 * link: https://www.paidmembershipspro.com/documentation/membership-levels/canceling-a-user-membership/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Cancel immediately for specific membership levels only.
 *
 * Replace the level IDs in the array below with the levels you want
 * to cancel immediately. All other levels retain the default behavior.
 */
function my_pmpro_cancel_specific_levels_immediately( $cancel ) {
	// Update this array with the level IDs you want to cancel immediately.
	$cancel_immediately_levels = array( 1, 2 );

	if ( pmpro_hasMembershipLevel( $cancel_immediately_levels, get_current_user_id() ) ) {
		return false;
	}

	return $cancel;
}
add_filter( 'pmpro_cancel_on_next_payment_date', 'my_pmpro_cancel_specific_levels_immediately' );
