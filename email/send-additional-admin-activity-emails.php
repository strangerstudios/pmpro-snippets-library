<?php
/**
 * Send additional admin activity emails during the Admin Activity Email cron
 *
 * title: Send Additional Admin Activity Emails
 * layout: snippet
 * collection: email
 * category: cron jobs, emails
 * link: https://www.paidmembershipspro.com/custom-admin-activity-email-frequency/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_send_additional_admin_activity_emails() {
	$admin_activity_email = new PMPro_Admin_Activity_Email();

	// Send an additional daily activity email to example1@email.com.
	$admin_activity_email->sendAdminActivity( 'day', 'example1@email.com' );

	// Send an additional weekly activity email on Mondays to example2@email.com.
	if ( 'Mon' === date( 'D' ) ) {
		$admin_activity_email->sendAdminActivity( 'week', 'example2@email.com' );
	}
}
add_action( 'pmpro_cron_admin_activity_email', 'my_pmpro_send_additional_admin_activity_emails', 12 );
