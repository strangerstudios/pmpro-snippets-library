<?php
/**
 * Give users with the "chapter_leadership" capability limited access to the Members List.
 *
 * title: Scope Chapter Leaders to Their Level on the Members List
 * layout: snippet
 * collection: admin-pages
 * category: admin
 * link: https://www.paidmembershipspro.com/how-to-manage-association-chapters/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

defined( 'ABSPATH' ) || exit;

// The capability that grants chapter-scoped access to the Members List.
if ( ! function_exists( 'my_chapter_leadership_cap' ) ) {
	function my_chapter_leadership_cap() {
		return 'chapter_leadership'; // Adjust this if you are using a different capability for your chapter leaders.
	}
}

// True when the current user is a chapter leader who should be scoped - i.e. has
// the cap but is not a full site admin.
function my_chapter_leader_is_scoped() {
	return current_user_can( my_chapter_leadership_cap() ) && ! current_user_can( 'manage_options' );
}

// REQUIRED: the ID of the Level Group that holds your chapter levels. Find it in the
// URL (edit_group=ID) when you edit the group at Memberships > Settings > Levels.
if ( ! function_exists( 'my_chapter_leader_chapters_group_id' ) ) {
	function my_chapter_leader_chapters_group_id() {
		return 0; // Replace 0 with your chapters Level Group ID.
	}
}

// True when the chapters group ID is set and PMPro has the level group helper (3.4+).
function my_chapter_leader_scoping_ready() {
	return absint( my_chapter_leader_chapters_group_id() ) > 0 && function_exists( 'pmpro_get_membership_levels_for_user_in_group' );
}

/**
 * The chapter level IDs a chapter leader may see (only their own).
 *
 * Only levels in the chapters Level Group count. Leadership levels like a shared
 * "Chapter President" level are skipped so a leader can't see every member who
 * holds that level in other chapters. Returns an empty array (the leader sees
 * nothing) until the chapters group ID is set above.
 *
 * @return int[] Level IDs the leader may view.
 */
function my_chapter_leader_level_ids() {
	if ( ! my_chapter_leader_scoping_ready() ) {
		return array();
	}

	$levels = pmpro_get_membership_levels_for_user_in_group( get_current_user_id(), absint( my_chapter_leader_chapters_group_id() ) );

	return $levels ? array_map( 'absint', wp_list_pluck( $levels, 'id' ) ) : array();
}

// Explain the empty list on the Members List page until the recipe is set up.
function my_chapter_leader_setup_notice() {
	if ( my_chapter_leader_scoping_ready() ) {
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		$message = esc_html__( 'Chapter leaders see an empty Members List until you set your chapters Level Group ID in the chapter leader recipe (requires Paid Memberships Pro 3.4 or later).', 'pmpro-snippets-library' );
	} elseif ( my_chapter_leader_is_scoped() ) {
		$message = esc_html__( 'Your chapter member list is not set up yet. Please ask a site administrator to finish the setup.', 'pmpro-snippets-library' );
	} else {
		return;
	}

	echo '<div class="notice notice-warning inline"><p>' . $message . '</p></div>';
}
add_action( 'pmpro_memberslist_before_table', 'my_chapter_leader_setup_notice' );

// 1. Let chapter leaders open the Members List by granting pmpro_memberslist
// dynamically. We deliberately do NOT grant pmpro_memberslistcsv.
function my_chapter_leader_grant_memberslist_cap( $allcaps, $caps, $args, $user ) {
	if ( ! empty( $allcaps[ my_chapter_leadership_cap() ] ) ) {
		$allcaps['pmpro_memberslist'] = true;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'my_chapter_leader_grant_memberslist_cap', 10, 4 );

// 2. Scope the Members List query (and CSV export, which uses the same filter) to
// the leader's level(s). Injecting at the base WHERE clause keeps the trailing
// GROUP BY / ORDER / LIMIT intact and can't be widened via the &l= URL filter.
function my_chapter_leader_scope_memberslist_sql( $sql ) {
	if ( ! my_chapter_leader_is_scoped() ) {
		return $sql;
	}

	$level_ids = my_chapter_leader_level_ids();

	// A leader with no level(s) sees nothing rather than everything.
	$in = ! empty( $level_ids ) ? implode( ',', array_map( 'absint', $level_ids ) ) : '0';

	// Matched as a substring: core wraps this clause in spaces (" WHERE ... 0 "),
	// so we deliberately omit them here and inject around the bare clause below.
	$anchor = 'WHERE mu.membership_id > 0';

	// Fail closed: if PMPro ever changes this clause our injection point is gone,
	// so return zero rows rather than silently leaking every member to the leader.
	// Wrapping (vs. appending) is position-independent - the query already ends in
	// GROUP BY / ORDER BY / LIMIT by the time this filter runs.
	if ( false === strpos( $sql, $anchor ) ) {
		return "SELECT * FROM ( {$sql} ) AS pmpro_scoped_guard WHERE 1 = 0";
	}

	$restriction = $anchor . ' AND mu.membership_id IN (' . $in . ')';

	return str_replace( $anchor, $restriction, $sql );
}
add_filter( 'pmpro_members_list_sql', 'my_chapter_leader_scope_memberslist_sql' );
