<?php
/**
 * Change user field type based on the role of the user. This code allows admins to edit a field that is displayed as readonly for users.
 * 
 * title: Change user field type based on the role of the user.
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_change_user_field_type_by_role() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	} 

	// Store our field settings in an array.
	$fields = array();

	// Default to the readonly field type.
	$field_type = 'readonly';

	// We only want to check for logged in users.
	if ( is_user_logged_in() ) {
		// Swap field type if user is administrator role.
		if ( current_user_can( 'manage_options' ) ) {
			$field_type = 'text';
		}
	}

	// Define the fields.
	$fields[] = new PMPro_Field(
		'member_number',
		$field_type,
		array(
			'label'		=> 'Member Number',
			'profile'	=> true,
		)
	);	

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Member Details' );

	// Add the fields into a new field group.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Member Details',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_change_user_field_type_by_role' );
