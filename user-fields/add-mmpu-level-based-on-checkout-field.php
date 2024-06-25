<?php
/**
 * Assign additional membership levels based on fields at checkout.
 *
 * This recipe requires Paid Memberships Pro and the Multiple Memberships Per User (MMPU) Add On.
 * Use this recipe as an example: you must change the field label, key, and membership level IDs to fit your needs.
 * 
 * title: Assign additional membership levels based on fields at checkout.
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_mmpu_add_level_via_field_at_checkout() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	} 
 
	// Store our field settings in an array.
	$fields = array();
 
	// Define the fields.
	$fields[] = new PMPro_Field(
		'include_level_2',
		'checkbox',
		array(
			'label'		=> 'Include Level 2 Content?',
			'save_function'  => 'my_pmpro_mmpu_add_level_field_save_function',
			'profile'   => true,
		)
	);
 
	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Additional Options' );
 
	// Add the fields into a new area of the checkout page
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Additional Options',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_mmpu_add_level_via_field_at_checkout' );
 
/**
 * Give users an extra level based on a profile field selected at checkout or on profile page.
 */
function my_pmpro_mmpu_add_level_field_save_function( $user_id, $field_name, $value ) {	
	
	// Check field and give user level if appropriate.
	if ( $field_name == 'include_level_2' ) {
		if ( $value == 1 ) {
			pmpro_changeMembershipLevel( 2, $user_id );
			update_user_meta( $user_id, 'include_level_2', 1 );
		} else {
			pmpro_cancelMembershipLevel( 2, $user_id );
			update_user_meta( $user_id, 'include_level_2', 0 );
		}
	}
}
