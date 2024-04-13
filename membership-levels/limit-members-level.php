<?php
/**
 * Limit the number of members in a level.
 * This is a very large snippet that provides a complete solution for limiting the number of members in a membership level
 * -- Show the limit in various places
 * -- Hide levels that reach the limit from the public
 * -- Redirecting away from checkout when the limit is reached.
 * -- Confirm there are spots available before checking out.
 *
 * Note: This does not affect pre-existing members that had a level before this code is implemented.
 *
 * title: Limit Members Per Membership Level
 * layout: snippet
 * collection: membership-levels
 * category: limit-members
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Save Level Meta for a member limit.
 */
function my_pmpro_limit_members_pmpro_save_membership_level( $save_id ) {
	$pmpro_level_member_limit = intval( $_REQUEST['pmpro_level_member_limit'] );
	update_pmpro_membership_level_meta( $save_id, 'pmpro_level_member_limit', $pmpro_level_member_limit );
}
add_action( 'pmpro_save_membership_level', 'my_pmpro_limit_members_pmpro_save_membership_level', 10, 1 );

/**
 * Add Level Meta for a member limit.
 */
function my_pmpro_limit_members_pmpro_membership_level_after_other_settings() {
	$edit_level_id = $_REQUEST['edit'];
	$pmpro_level_member_limit = get_pmpro_membership_level_meta( $edit_level_id, 'pmpro_level_member_limit', true );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" valign="top"><label for="pmpro_level_member_limit"><?php esc_html_e( 'Maximum Number of Members', 'pmpro-customizations' ); ?></label></th>
				<td>
					<input id="pmpro_level_member_limit" name="pmpro_level_member_limit" type="number" value="<?php echo esc_attr( $pmpro_level_member_limit );?>" />
					<p class="description"><?php esc_html_e( 'Set the maximum number of members for this level.', 'pmpro-customizations' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'pmpro_membership_level_after_other_settings', 'my_pmpro_limit_members_pmpro_membership_level_after_other_settings' );

/**
 * Restrict checkout if the maximum number of members in a membership level has been reached.
 */
function my_pmpro_limit_members_pmpro_registration_checks( $value ) {
	global $wpdb;
	
	// Get the level at checkout.
	$pmpro_level = pmpro_getLevelAtCheckout();

	// If we don't have a level, return.
	if ( empty( $pmpro_level->id ) ) {
		return $value;
	}

	// Get the maximum number of members allowed in this level.
	$pmpro_level_member_limit = get_pmpro_membership_level_meta( $pmpro_level->id, 'pmpro_level_member_limit', true );

	// Return early if the limit is not set.
	if ( empty( $pmpro_level_member_limit ) ) {
		return $value;
	}

	// Get the count of members in this level.
	$member_count = my_pmpro_get_active_members_in_level( $level_id );

	// Compare the count of members to the maximum number of members allowed in this level
	if ( $pmpro_level_member_limit > 0 && $member_count >= $pmpro_level_member_limit ) {
		global $pmpro_msg, $pmpro_msgt;
		$pmpro_msg = __( 'Membership limit has been reached for this level', 'pmpro-customizations' );
		$pmpro_msgt = "pmpro_error";
		$value = false;
	}

	return $value;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_limit_members_pmpro_registration_checks' );

/**
 * Show '0 of X spots available.' on membership checkout page if a limit is set.
 */
function my_pmpro_show_spots_available( ) {

	// Get the level.
	$pmpro_level = pmpro_getLevelAtCheckout();

	// Get the maximum number of members allowed in this level.
	$pmpro_level_member_limit = get_pmpro_membership_level_meta( $pmpro_level->id, 'pmpro_level_member_limit', true );

	// Return early if the limit is not set.
	if ( empty( $pmpro_level_member_limit ) ) {
		return;
	}
	
	// Get the count of members in this level.
	$member_count = my_pmpro_get_active_members_in_level( $pmpro_level->id );

	// Show the spots claimed.
	echo '<p class="my-pmpro-spots-claimed">';
	echo esc_html( sprintf(
		// translators: %1$s is the number of members in the level and %2$s is the limit.
		__( '%1$s of %2$s spots claimed.', 'pmpro-customizations' ),
		$member_count,
		$pmpro_level_member_limit
	) ); 
	echo '</p>';

	// Or, use this code to show the spots available.
	/*
	echo '<p class="my-pmpro-spots-available">';
	echo esc_html( sprintf(
		// translators: %1$s is the number of spots available.
		__( '%s spots available.', 'pmpro-customizations' ),
		$pmpro_level_member_limit - $member_count
	) ); 
	echo '</p>';
	*/
}
add_action( 'pmpro_checkout_after_level_cost', 'my_pmpro_show_spots_available' );

/**
 * Redirect away from checkout if the limit is reached.
 */
function my_pmpro_template_redirect_no_spots_available() {
	global $pmpro_pages;

	// Return early if we are not on a PMPro page.
	if ( empty( $pmpro_pages ) ) {
		return;
	}

	// Return early if this is not the checkout page.
	if ( ! is_page( $pmpro_pages['checkout'] ) ) {
		return;
	}

	// Get the level.
	$pmpro_level = pmpro_getLevelAtCheckout();

	// Get the maximum number of members allowed in this level.
	$pmpro_level_member_limit = get_pmpro_membership_level_meta( $pmpro_level->id, 'pmpro_level_member_limit', true );

	// Return early if the limit is not set.
	if ( empty( $pmpro_level_member_limit ) ) {
		return;
	}

	// Get the count of members in this level.
	$member_count = my_pmpro_get_active_members_in_level( $pmpro_level->id );

	// Redirect away from checkout if the limit is reached.
	if ( $pmpro_level_member_limit > 0 && $member_count >= $pmpro_level_member_limit ) {
		wp_redirect( pmpro_url( 'levels' ) );
		exit;
	}

}
add_action('template_redirect', 'my_pmpro_template_redirect_no_spots_available');

/**
 * Hide the level from the Membership Levels page if the limit is reached.
 */
function my_pmpro_levels_array_hide_full_levels( $levels ) {
	// Build the filtered levels array.
	$newlevels = array();

	// Loop through each level.
	foreach ( $levels as $key => $level ) {
		// Get the maximum number of members allowed in this level.
		$pmpro_level_member_limit = get_pmpro_membership_level_meta( $level->id, 'pmpro_level_member_limit', true );

		// Get the count of members in this level.
		$member_count = my_pmpro_get_active_members_in_level( $level->id );

		// If the limit is not set or the limit is not reached, add the level to the new array.
		if ( empty( $pmpro_level_member_limit ) || $member_count < $pmpro_level_member_limit ) {
			$newlevels[ $key ] = $level;
		}
	}

	// Return the filtered levels array.
	return $newlevels;
}
add_filter( 'pmpro_levels_array', 'my_pmpro_levels_array_hide_full_levels' );

/**
 * Show the number of members in a level on the Membership Levels Settings page.
 */
function my_pmpro_membership_levels_table_extra_cols_header_spots( $reordered_levels ) { ?>
	<th><?php esc_html_e( 'Spots Claimed', 'pmpro-customizations' ); ?></th>
	<?php
}
add_action( 'pmpro_membership_levels_table_extra_cols_header', 'my_pmpro_membership_levels_table_extra_cols_header_spots' );

function my_pmpro_membership_levels_table_extra_cols_body_spots( $level ) {
	// Get the maximum number of members allowed in this level.
	$pmpro_level_member_limit = get_pmpro_membership_level_meta( $level->id, 'pmpro_level_member_limit', true );

	// Show empty if the limit is not set.
	if ( empty( $pmpro_level_member_limit ) ) {
		echo '<td>' . __( '&#8212;', 'paid-memberships-pro' ) . '</td>';
		return;
	}

	// Get the count of members in this level.
	$member_count = my_pmpro_get_active_members_in_level( $level->id );

	// Show the spots available.
	echo '<td>';
	echo esc_html( sprintf(
		// translators: %1$s is the number of members in the level and %2$s is the limit.
		__( '%1$s of %2$s', 'pmpro-customizations' ),
		$member_count,
		$pmpro_level_member_limit
	) ); 
	echo '</td>';
}
add_action( 'pmpro_membership_levels_table_extra_cols_body', 'my_pmpro_membership_levels_table_extra_cols_body_spots' );

/**
 * Get the count of active members in a level.
 */
function my_pmpro_get_active_members_in_level( $level_id ) {
	global $wpdb;

	// Get active members in this level.
	$sqlQuery = $wpdb->prepare(
		"SELECT COUNT(*) AS total_active_members 
		FROM {$wpdb->pmpro_memberships_users} AS mu 
		LEFT JOIN {$wpdb->users} AS u ON u.ID = mu.user_id 
		WHERE mu.status = 'active' 
		AND u.ID IS NOT NULL
		AND membership_id = %d",
		$level_id
	);
	return $wpdb->get_var( $sqlQuery );
}
