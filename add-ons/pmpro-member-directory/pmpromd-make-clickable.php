<?php
/**
 * Run certain Member Directory and Profile fields through the make_clickable function.
 *
 * title: Make Member Directory and Profile Fields Clickable
 * layout: snippet
 * collection: pmpro-member-directory
 * category: directory, profile clickable, links
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpromd_make_clickable( $value, $element, $pu, $displayed_levels ) {
	// Process a specific element and make it clickable.
	if ( $element === 'portfolio_url' ) {
		$value = make_clickable( $value );
	}

	// Make all values clickable.
	//if ( ! empty( $value ) ) {
	//	$value = make_clickable( $value );
	//}

	return $value;
}
add_filter( 'pmpromd_get_display_value', 'my_pmpromd_make_clickable', 20, 4 );
