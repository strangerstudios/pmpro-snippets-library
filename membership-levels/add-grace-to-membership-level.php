<?php
/**
 * Add a 15-Day Grace Period to Membership Levels with PMPro
 * 
 * title: Add a Grace Period to a Membership Level
 * layout: snippet
 * collection: membership-levels
 * category: code-snippet, membership-level
 * link: https://www.paidmembershipspro.com/add-a-grace-period/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_membership_post_membership_expiry( $user_id, $level_id ) {
	// Make sure we aren't already in a grace period for this level.
	// Use a per-level meta key so members with multiple levels don't overwrite each other's grace period tracking.
	$grace_period_meta_key = 'pmpro_grace_period_level_' . $level_id;
	$grace_level_flag      = get_user_meta( $user_id, $grace_period_meta_key, true );

	if ( empty( $grace_level_flag ) ) {
		$grace_level                  = array();
		$grace_level['user_id']       = $user_id;
		$grace_level['membership_id'] = $level_id;
		$grace_level['enddate']       = date( 'Y-m-d H:i:s', strtotime( '+15 days', current_time( 'timestamp' ) ) ); // change +15 days with the number of days you would like to give for the grace period.
		$changed = pmpro_changeMembershipLevel( $grace_level, $user_id );
		update_user_meta( $user_id, $grace_period_meta_key, 1 );
	} else {
		delete_user_meta( $user_id, $grace_period_meta_key );
	}
}
add_action( 'pmpro_membership_post_membership_expiry', 'my_pmpro_membership_post_membership_expiry', 10, 2 );

/**
 * Clear the grace period flag when the member is given the level again (renewal at checkout, admin change, import, etc.).
 * Otherwise the stale flag keeps the member marked as in a grace period and skips the grace period at the next expiration.
 * Expirations run this hook with a $level_id of 0, so the flag is left alone there.
 */
function my_pmpro_clear_grace_period_flag( $level_id, $user_id ) {
	if ( ! empty( $level_id ) ) {
		delete_user_meta( $user_id, 'pmpro_grace_period_level_' . $level_id );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_clear_grace_period_flag', 10, 2 );
