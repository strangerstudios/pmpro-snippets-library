<?php
/**
 * Give holders of a custom "chapter_leadership" capability scoped access to the
 * Members by Chapter report.
 *
 * title: Chapter Leaders Scoped Access to Members by Chapter Report
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

defined( 'ABSPATH' ) || exit;

// Shared chapters taxonomy slug. Guarded so any of the related snippets can declare it.
if ( ! function_exists( 'my_pmpro_chapters_taxonomy' ) ) {
	function my_pmpro_chapters_taxonomy() {
		return 'chapters'; // Update this to match your site's custom user taxonomy.
	}
}

// The capability that grants chapter-scoped access to the report. Guarded so it
// can be pre-defined to use a different capability for your chapter leaders.
if ( ! function_exists( 'my_pmpro_chapter_leadership_cap' ) ) {
	function my_pmpro_chapter_leadership_cap() {
		return 'chapter_leadership';
	}
}

// True when the current user is a chapter leader who should be scoped - i.e. has
// the cap but is not a full site admin.
function my_pmpro_user_is_scoped_chapter_leader() {
	return current_user_can( my_pmpro_chapter_leadership_cap() ) && ! current_user_can( 'manage_options' );
}

// The chapter term IDs a user belongs to.
function my_pmpro_get_user_chapter_ids( $user_id ) {
	$terms = wp_get_object_terms( $user_id, my_pmpro_chapters_taxonomy(), array( 'fields' => 'ids' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	return array_map( 'absint', $terms );
}

// The chapter term IDs the current viewer may see; the report snippet calls this.
// Returns null for unrestricted (full admins) or an array for chapter leaders.
function my_pmpro_report_allowed_chapter_ids() {
	if ( my_pmpro_user_is_scoped_chapter_leader() ) {
		return my_pmpro_get_user_chapter_ids( get_current_user_id() );
	}

	return null;
}

// 1. Let chapter leaders into the Reports page by granting pmpro_reports dynamically.
function my_pmpro_chapter_leadership_grant_reports_cap( $allcaps, $caps, $args, $user ) {
	if ( ! empty( $allcaps[ my_pmpro_chapter_leadership_cap() ] ) ) {
		$allcaps['pmpro_reports'] = true;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'my_pmpro_chapter_leadership_grant_reports_cap', 10, 4 );

// 2. Hide every report except Members by Chapter from scoped leaders. Priority
// 100 so this runs after the reports are registered.
function my_pmpro_chapter_leadership_limit_reports( $reports ) {
	if ( my_pmpro_user_is_scoped_chapter_leader() ) {
		$slug    = 'members_by_chapter';
		$reports = isset( $reports[ $slug ] ) ? array( $slug => $reports[ $slug ] ) : array();
	}

	return $reports;
}
add_filter( 'pmpro_registered_reports', 'my_pmpro_chapter_leadership_limit_reports', 100 );

// 3. Block scoped leaders from opening any other report by URL.
function my_pmpro_chapter_leadership_block_other_reports() {
	if ( ! is_admin() || ! my_pmpro_user_is_scoped_chapter_leader() ) {
		return;
	}

	if ( ! isset( $_REQUEST['page'] ) || 'pmpro-reports' !== $_REQUEST['page'] ) {
		return;
	}

	$report = isset( $_REQUEST['report'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['report'] ) ) : '';
	if ( '' !== $report && 'members_by_chapter' !== $report ) {
		wp_die( esc_html__( 'You do not have permission to view this report.', 'pmpro-snippets-library' ) );
	}
}
add_action( 'admin_init', 'my_pmpro_chapter_leadership_block_other_reports' );
