<?php
/**
 * Add or remove action links from the Membership Account page based on the member’s level.
 *
 * title: Customize Account Page Action Links by Level
 * layout: snippet
 * collection: frontend-pages
 * category: account-page
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_member_action_links( $pmpro_member_action_links, $level_id ) {
	global $current_user;
	$new_pmpro_member_action_links = array();

	// Always unset the 'change' and 'update-billing' links.
	unset( $pmpro_member_action_links['change'] );

	// Add link to onboarding page.
	if ( in_array( $level_id, array( 1 ) ) ) {
		$new_pmpro_member_action_links['upgrade'] = '<a href="/membership-checkout/?pmpro_level=5">Upgrade</a>';
	}

	// Add link to get support for Website plan members.
	if ( in_array( $level_id, array( 5 ) ) ) {
		$new_pmpro_member_action_links['feedback'] = '<a href="#">Share Feedback</a>';
	}

	// Remove cancel link for coaching levels.
	if ( in_array( $level_id, array( 6, 7, 8 ) ) ) {
		unset( $pmpro_member_action_links['cancel'] );
	}

	// Merge the new links with the existing links.
	$pmpro_member_action_links = array_merge( $new_pmpro_member_action_links, $pmpro_member_action_links );

	return $pmpro_member_action_links;
}
add_filter( 'pmpro_member_action_links', 'my_member_action_links', 10, 2 );