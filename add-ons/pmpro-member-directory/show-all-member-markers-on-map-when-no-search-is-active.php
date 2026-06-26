<?php
/**
 * Show ALL member markers on the directory map when no search is active.
 *
 * When the directory is viewed without a search query, the map normally only shows
 * markers for the current paginated page of members. This snippet queries all active
 * members with a saved map location and overrides the marker data before Google Maps
 * initializes, so every member appears as a pin regardless of pagination.
 *
 * title: Show All Member Markers on the Directory Map When No Search Is Active
 * layout: snippet
 * collection: pmpro-member-directory
 * category: maps, markers
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Capture the exact shortcode attributes used when the map renders,
// so we build marker info-windows with the same fields/links as the live map.
function custom_pmpromd_capture_map_attributes( $map_id, $attributes ) {
	global $custom_pmpromd_map_attributes;
	$custom_pmpromd_map_attributes = $attributes;
	return $map_id;
}
add_filter( 'pmpromd_map_id', 'custom_pmpromd_capture_map_attributes', 10, 2 );

// Before footer scripts are printed, replace the paginated marker set with all members.
function custom_pmpromd_all_markers_no_search() {
	global $wpdb, $custom_pmpromd_map_attributes;

	// Only act when the map script was actually enqueued on this page load.
	if ( ! wp_script_is( 'pmpromd-google-maps-javascript', 'enqueued' ) ) {
		return;
	}

	// Leave the default behaviour in place when the visitor has searched.
	if ( ! empty( $_REQUEST['ps'] ) ) {
		return;
	}

	// The plugin's marker-data generator must be available.
	if ( ! function_exists( 'pmpromd_generate_marker_data' ) ) {
		return;
	}

	$attributes = ! empty( $custom_pmpromd_map_attributes ) ? $custom_pmpromd_map_attributes : array();

	// Optional: carry level restrictions from the directory shortcode into this query.
	$level_sql = '';
	if ( ! empty( $attributes['level'] ) ) {
		$levels = is_array( $attributes['level'] ) ? implode( ',', $attributes['level'] ) : $attributes['level'];
		$levels = preg_replace( '/[^0-9,]/', '', $levels );
		if ( ! empty( $levels ) ) {
			$level_sql = "AND mu.membership_id IN ($levels)";
		}
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$sql = "SELECT
			u.ID,
			u.user_nicename,
			umf.meta_value   AS first_name,
			uml.meta_value   AS last_name,
			ummap.meta_value AS maplocation
		FROM {$wpdb->users} u
		LEFT JOIN {$wpdb->pmpro_memberships_users} mu
			ON u.ID = mu.user_id
		LEFT JOIN {$wpdb->usermeta} umh
			ON umh.meta_key = 'pmpromd_hide_directory' AND u.ID = umh.user_id
		LEFT JOIN {$wpdb->usermeta} umf
			ON umf.meta_key = 'first_name' AND u.ID = umf.user_id
		LEFT JOIN {$wpdb->usermeta} uml
			ON uml.meta_key = 'last_name' AND u.ID = uml.user_id
		LEFT JOIN {$wpdb->usermeta} ummap
			ON ummap.meta_key = 'pmpromd_pin_location' AND u.ID = ummap.user_id
		WHERE mu.status = 'active'
			AND mu.membership_id > 0
			AND ( umh.meta_value IS NULL OR umh.meta_value <> '1' )
			AND ummap.meta_value IS NOT NULL
			$level_sql
		GROUP BY u.ID";

	// Use the plugin's built-in object cache (invalidated automatically on membership/profile changes).
	$cached = function_exists( 'pmpromd_get_cached_results' ) ? pmpromd_get_cached_results( $sql ) : false;
	if ( is_array( $cached ) && isset( $cached['users'] ) ) {
		$all_members = $cached['users'];
	} else {
		$all_members = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( function_exists( 'pmpromd_set_cached_results' ) ) {
			pmpromd_set_cached_results( $sql, $all_members, count( $all_members ) );
		}
	}

	if ( empty( $all_members ) ) {
		return;
	}

	$all_marker_data = pmpromd_generate_marker_data( $all_members, $attributes );

	if ( empty( $all_marker_data ) ) {
		return;
	}

	// Inject after map.js but before the async Google Maps callback fires,
	// so pmpromd_init_map() picks up the full unpaginated marker set.
	$js = 'if ( typeof pmpromd_vars !== "undefined" ) { pmpromd_vars.marker_data = '
		. wp_json_encode( $all_marker_data ) . '; }';

	wp_add_inline_script( 'pmpromd-google-maps-javascript', $js );
}
add_action( 'wp_footer', 'custom_pmpromd_all_markers_no_search', 5 );
