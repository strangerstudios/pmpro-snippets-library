<?php
/**
 * Send a welcome email after a user has been imported
 *
 * title: Send a welcome email after a user has been imported
 * layout: snippet
 * collection: add-ons, import-user-from-csv
 * category: members, import, email
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmproiucsv_send_welcome_email( $user_id ) {

	// If PMPro isn't defined, bail.
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return;
	}

	wp_cache_delete( $user_id, 'users' );

	$sitename = get_bloginfo( 'sitename' );
	$user     = get_userdata( $user_id );

	// look for a membership level
	$membership_id = $user->import_membership_id;

	// get level object
	$level = pmpro_getLevel( $membership_id );

    $my_email = new PMProEmail();

    $my_email->email    = $user->user_email; // who to send the email to - send to user that is checking out.
    $my_email->subject  = sprintf( 'Welcome to %s', $sitename ); // email subject, "Welcome to Sitename"
    $my_email->template = 'Welcome Email'; // custom name of email template.
    $my_email->body     = '<p>Your welcome email body text will go here.</p><p>Site: ' . $sitename . ' (' . site_url() . ')<br />Your login name: ' . $user->user_login . '<br />Membership Level: ' . $level->name . '</p>';

    $my_email->sendEmail();

}
add_action( 'pmproiucsv_post_user_import', 'my_pmproiucsv_send_welcome_email' );
