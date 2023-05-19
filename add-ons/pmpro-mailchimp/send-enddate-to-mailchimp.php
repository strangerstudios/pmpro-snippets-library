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
function my_pmpro_mailchimp_add_enddate_to_merge_fields( $fields, $user ) {
	// Get user level
	$level = pmpro_getMembershipLevelForUser( $user->ID );
	$expiration_date = 'Never';

	// Update variable value if level has an expiration date
	if ( ! empty( $level->enddate ) ) {
		$expiration_date = date( get_option( 'date_format' ), $level->enddate );
	}
	
	// Create new ENDDATE field
	$new_fields = array(
		'ENDDATE' => $expiration_date,
	);

	// join existing fields with new fields
	$fields = array_merge( $fields, $new_fields );

	return $fields;
}
add_action( 'pmpro_mailchimp_listsubscribe_fields', 'my_pmpro_mailchimp_add_enddate_to_merge_fields', 10, 2 );

/*
	(Optional) Tell PMPro MailChimp to always synchronize user profile updates. By default it only synchronizes if the user's email has changed.
	Requires PMPro Mailchimp v2.0.3 or higher.
*/

add_filter( 'pmpromc_profile_update', '__return_true' );
