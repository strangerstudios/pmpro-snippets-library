<?php
/**
 * Display child group accounts below  member profile on Member Directory Add On.
 *
 * title: Display child group accounts below  member profile on Member Directory Add On.
 * layout: snippet
 * collection: add-ons, pmpro-membership-directory, pmpro-group-account
 * category: directory, profile, group accounts
 * link: https://www.paidmembershipspro.com/display-linked-child-accounts-on-the-sponsor-parent-members-directory-profile-page/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


/**
 * Add child group accounts below  member profile on Member Directory Add On.
 *
 * @param $pu WP_User The user whose profile is being shown.
 * 
 */
function show_group_account_child_pmpro_member_profile_after( $pu ) {
	// Bail if pmpro groups account Add On isn't active.
	if ( ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return;
	}

	// Get Profile User levels.
	$pu_levels = pmpro_getMembershipLevelsForUser( $pu->ID );

	// Bail if user does not have a level
	if ( empty( $pu_levels ) ) {
		return;
	}

	// Get the groups for each user's level.
	$groups = array();
	foreach ( $pu_levels as $pu_level ) {
		$group = PMProGroupAcct_Group::get_group_by_parent_user_id_and_parent_level_id( $pu->ID, $pu_level->id );
		if ( ! empty( $group ) ) {
			array_push( $groups, $group );
		}
	}

	// Bail if the user isn't a parent.
	if ( empty( $groups ) ) {
		return;
	}

	// Get the active members for each group.
	$active_members = array();
	foreach ( $groups as $group ) {
		$active_members = array_merge( $active_members, $group->get_active_members() );
	}

	// Bail if the group has no  members.
	if ( empty( $active_members ) ) {
		return;
	}

	// Display the group members.
	echo '<strong>' . esc_html__( 'Group Account Members' ) . '</strong>';
	echo '<ul>';
	foreach ( $active_members as $active_member ) {
		$user = get_userdata( $active_member->group_child_user_id );
		echo '<li>' . esc_html( $user->display_name ) . '</li>';
	}
	echo '</ul>';

}
add_action( 'pmpro_member_profile_after', 'show_group_account_child_pmpro_member_profile_after' );
