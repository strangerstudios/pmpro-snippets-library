<?php
/**
 * Add 'buddypress' field attribute to existing User Fields.
 * 
 * title: Add buddypress to existing User Fields
 * layout: snippet-example
 * collection: pmpro-buddypress
 * category: custom-fields, buddypress, buddyboss, xprofile
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function my_pmpro_add_buddypress_to_existing_fields( $field, $where ) {
	// PMPro User Field => BuddyPress XProfile Field. Adjust this array for each field you need to map.
	$buddypress_mapping = array(
		'company' => 'Company',
		'referral' => 'Referral'
	);

	// Loop through the above array and add the buddypress field to the existing PMPro User Field.
	foreach( $buddypress_mapping as $user_field => $buddypress_field ) {
		if ( $field->name === $user_field ) {
			$field->buddypress = $buddypress_field;
		}
	}

	return $field;
}
add_filter( 'pmpro_add_user_field', 'my_pmpro_add_buddypress_to_existing_fields', 10, 2 );