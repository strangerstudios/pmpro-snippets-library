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

/**
 * The membership level IDs a chapter leader may see (only their own).
 *
 * @return int[] Level IDs the leader may view.
 */
function my_chapter_leader_level_ids() {
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) ) {
		return array();
	}

	$levels = pmpro_getMembershipLevelsForUser( get_current_user_id() );

	return $levels ? array_map( 'absint', wp_list_pluck( $levels, 'id' ) ) : array();
}

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
