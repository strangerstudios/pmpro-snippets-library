<?php
/**
 * Create and Display Custom Graduation Year Field on Membership Card
 *
 * This recipe creates a custom field using the PMPro Register Helper Add On and displays its value
 * on the Membership Card.
 * 
 * Requires Membership Card V2.0+
 *
 * title: Create and Display Custom Graduation Year Field on Membership Card
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: register-helper
 * link: https://www.paidmembershipspro.com/customize-membership-card-wordpress/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_create_user_fields_with_code(){

	$fields[] = new PMPro_Field(
		'graduation_year',						// input name, will also be used as meta key
		'textarea',							// type of field
		array(
			'label'		=> 'Graduation Year',
			'profile' => true,
			'required' => true
		)
	);

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Graduation Details' );
 
	// Add all of our fields into that group.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Graduation Details',	// Which group to add to.
			$field					// PMPro_Field object
		);
	}
}
add_action( 'init', 'my_pmpro_create_user_fields_with_code' );

//Now display the Graduation Year field on the membership card.
function my_pmpro_add_user_fields_after_member_card( $content, $pmpro_membership_card_user, $atts ) {
	$graduation_year = get_user_meta( $pmpro_membership_card_user->ID, 'graduation_year', true );

	if ( $graduation_year !== "" ) {
		$content[] = "<strong>Graduation Year:</strong> " . $graduation_year; 
	}
	
	return $content;
}
add_filter( 'pmpro_membership_card_right', 'my_pmpro_add_user_fields_after_member_card', 10, 3 );
