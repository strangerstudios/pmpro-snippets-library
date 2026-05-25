<?php
/**
 * This recipe will create a [pmpro_member_grace_levels] shortcode to show a list of memberships within a grace period.
 *
 * Requires the Add a Grace Period recipe: https://www.paidmembershipspro.com/add-a-grace-period/
 *
 * title: Add a shortcode to show a list of memberships within a grace period.
 * layout: snippet
 * collection: block-shortcodes
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/add-a-grace-period/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_show_member_grace_levels() {

	// Bail if PMPro is not active.
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) ) {
		return '';
	}

	// Bail if the user isn't logged-in.
	$current_user = wp_get_current_user();

	if ( empty( $current_user->ID ) ) {
		return '';
	}

	// Bail if the user has no membership levels.
	$levels = pmpro_getMembershipLevelsForUser( $current_user->ID );

	if ( empty( $levels ) ) {
		return '';
	}

	// Collect the names of any of the user's levels flagged as in a grace period.
	// The 'pmpro_grace_period_level_{level_id}' meta is set by the companion Add a Grace Period recipe.
	$grace_period_levels = array();

	foreach ( $levels as $level ) {

		$level_in_grace_period = (bool) get_user_meta( $current_user->ID, 'pmpro_grace_period_level_' . $level->id, true );

		if ( $level_in_grace_period ) {
			$grace_period_levels[] = $level->name;
		}
	}

	// Bail if none of the user's levels are currently in a grace period.
	if ( empty( $grace_period_levels ) ) {
		return '';
	}

	// Build the notice HTML.
	$output  = '<div class="pmpro-member-grace-period-notice">';
	$output .= '<p><strong>' . esc_html(
		_n(
			'The following membership level is in a grace period due to an overdue renewal:',
			'The following membership levels are in a grace period due to an overdue renewal:',
			count( $grace_period_levels ),
			'pmpro-snippets-library'
		)
	) . '</strong></p>';
	$output .= '<ul>';

	foreach ( $grace_period_levels as $level_name ) {
		$output .= '<li>' . esc_html( $level_name ) . '</li>';
	}

	$output .= '</ul>';
	$output .= '<p>' . esc_html__( 'Please renew your membership to continue enjoying your member benefits.', 'pmpro-snippets-library' ) . '</p>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'pmpro_member_grace_levels', 'my_pmpro_show_member_grace_levels' );
