<?php
/**
 * Unlock Specific Post IDs in a Restricted Category
 *
 * This recipe allows specific post IDs to remain publicly accessible even if they are
 * inside a category restricted by Paid Memberships Pro.
 *
 * title: Unlock Specific Post IDs in a Restricted Category
 * layout: snippet
 * collection: restricted-content
 * category: access-control
 * link: https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function unlock_posts_by_ID( $hasaccess, $post, $user, $post_membership_levels ) {
	// If they already have access, let them at it.
	if ( $hasaccess ) {
		return $hasaccess;
	}

	// Set which post IDs are public.
	$public_post_IDs = array( 1, 2 );

	// Now check the post ID.
	if ( in_array( $post->ID, $public_post_IDs ) ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'unlock_posts_by_ID', 15, 4 );
