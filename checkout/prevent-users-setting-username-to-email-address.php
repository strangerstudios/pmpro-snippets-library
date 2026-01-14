<?php
/**
 * Prevent users from setting their username to an email address when checking out
 *
 * title: prevent users from setting their username to an email address
 * layout: snippet
 * collection: checkout
 * category: registration
 * link: https://www.paidmembershipspro.com/prevent-users-from-using-an-email-address-as-username/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_registration_checks_no_email_user_login( $continue ) {
	// If there are earlier problems, don't bother checking.
	if ( ! $continue ) {
		return;
	}

	// Make sure the username passed in doesn't look like an email address (contains an @).
	global $username;

	if ( ! empty( $username ) && false !== strpos( $username, '@' ) ) {
		$continue = false;
		pmpro_setMessage( 'Your username must not be an email address', 'pmpro_error' );
	}

	return $continue;
}
add_filter( 'pmpro_registration_checks', 'pmpro_registration_checks_no_email_user_login' );
