<?php
// Create a registration form field with a custom callback
function my_pmpro_user_field_init_example_save_function() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// define the fields
	$fields = array();

	// Basic Text Field Example
	$fields[] = new PMPro_Field(
		'my_field_name', // input field name, used as meta key
		'text',          // field type
		array(
			'label'         => 'My Field Label', // field label
			'profile'       => true,            // display on user profile
			'save_function' => 'my_custom_callback_function', // use a custom callback function
		)
	);

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Example Group' );

	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Example Group', // location on checkout page
			$field            // PMPro_Field object
		);
	}
}
add_action( 'init', 'my_pmpro_user_field_init_example_save_function' );

// Custom callback function
function my_custom_callback_function( $field_value ) {
	// Do something
}
