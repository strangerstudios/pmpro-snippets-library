<?php
/**
 * Create a user taxonomy field to capture structured data on users. This code demo includes a select, multi select, checkbox, and radio method. Once the User Taxonomy is registered, you must populate terms on the Users > Your Taxonomy page in the WordPress admin.
 * 
 * title: Create custom user taxonomy fields for structured user profile data.
 * layout: snippet
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_user_fields_user_taxonomy() {

	// Require PMPro
	if ( ! function_exists( 'pmpro_add_user_taxonomy' ) ) {
		return;
	}

	// Add new field group
	pmpro_add_field_group( 'business', 'Business Information', 'Complete the following information about your business.' );

	// Add new user taxonomy with singular and plural name
	pmpro_add_user_taxonomy( 'title', 'titles' );

	// Add new user taxonomy with singular and plural name
	pmpro_add_user_taxonomy( 'industry', 'industries' );

	// Add new user taxonomy with singular and plural name
	pmpro_add_user_taxonomy( 'team', 'team' );

	// Add new user taxonomy with singular and plural name
	pmpro_add_user_taxonomy( 'revenue', 'revenue' );

	// Define the fields
	$fields = array();
	$fields[] = new PMPro_Field(
		'titles',
		'select',
		array(
			'label' => 'Job Title',
			'profile' => true,
			'addmember' => true,
			'taxonomy' => 'title',
		)
	);
	$fields[] = new PMPro_Field(
		'profession',
		'select2',
		array(
			'label' => 'Industry',
			'profile' => true,
			'addmember' => true,
			'taxonomy' => 'industry',
		)
	);
	$fields[] = new PMPro_Field(
		'team',
		'checkbox_grouped',
		array(
			'label' => 'Team Size',
			'profile' => true,
			'addmember' => true,
			'taxonomy' => 'team',
		)
	);
	$fields[] = new PMPro_Field(
		'revenue',
		'radio',
		array(
			'label' => 'Annual Revenue',
			'profile' => true,
			'addmember' => true,
			'taxonomy' => 'revenue',
		)
	);

	// Add fields into a new area of checkout page
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'business',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_user_fields_user_taxonomy' );
