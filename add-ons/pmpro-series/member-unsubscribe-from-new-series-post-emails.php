<?php
/**
 * Let members opt out of new post notification emails from the Series Add On for Paid Memberships Pro.
 * You must also create a User Field with the name "disable_pmpro_series_email_notifications" to store the user's preference.
 *
 * title: Member Unsubscribe from New Series Post Emails
 * layout: snippet
 * collection: add-ons, pmpro-series
 * category: email
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_disable_series_new_content_email_by_usermeta( $recipient, $email ) {
	$user = get_user_by( 'login', $email->data['user_login'] );
	if ( $email->template == 'new_content' && '1' === $user->disable_pmpro_series_email_notifications ) {
		$recipient = null;
	}
	return $recipient;
}
add_filter( 'pmpro_email_recipient', 'my_pmpro_disable_series_new_content_email_by_usermeta', 10, 2 );
