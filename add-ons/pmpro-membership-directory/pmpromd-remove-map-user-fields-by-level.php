<?php
/**
 * Only show the Member Directory fields at checkout and on profile for specific membership levels.
 *
 * title: 
 * layout: snippet
 * collection: pmpro-member-directory
 * category: directory, user fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_limit_pmpromd_fields_by_level( $field, $where ) {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// Which levels should see these fields?
	$allowed_levels = array( 2, 3 );

	// Member Directory field names to constrain.
	$directory_fields = array(
		'pmpromd_hide_directory',
		'pmpromd_map_optin',
		'pmpromd_street_name',
		'pmpromd_city',
		'pmpromd_state',
		'pmpromd_zip',
		'pmpromd_country',
	);

	// Add 'levels' attribute to the targeted fields.
	if ( in_array( $field->name, $directory_fields, true ) ) {
		$field->levels = $allowed_levels;
		//$field->profile = 'only';
	}

	return $field;
}
add_filter( 'pmpro_add_user_field', 'my_limit_pmpromd_fields_by_level', 10, 2 );
