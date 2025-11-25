<?php // Do not copy this line
/**
 * Create an Enddate Merge Field in Mailchimp Through the Mailchimp API
 * 
 * title: create enddate merge field in mailchimp
 * layout: snippet
 * collection: add-ons
 * category: pmpro-mailchimp
 * link: https://www.paidmembershipspro.com/send-additional-user-information-fields-mailchimp
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_mailchimp_merge_fields( $merge_fields ) {
	// Adds an "ENDDATE" merge field to Mailchimp.
	$merge_fields[] = array(
		'name' => 'ENDDATE',
		'type' => 'text',
	);

	return $merge_fields;
}
add_filter( 'pmpro_mailchimp_merge_fields', 'my_pmpro_mailchimp_merge_fields' );
