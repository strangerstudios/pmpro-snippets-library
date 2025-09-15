<?php
/**
 * Send PMPro admin change email after registering a member through a PMPro Zapier zap.
 *
 * title: Send the Admin Change Email to Members Added via Zapier
 * layout: snippet
 * collection: add-ons
 * category: email
 * link: https://www.paidmembershipspro.com/send-signup-emails-to-zapier-added-pmpro-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmproz_send_admin_change_email_after_add_member( $user_id, $level_id ){
	$user = get_userdata( $user_id );
	$pmpro_email = new PMProEmail();
	$pmpro_email->sendAdminChangeEmail( $user );
}
add_action( 'pmproz_after_add_member', 'my_pmproz_send_admin_change_email_after_add_member', 10, 2 );
