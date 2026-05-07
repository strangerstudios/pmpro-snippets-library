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
 * @param $attrs An array of attributes ( status, level, justnumber)
 */
function pmpro_member_count_shortcode( $attrs = null ) {
	global $wpdb;

	extract(
		shortcode_atts(
			array(
				'status'     => 'active',
				'level'      => null,
				'justnumber' => false,
			),
		$attrs
	    )
	);

	// Process statuses
	$statuses = array_map( 'trim', explode( ',', $status ) );

	if ( ! is_array( $statuses ) && ! empty( $status ) ) {
		$statuses = array( $status );
	}

	// Start building SQL
	$sql = "SELECT COUNT(*) FROM {$wpdb->pmpro_memberships_users} WHERE `status` IN ('" . implode( "', '", $statuses ) . "')";

	// Process levels
	if ( ! empty( $level ) ) {
		$levels = array_map( 'intval', explode( ',', $level ) );
		$sql    .= " AND `membership_id` IN (" . implode( ',', $levels ) . ")";
	}

	$member_count = $wpdb->get_var( $sql );
	
	// There was an error getting data from the database.
	if ( is_wp_error( $member_count ) ) {
		return sprintf( esc_html__( "Error while processing request: %s", 'pmpro-snippets-library' ), $wpdb->print_error() );	
	}
	
	// Display only the number of members if we want the integer value.
	if ( ! empty( $justnumber ) ) {
		return $member_count;
	} 

	// Change the text if we are outputting for specific levels, or the overall member count.
	if ( ! empty( $level ) ) {
		return sprintf( esc_html__( "This site has %d members in the selected levels", 'pmpro-snippets-library' ), $member_count );
	} else {
		return sprintf( esc_html__( "This site has %d members", 'pmpro-snippets-library' ), $member_count );
	}
}
add_shortcode( 'pmpro_member_count', 'pmpro_member_count_shortcode' );