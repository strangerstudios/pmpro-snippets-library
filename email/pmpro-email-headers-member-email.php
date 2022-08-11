<?php
/**
 * BCC admin emails on Paid Memberships Pro member emails.
 *
 * title: BCC admin emails on PMPro member emails.
 * layout: snippet
 * collection: email
 * category: admin, bcc
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_email_headers( $headers, $email ) {
	// BCC emails not already going to admin_email.
	if ( $email->email != get_bloginfo( 'admin_email' ) ) {
		$headers[] = 'Bcc:' . get_bloginfo( 'admin_email' );
	}

	return $headers;
}
add_filter( 'pmpro_email_headers', 'my_pmpro_email_headers', 10, 2 );
