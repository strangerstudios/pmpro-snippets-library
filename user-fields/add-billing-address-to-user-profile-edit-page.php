<?php
/**
 * Add default billing fields to the user profile edit page for admins and frontend edit profile for members.
 *
 * title: Add billing fields to user profile edit page.
 * layout: snippet
 * collection: user-fields
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function add_billing_fields_to_user_profile_edit() {
	global $pmpro_countries;

	// Require PMPro 2.9 or higher.
	if ( ! defined( 'PMPRO_VERSION' ) || version_compare( PMPRO_VERSION, '2.9.0', '<' ) ) {
		return;
	}

	// Define the fields
	$fields = array();

	// List the fields you want to include
	$address_fields = array(
		'pmpro_bfirstname' => 'First Name',
		'pmpro_blastname'  => 'Last Name',
		'pmpro_baddress1'  => 'Address 1',
		'pmpro_baddress2'  => 'Address 2',
		'pmpro_bcity'      => 'City',
		'pmpro_bstate'     => 'State',
		'pmpro_bzipcode'   => 'Zipcode',
		'pmpro_bcountry'   => 'Country',
		'pmpro_bphone'     => 'Phone',
	);

	foreach ( $address_fields as $name => $label ) {
		// Set the field type and options based on the field name.
		if ( $name === 'pmpro_bcountry' ) {
				$options = $pmpro_countries;
				$type    = 'select';
			} else {
				$options = array();
				$type    = 'text';
			}

		$fields[] = new PMProRH_Field(
			$name,
			$type,
			array(
				'label'           => $label,
				'size'            => 40,
				'profile'         => 'only',
				'options'         => $options,
				'addmember'       => true,
				'required'        => true, // comment this out to make the fields optional.
				'html_attributes' => array( 'required' => 'required' ), // comment this out to make the fields optional.
			)
		);
	}

	// Add new checkout box with label
	pmpro_add_field_group( 'billing_address_profile', 'Billing Address' );

	// Add fields into a new checkout_boxes area of checkout page
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'billing_address_profile', // location on checkout page
			$field            // PMPro_Field object
		);
	}
}
add_action( 'init', 'add_billing_fields_to_user_profile_edit' );
