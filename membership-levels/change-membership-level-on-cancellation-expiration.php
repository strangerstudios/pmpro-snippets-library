<?php
/**
 * Change Membership Level on Cancellation or Expiration
 *
 * Change the Level ID on line 30 to the level you want members to have assigned
 * to them after cancellation or expiration.
 *
 * title: Change Membership Level on Cancellation or Expiration
 * layout: snippet
 * collection: membership-levels
 * category: expiration, cancellation
 * link: https://www.paidmembershipspro.com/change-membership-level-on-cancellation-or-expiration/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_after_change_membership_level_default_level( $level_id, $user_id, $cancel_level = null ) {

	// Only act on cancellations (level_id 0) and only when we know which level was cancelled.
	if ( $level_id != 0 || empty( $cancel_level ) ) {
		return;
	}

	// If the user still has another active membership level (e.g. in another level group), don't downgrade them.
	if ( pmpro_hasMembershipLevel( null, $user_id ) ) {
		return;
	}

	// cancelling, give them level 1 instead
	pmpro_changeMembershipLevel( 1, $user_id );
}
add_action( 'pmpro_after_change_membership_level', 'pmpro_after_change_membership_level_default_level', 10, 3 );
