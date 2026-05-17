<?php
/**
 * Rate limit password reset requests by IP address.
 *
 * title: Rate limit password reset requests
 * layout: snippet
 * collection: misc
 * category: security, login
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_rate_limit_password_resets( $errors ) {
	$ip    = $_SERVER['REMOTE_ADDR'];
	$key   = 'my_pwreset_' . md5( $ip );
	$count = (int) get_transient( $key );

	/**
	 * Cap password reset requests at 3 per IP address per hour. This helps
	 * slow down automated abuse of the lost-password form without affecting
	 * legitimate users who occasionally need a reset.
	 *
	 * Adjust the limit or window by changing the 3 and HOUR_IN_SECONDS values below.
	 */
	if ( $count >= 3 ) {
		$errors->add( 'too_many_requests', 'Too many password reset requests. Please try again later.' );
		return;
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
}
add_action( 'lostpassword_post', 'my_rate_limit_password_resets' );
