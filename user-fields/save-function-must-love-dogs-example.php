<?php
/**
 * Example of using a custom callback function for a user field created with code.
 *
 * title: Example of using a custom callback save function for a user field.
 * layout: snippet-example
 * collection: user-fields
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Create a registration form field with a custom callback
function my_pmpro_init_must_love_dogs_example_save_function() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// define the fields
	$fields = array();

	// Basic Text Field Example
	$fields[] = new PMPro_Field(
		'pet_name', // input field name, used as meta key
		'text',          // field type
		array(
			'label'         => 'Pet Name', // field label
			'profile'       => true,            // display on user profile
			'save_function' => 'my_capitalize_registration_field_value', // use a custom callback function
		)
	);

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Pet Details' );

	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Pet Details', // location on checkout page
			$field            // PMPro_Field object
		);
	}
}
add_action( 'init', 'my_pmpro_init_must_love_dogs_example_save_function' );

// Custom callback function to capitalize string value
function my_capitalize_registration_field_value( $user_id, $field_name, $field_value ) {
	// Do nothing if no value or if not string
	if ( empty( $field_value ) || ! is_string( $field_value ) ) {
		return;
	}
	// Captilize words
	$field_value = ucwords( $field_value );

	// Sanitize before saving to the database.
	$field_value = sanitize_text_field( $field_value );

	// Save to the usermeta table
	update_user_meta( $user_id, $field_name, $field_value );
}
