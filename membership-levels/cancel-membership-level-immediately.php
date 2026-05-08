<?php
/**
 * Cancel an automatically recurring payment membership level immediately.
 *
 * By default this applies to all membership levels. To restrict cancellation
 * to specific levels only, see the level-specific example at the bottom of this file.
 *
 * title: Cancel Recurring Level Immediately
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

// Option 1: Cancel immediately for ALL membership levels.
add_filter( 'pmpro_cancel_on_next_payment_date', '__return_false' );

/**
 * Option 2: Cancel immediately for specific membership levels only.
 *
 * Replace the level IDs in the array below with the levels you want
 * to cancel immediately. All other levels retain the default behavior.
 */
// function my_pmpro_cancel_specific_levels_immediately( $cancel ) {
// 	// Add the membership level IDs that should cancel immediately.
// 	$cancel_immediately_levels = array( 1, 2 );
//
// 	if ( pmpro_hasMembershipLevel( $cancel_immediately_levels, get_current_user_id() ) ) {
// 		return false;
// 	}
//
// 	return $cancel;
// }
// add_filter( 'pmpro_cancel_on_next_payment_date', 'my_pmpro_cancel_specific_levels_immediately' );
