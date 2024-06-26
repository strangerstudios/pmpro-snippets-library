<?php
/**
 * Send a different 'Application Approved' email based on the member's level.
 *
 * Note that this does not support email templates. Subject & Body should be updated in the code.
 *
 *
 * title: Send a different 'Application Approved' email based on the member's level.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Sends a different 'Application Approved' email based on the member's level.
 *
 * @param array $email   Email template variables.
 *
 * @return array
 */

function my_pmpro_approval_email_filter( $email ) {

	if ( $email->template == 'application_approved' ) {

		if ( $email->data['membership_id'] == 1 ) {
			$email->subject = 'Your Membership 1 Has Been Approved';
			$email->body    = 'This is the content/body of the email for members who are approved for level 1';
		}

		if ( $email->data['membership_id'] == 2 ) {
			$email->subject = 'Your Membership 2 Has Been Approved';
			$email->body    = 'This is the content/body of the email for members who are approved for level 2';
		}
	}

	return $email;
}
add_filter( 'pmpro_email_filter', 'my_pmpro_approval_email_filter', 99, 1 );
