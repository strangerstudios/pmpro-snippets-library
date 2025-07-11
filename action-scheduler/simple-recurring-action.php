<?php
/**
 * Custom recurring action example for PMPro.
 * This code sets up a weekly action that counts new members registered in the past week
 * and sends them a welcome email.
 */
function my_custom_weekly_action() {

	// Each week count up the new members registered on my PMPro site in the past 7 days and send them a welcome email
	$new_members = get_users(
		array(
			'role'       => 'subscriber',
			'fields'     => 'ID',
			'date_query' => array( 'after' => '1 week ago' ),
		)
	);
	if ( empty( $new_members ) ) {
		return; // No new members to process
	}
	// Send them a welcome email
	$welcome_email_subject = 'Welcome to Our Site!';
	$welcome_email_message = 'Thank you for joining us! We are excited to have you on board.';

	// Optionally, send the welcome email immediately
	foreach ( $new_members as $user_id ) {
		wp_mail( get_userdata( $user_id )->user_email, $welcome_email_subject, $welcome_email_message );
	}
}
add_action( 'pmpro_schedule_weekly', 'my_custom_weekly_action' );
