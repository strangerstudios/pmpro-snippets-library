<?php
/**
 * Example of adding a save function custom callback function to a field
 * using the "pmpro_add_user_field" filter hook.
 *
 * title: Example add custom callback save function for a user field.
 * layout: snippet-example
 * collection: user-fields
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_user_field_add_save_function_example( $field, $where ) {
	// Match our field name
	if ( 'field_name' === $field->name ) {
		// Add save function for our field.
		$field->save_function = 'custom_callback_function_name'; // Set to your callback function name.
	}

	return $field;
}
add_filter( 'pmpro_add_user_field', 'my_pmpro_user_field_add_save_function_example', 10, 2 );

// Custom callback function
function custom_callback_function_name( $field_value ) {
	// Do something
}
