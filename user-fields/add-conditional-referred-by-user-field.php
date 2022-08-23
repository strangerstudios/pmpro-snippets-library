<?php
/**
 * Add a conditional text field for referral name if a built-in select field named 'how_hear' is set to 'other'.
 * 
 * title: Add a conditional text field for referral name if a built-in select field named 'how_hear' is set to 'other'.
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_referral_source_conditional_user_field() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// Store our field settings in an array.
	$fields = array();
	
	// The conditional field that only shows if the field name 'how_hear' is set to 'other'.
	$fields[] = new PMPro_Field(
		'how_hear_referrer',
		'text',
		array(
			'label'			=> 'Referred by',
			'profile'		=> 'admins',
			'memberslistcsv'	=> true,
			'depends'		=> array(array('id' => 'how_hear', 'value' => 'Other')) 
		)
	);

	// Add our field into the same group as the "How did you hear about us?" field.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Referrer',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_add_referral_source_conditional_user_field' );
