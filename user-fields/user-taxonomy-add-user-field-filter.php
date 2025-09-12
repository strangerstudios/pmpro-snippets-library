<?php
/**
 * Extend a user field created with settings to save as a user taxonomy.
 *
 * title: Extend a user field created with settings to save as a user taxonomy.
 * layout: snippet
 * collection: user-fields
 * category: custom-fields
 * link: https://www.paidmembershipspro.com/store-user-profile-fields-custom-user-taxonomy/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_create_location_user_taxonomy_example() {
	// Require PMPro.
	if ( ! function_exists( 'pmpro_add_user_taxonomy' ) ) {
		return;
	}

	// Add new user taxonomy with singular and plural name.
	pmpro_add_user_taxonomy( 'location', 'locations' );
}
add_action( 'init', 'my_pmpro_create_location_user_taxonomy_example' );

function my_pmpro_location_user_field_filter_example( $field, $where ) {
	// Match our field name
	if ( 'member_location' === $field->name ) {
		// Set the field to save as a user taxonomy.
		$field->taxonomy = 'location'; // Set to your taxonomy singular name.
		$field->save_function = array( $field, 'saveTermRelationshipsTable' );
	}

	return $field;
}
add_filter( 'pmpro_add_user_field', 'my_pmpro_location_user_field_filter_example', 10, 2 );
