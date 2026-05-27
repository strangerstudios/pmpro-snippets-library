<?php
/**
 * Customize the WordPress password reset email.
 *
 * This is core WordPress functionality — not specific to Paid Memberships Pro.
 *
 * title: Customize the WordPress Password Reset Email
 * layout: snippet
 * collection: email
 * category: login, password
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 */

/**
 * Customize the default email message.
 *
 * @param string  $message    The default email message.
 * @param string  $key        The password reset key.
 * @param string  $user_login The user's login name.
 * @param WP_User $user_data  The WP_User object.
 * @return string Modified email message.
 */
function custom_retrieve_password_message( $message, $key, $user_login, $user_data ) {
	$reset_link = network_site_url( 'wp-login.php?action=rp&key=' . rawurlencode( $key ) . '&login=' . rawurlencode( $user_login ), 'login' );
	$site_name  = get_bloginfo( 'name' );

	$message  = "Hello,\n\n";
	$message .= "Someone has requested a password reset for your account on {$site_name}.\n\n";
	$message .= "To reset your password, visit the link below:\n";
	$message .= $reset_link . "\n\n";
	$message .= "If you did not request this, you can safely ignore this email.\n\n";
	$message .= "Regards,\n";
	$message .= $site_name;

	return $message;
}
add_filter( 'retrieve_password_message', 'custom_retrieve_password_message', 10, 4 );
