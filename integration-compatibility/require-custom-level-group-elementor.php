<?php
/**
 * Create a group of membership levels to protect Elementor content with fewer settings and less need for future updates.
 * Learn more at https://www.paidmembershipspro.com/group-levels-elementor/
 *
 * title: Group membership levels to more easily protect content in Elementor.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, restriction, elementor
 * link: https://www.paidmembershipspro.com/group-levels-elementor/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Define levels for the custom group of levels.
 */
global $pmpro_elementor_group_levels;
$pmpro_elementor_group_levels = array( 1, 2, 3 );

/**
 * Add the "My Custom Levels Group" item to the PMPro field in Elementor editor.
 */
function my_pmpro_elementor_levels_array_custom( $levels ) {
	// Add a new selection to the Require Membership Level advanced setting in Elementor.
	$levels['my_pmpro_elementor_custom_levels'] = __( 'My Custom Levels Group' );

	// Return the array of items for the dropdown with our new item added.
	return $levels;
}
add_filter( 'pmpro_elementor_levels_array', 'my_pmpro_elementor_levels_array_custom', 10, 1 );

/**
 * Callback to check the custom setting against our levels group before showing content for PMPro < v3.5.
 */
function my_pmpro_elementor_has_access_custom( $access, $element, $restricted_levels ) {
	global $pmpro_elementor_group_levels;
	// Check if the element is restricted for our custom level group item.
	if ( in_array( 'my_pmpro_elementor_custom_levels', $restricted_levels ) ) {
		// Require a membership level from our custom group to view this content.
		if ( pmpro_hasMembershipLevel( $pmpro_elementor_group_levels ) ) {
			return true;
		}
	}

	// Return whether the user has access to this content.
	return $access;
}

/**
 * Callback to check the custom setting against our levels group before showing content for PMPro v3.5+.
 */
function my_pmpro_elementor_has_membership_level_custom( $has_level, $user_id, $levels ) {
	global $pmpro_elementor_group_levels;
	// Bail if the user has access already.
	if ( $has_level ) {
		return $has_level;
	}

	// Bail if our custom level group item is not in the required levels.
	if ( ! is_array( $levels ) && ! in_array( 'my_pmpro_elementor_custom_levels', $levels ) ) {
		return $has_level;
	}

	// Require a membership level from our custom group to view this content.
	if ( pmpro_hasMembershipLevel( $pmpro_elementor_group_levels ) ) {
		$has_level = true;
	}

	return $has_level;
}

/**
 * Hook into pmpro_has_membership_level if running PMPro v3.5+.
 * Otherwise, hook into pmpro_elementor_has_access.
 */
function my_pmpro_hook_elementor_access_filters() {
	// If PMPRO_VERSION is not defined, bail.
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return;
	}

	if ( version_compare( PMPRO_VERSION, '3.5', '>=' ) ) {
		add_filter( 'pmpro_has_membership_level', 'my_pmpro_elementor_has_membership_level_custom', 10, 3 );
	} else {
		add_filter( 'pmpro_elementor_has_access', 'my_pmpro_elementor_has_access_custom', 10, 3 );
	}
}
add_action( 'init', 'my_pmpro_hook_elementor_access_filters' );
