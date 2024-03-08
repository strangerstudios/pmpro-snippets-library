<?php
/**
 * Show the user's Membership Number in their membership confirmation email.
 * 
 * Member numbers must be generated using the generate_member_number snippet in this companion code: 
 * https://github.com/strangerstudios/pmpro-snippets-library/tree/dev/misc/generate-unique-membership-number.php
 *
 * title: Show the Member's Unique Membership Number in the Membership Confirmation Email
 * layout: snippet
 * collection: frontend-pages
 * category: membership-number
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_number_email_filter ( $email ) {
	global $wpdb;

	// Only update membership checkout confirmation emails.
	if ( strpos( $email->template, "checkout" ) !== false ) {

		if ( ! empty( $email->data ) && ! empty( $email->data['user_login'] ) ) {

			$user = get_user_by( 'login', $email->data['user_login'] );

			if ( ! empty( $user ) && ! empty( $user->ID ) ) {

				$member_number = get_user_meta( $user->ID, 'member_number', true );

				if ( ! empty( $member_number ) ) {
					$email->body = str_replace( '<p>Membership Level', '<p>Member Number: ' . $member_number . '</p><p>Membership Level', $email->body);
				}
			}
		}
	}

	return $email;
}
add_filter( 'pmpro_email_filter', 'my_pmpro_member_number_email_filter', 30, 2 );
