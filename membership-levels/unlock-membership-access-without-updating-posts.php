<?php
/**
 * Unlock Membership Access to All Restricted Content Without Updating Posts
 *
 * title: Unlock Membership Access to All Restricted Content Without Updating Posts
 * layout: snippet
 * collection: levels
 * category: all-access
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function give_access_to_all_pmpro_members_example( $hasaccess, $thepost, $theuser, $post_membership_levels ) {

	// Which levels are required to view this content
	if ( pmpro_hasMembershipLevel( array( 7, 8, 9, 10 ) ) ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'give_access_to_all_pmpro_members_example', 10, 4 );
