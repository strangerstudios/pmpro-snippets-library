<?php
/**
 * Customize the WordPress password reset email sent to users.
 *
 * title: Customize the WordPress Password Reset Email
 * layout: snippet-example
 * collection: email
 * category: emails
 * link: TBD
 *
 * This recipe uses a core WordPress filter and is not specific to Paid Memberships Pro.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/* Update the text assigned to the $message variable to customize 
 * the content of the password reset email. 
 * Keep the $reset_link variable in the message so users can access their 
 * password reset link.
*/

function custom_retrieve_password_message( $message, $key, $user_login, $user_data ) {
	$reset_link = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' );
	$site_name  = get_bloginfo( 'name' );

	$message  = "Hello,\n\n";
	$message .= "You requested a password reset for your account on {$site_name}.\n\n";
	$message .= "To reset your password, visit the link below:\n";
	$message .= $reset_link . "\n\n";
	$message .= "If you did not request this, you can safely ignore this email.\n\n";
	$message .= "Regards,\n";
	$message .= $site_name;

	return $message;
}
add_filter( 'retrieve_password_message', 'custom_retrieve_password_message', 10, 4 );