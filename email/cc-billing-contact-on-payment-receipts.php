<?php
/**
 * Add a billing contact to selected Paid Memberships Pro billing emails.
 *
 * Reads the "billing_contact_email" User Field on the member and adds
 * that address as a Cc on the templates listed in $billing_templates below.
 *
 * title: Add a Billing Contact to PMPro Billing Emails
 * layout: snippet
 * collection: email
 * category: cc, billing, user fields
 * link: https://www.paidmembershipspro.com/add-a-billing-contact-to-pmpro-recurring-payment-receipts-and-other-emails/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_billing_contact_cc( $headers, $email ) {
	// The templates that should be copied to the billing contact.
	// Add or remove slugs here to control which emails are copied.
	$billing_templates = array(
		'invoice',         // Recurring Payment Receipt
		'checkout_paid',   // Initial paid checkout receipt
		'billing_failure', // Payment Failure
	);

	if ( ! in_array( $email->template, $billing_templates, true ) ) {
		return $headers;
	}

	// $email->email is the member's email address for these templates.
	$member = get_user_by( 'email', $email->email );
	if ( empty( $member ) ) {
		return $headers;
	}

	// Look up the billing contact from the User Field on the member's profile.
	$billing_contact = get_user_meta( $member->ID, 'billing_contact_email', true );
	if ( empty( $billing_contact ) || ! is_email( $billing_contact ) ) {
		return $headers;
	}

	$headers[] = 'Cc: ' . sanitize_email( $billing_contact );

	return $headers;
}
add_filter( 'pmpro_email_headers', 'my_pmpro_billing_contact_cc', 10, 2 );
