<?php // Do not copy this line

/**
 * Sends the membership expiry date to mailchimp merge fields where applicable
 * 
 * title: Send enddate to mailchimp
 * layout: snippet
 * collection: add-ons
 * category: pmpro-mailchimp
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_action( 'pmpro_mailchimp_listsubscribe_fields', 'my_pmpro_mailchimp_listsubscribe_fields', 10, 2 );

function my_pmpro_mailchimp_listsubscribe_fields( $fields, $user ) {
	// Get user level
	$level = pmpro_getMembershipLevelForUser( $user->ID );
	// Declare variable and set default
	$expiration_date = 'N/A';
	// Update variable value if level has an expiration date
	if ( ! empty( $level->enddate ) ) {
		$expiration_date = date( 'd/m/Y', $level->enddate );
	}
	// Create new ENDDATE field
	$new_fields = array(
		'ENDDATE' => $expiration_date,
	//	'FNAME' => $user->first_name, // uncomment and edit if you need to send other fields to Mailchimp
	);
	// join existing fields with new fields
	$fields = array_merge( $fields, $new_fields );

	return $fields;
}

/*
	(Optional) Tell PMPro MailChimp to always synchronize user profile updates. By default it only synchronizes if the user's email has changed.
	Requires PMPro Mailchimp v2.0.3 or higher.
*/

add_filter( 'pmpromc_profile_update', '__return_true' );
