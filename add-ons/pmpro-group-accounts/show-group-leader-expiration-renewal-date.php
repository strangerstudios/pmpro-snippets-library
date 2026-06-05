<?php
/**
 * Show the group leader's expiration or renewal date to child group members.
 *
 * title: Show Group Leader Expiration Date for Child Members
 * layout: snippet-example
 * collection: group-accounts
 * category: membership-levels
 * link: TBD
 *
 * Child members in a Group Accounts membership inherit access from the group
 * leader. This recipe replaces the default membership expiration text shown to
 * child members with the group leader's expiration date. If the leader does not
 * have a fixed expiration date, the next subscription renewal date is shown.
 *
 * Tested with Paid Memberships Pro 3.7.4 and Group Accounts Add On 1.6.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Filter the membership expiration text for child group members.
 */
function my_pmprogroupacct_membership_expiration_text( $text, $level, $user ) {
	// Bail if Group Accounts is not active.
	if ( ! class_exists( 'PMProGroupAcct_Group_Member' ) || ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return $text;
	}

	// Get the active group membership record for this user and level.
	$group_members = PMProGroupAcct_Group_Member::get_group_members(
		array(
			'group_child_user_id'  => $user->ID,
			'group_child_level_id' => $level->id,
			'group_child_status'   => 'active',
		)
	);

	if ( empty( $group_members ) ) {
		return $text;
	}

	// Get the associated group.
	$groups = PMProGroupAcct_Group::get_groups(
		array(
			'id' => $group_members[0]->group_id,
		)
	);

	if ( empty( $groups ) ) {
		return $text;
	}

	$group           = $groups[0];
	$group_leader_id = $group->group_parent_user_id;

	// Check for a fixed expiration date on the group leader's membership.
	$expiration_text = pmpro_get_membership_expiration_text(
		$group->group_parent_level_id,
		$group_leader_id,
		''
	);

	if ( ! empty( $expiration_text ) && strpos( $expiration_text, 'No Expiration' ) === false ) {
		return $expiration_text;
	}

	// No fixed expiration date. Look for the next subscription renewal date.
	global $wpdb;

	$next_payment_date = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT next_payment_date
			 FROM {$wpdb->prefix}pmpro_subscriptions
			 WHERE user_id = %d
			 AND status = 'active'
			 ORDER BY id DESC
			 LIMIT 1",
			$group_leader_id
		)
	);

	if ( empty( $next_payment_date ) ) {
		return $text;
	}

	$formatted_date = wp_date(
		get_option( 'date_format' ),
		strtotime( $next_payment_date )
	);

	return sprintf(
		/* translators: %s: formatted renewal date. */
		__( 'Your membership renews on %s.', 'pmpro' ),
		$formatted_date
	);
}
add_filter( 'pmpro_membership_expiration_text', 'my_pmprogroupacct_membership_expiration_text', 10, 3 );