<?php

/**
 * This recipe will create a [pmpro_member_count] shortcode, that will display the number of members in a specific level with a specific status.
 * title: Add a shortcode that counts members. Support passing different statuses
 * layout: snippet
 * collection: block-shortcodes
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/display-count-members-level-andor-status-via-shortcode/
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 *
 * @param $attrs An array of attributes ( status, level, justnumber)
 */

function pmpro_member_count_shortcode( $attrs = null ) {
	global $wpdb;

	$attrs = shortcode_atts(
		array(
			'status'     => 'active',
			'levels'     => null,
			'justnumber' => false,
		),
		$attrs
	);

	// Process statuses
	$statuses = array_map( 'trim', explode( ',', $attrs['status'] ) );

	if ( ! is_array( $statuses ) && ! empty( $attrs['status'] ) ) {
		$statuses = array( $attrs['status'] );
	}

	// Start building SQL
	$sql = "SELECT COUNT(*) FROM {$wpdb->pmpro_memberships_users} WHERE `status` IN ('" . implode( "', '", $statuses ) . "')";

	// Process levels
	if ( ! empty( $attrs['levels'] ) ) {
		$levels = array_map( 'intval', explode( ',', $attrs['levels'] ) );
		$sql    .= " AND `membership_id` IN (" . implode( ',', $levels ) . ")";
	}

	$member_count = $wpdb->get_var( $sql );

	if ( ! is_wp_error( $member_count ) ) {
		if ( ! empty( $attrs['levels'] ) ) {
			if ( ! empty( $attrs['justnumber'] ) ) {
				return $member_count;
			} else {
				return sprintf( __( "This site has %d members in the selected levels", "pmpromsc" ), $member_count );
			}
		} else {
			if ( ! empty( $attrs['justnumber'] ) ) {
				return $member_count;
			} else {
				return sprintf( __( "This site has %d members", "pmpromsc" ), $member_count );
			}
		}
	} else {
		return sprintf( __( "Error while processing request: %s", "pmpromsc" ), $wpdb->print_error() );
	}
}

add_shortcode( 'pmpro_member_count', 'pmpro_member_count_shortcode' );