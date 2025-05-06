<?php
/**
 * Create and Display Custom Graduation Year Field on Membership Card
 *
 * This recipe creates a custom field using the PMPro Register Helper Add On and displays its value
 * on the Membership Card.
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

//Create the custom field
function my_pmprorh_init(){
	//don't break if Register Helper is not loaded
	if(!function_exists( 'pmprorh_add_registration_field' )) {
		return false;
	}

	$fields[] = new PMProRH_Field(
		'graduation_year',						// input name, will also be used as meta key
		'textarea',							// type of field
		array(
			'label'		=> 'Graduation Year'	,
			'profile' => true,
			'required' => true
		)
	);

	//add the fields into a new checkout_boxes are of the checkout page
	foreach($fields as $field)
		pmprorh_add_registration_field(
			'checkout_boxes',				// location on checkout page
			$field						// PMProRH_Field object
		);
	//that's it. see the PMPro Register Helper readme for more information and examples.
}
add_action( 'init', 'my_pmprorh_init' );

//Now display the Graduation Year field on the membership card.
function my_pmpro_add_rh_fields_after_member_card( $pmpro_membership_card_user, $print_sizes, $qr_code, $qr_data ){

	$graduation_year = get_user_meta( $pmpro_membership_card_user->ID, 'graduation_year', true );

	if( $graduation_year !== "" ){
		echo "<p>Graduation Year: ".$graduation_year."</p>"; 
		//Wrapping content in <p> tags will help with consistent spacing
	}
	
}
add_action( 'pmpro_membership_card_after_card', 'my_pmpro_add_rh_fields_after_member_card', 10, 4 );