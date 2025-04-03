<?php
/**
 * Add a 15-Day Grace Period to a Membership Level with PMPro
 * 
 * title: Add a Grace Period to a Membership Level
 * layout: snippet
 * collection: membership-levels
 * category: code-snippet, membership-level
 * link: https://www.paidmembershipspro.com/add-a-grace-period-to-a-pmpro-membership/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_membership_post_membership_expiry( $user_id, $level_id ) {
	// Make sure we aren't already in a grace period for this level
	$grace_level = get_user_meta( $user_id, 'grace_level', true );
	if ( empty( $grace_level ) || $grace_level !== $level_id ) {
		$grace_level                  = array();
		$grace_level['user_id'] = $user_id;
		$grace_level['membership_id'] = $level_id;
		$grace_level['enddate']       = date( 'Y-m-d H:i:s', strtotime( '+15 days', current_time( 'timestamp' ) ) ); // change +15 days with the number of days you would like to give for the grace period.
		$changed = pmpro_changeMembershipLevel( $grace_level, $user_id );
		update_user_meta( $user_id, 'grace_level', $level_id );
	} else {
		delete_user_meta( $user_id, 'grace_level' );
	}
}
add_action( 'pmpro_membership_post_membership_expiry', 'my_pmpro_membership_post_membership_expiry', 10, 2 );
