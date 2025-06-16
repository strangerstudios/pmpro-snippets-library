<?php
/**
 * Change Membership Level on Expiration Only
 * 
 * title: Change Membership Level on Expiration Only
 * layout: snippet
 * collection: membership-levels
 * category: expiration
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_set_default_level_only_when_expiring( $user_id, $level_id ) {
	global $pmpro_next_payment_timestamp;

	// Only change the level if there is no future payment scheduled.
	if ( empty( $pmpro_next_payment_timestamp ) ) {
		pmpro_changeMembershipLevel( 1, $user_id ); // Change this to the level ID you want to give expired members.
	}

	return $user_id;
}
add_filter( 'pmpro_membership_post_membership_expiry', 'my_pmpro_set_default_level_only_when_expiring', 10, 2 );
