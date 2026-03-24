<?php
/**
 * Exclude specific PMPro email templates from the email log.
 * Update the $excluded_templates array with the template names
 * you want to skip. All other PMPro emails will still be logged.
 *
 * title: Exclude Specific PMPro Email Templates From the Email Log
 * layout: snippet
 * collection: email
 * category: email-log
 * link: https://www.paidmembershipspro.com/filter-which-emails-are-logged/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_exclude_templates_from_log( $should_log, $email_data ) {
	// Add or remove template names as needed.
	$excluded_templates = array(
		'checkout_paid',
		'checkout_free',
	);

	if ( in_array( $email_data['template'], $excluded_templates, true ) ) {
		return false;
	}

	return $should_log;
}
add_filter( 'pmpro_should_log_email', 'my_pmpro_exclude_templates_from_log', 10, 2 );
