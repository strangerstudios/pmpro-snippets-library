<?php
/**
 * Remove access to posts that were published before a member's join date.
 *
 * title: Hide Previous Content From New Members
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/hide-previous-content-from-new-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_hide_old_posts_from_members( $hasaccess, $thepost, $theuser, $post_membership_levels ) {
	global $wpdb;
	// If PMPro says false already, return false.
	if ( ! $hasaccess ) {
		return false;
	}

	// If the post doesn't require membership, allow access.
	if ( ! $post_membership_levels ) {
		return true;
	}

	// Okay, this post requires membership. start by getting the user's startdate.
	$startdate = pmpro_getMemberStartdate( $theuser->ID );

	// No startdate? return false.
	if ( empty( $startdate ) ) {
		return false;
	}

	// If the startdate is before the post date, return true.
	if ( $startdate < strtotime( $thepost->post_date ) ) {
		return true;
	} else {
		// In this case we want to also tweak the message shown.
		add_filter( 'pmpro_no_access_message_body', 'my_pmpro_swap_old_posts_member_text' );
		return false;
	}
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_hide_old_posts_from_members', 10, 4 );

function my_pmpro_swap_old_posts_member_text( $s ) {
	$s = 'This content was published before your membership started.';
	return $s;
}
