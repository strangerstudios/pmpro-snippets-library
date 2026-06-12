<?php
/**
 * Allow pending approval members to see content shown to non-members.
 *
 * title: Allow Pending Approval Members to View Non-Member Content
 * layout: snippet-example
 * collection: content-controls
 * category: approvals
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * This adjusts checks for level ID 0, which PMPro uses when checking
 * whether a user should see non-member content. If the logged-in user
 * only has one membership level and that level is pending approval,
 * this returns true so non-member content is shown.
 */
function my_pmproapprovals_show_non_member_content_to_pending_users( $has_level, $user_id, $levels ) {
	global $pmpro_pages;

	// Do not adjust checks in the WordPress admin.
	if ( is_admin() ) {
		return $has_level;
	}

	// Only adjust checks that are currently returning false.
	if ( $has_level ) {
		return $has_level;
	}

	// Let PMPro handle access checks on PMPro-generated pages.
	if ( did_action( 'wp' ) && ! empty( $pmpro_pages ) && is_page( array_values( $pmpro_pages ) ) ) {
		return $has_level;
	}

	// A user ID is required to check approval status.
	if ( empty( $user_id ) ) {
		return $has_level;
	}

	// The Approval Process for Membership Add On must be active.
	if ( ! class_exists( 'PMPro_Approvals' ) ) {
		return $has_level;
	}

	// Only adjust checks for non-member content.
	$is_non_member_check = (
		$levels === 0 ||
		$levels === '0' ||
		(
			is_array( $levels ) &&
			count( $levels ) === 1 &&
			( in_array( 0, $levels, true ) || in_array( '0', $levels, true ) )
		)
	);

	if ( ! $is_non_member_check ) {
		return $has_level;
	}

	$user_levels = pmpro_getMembershipLevelsForUser( $user_id );

	// Only adjust users with a single pending approval level.
	if ( count( $user_levels ) === 1 && PMPro_Approvals::isPending( $user_id, $user_levels[0]->id ) ) {
		return true;
	}

	return $has_level;
}
add_filter( 'pmpro_has_membership_level', 'my_pmproapprovals_show_non_member_content_to_pending_users', 15, 3 );