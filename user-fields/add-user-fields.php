<?php
/**
 * Add user fields using code. This example will add a Business Information section to your checkout and profile pages, with fields for company, referral code, and budget. Some fields are required. Some require level 1 or 2 to use.
 * 
 * title: Add user fields using code.
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add our user fields: company, referral code, and budget.
 * This callback fires during the init action hook.
 */
function my_pmpro_add_user_fields() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// Store our field settings in an array.
	$fields = array();
	
	/*
		Settings for a company text field that is shown in the user profile.
		The text field has a custom size and CSS class. It is required.
		Only members with or checking out for levels 1 and 2 will see the field.
	*/
	$fields[] = new PMPro_Field(
		'company',							// input name and user meta key
		'text',								// type of field
		array(
			'label'		=> 'Company',		// custom field label
			'size'		=> 40,				// input size
			'class'		=> 'company',		// custom class
			'profile'	=> true,			// show in user profile
			'required'	=> true,			// make this field required
			'levels'	=> array(1,2)		// require level 1 or 2 for this field
		)
	);
	
	/*
		Settings for a referral code field.
		All users can set this at checkout.
		Only admins can see it on the user profile page.
	*/
	$fields[] = new PMPro_Field(
		'referral',							// input name and user meta key key
		'text',								// type of field
		array(
			'label'		=> 'Referral Code',	// custom field label
			'profile'	=> 'only_admin'		// only show in profile for admins
		)
	);
	
	/*
		Settings for a budget dropdown field that is shown at checkout.
		An array of options define the possible selections.
	*/
	$fields[] = new PMPro_Field(
		'budget',				// input name and user meta key
		'select',				// type of field
		array(
			'options' => array(	// <option> elements for select field
				'' => '',		// blank option
				'lessthan2000'	=> '&lt; $2,000',
				'2000to5000'	=> '$2,000-$5,000',
				'5000to10000'	=> '$5,000-$10,000',
				'morethan10000'	=> '&gt; $10,000',
			)
		)
	);
	
	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Business Details' );

	// Add all of our fields into that group.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Business Details',		// Which group to add to.
			$field					// PMPro_Field object
		);
	}

	// That's it. See the PMPro User Fields docs for more information.
}
add_action( 'init', 'my_pmpro_add_user_fields' );