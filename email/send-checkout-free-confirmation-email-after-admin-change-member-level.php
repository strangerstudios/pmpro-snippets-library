<?php
/**
 * Always send the checkout_free confirmation email in PMPro when changing a user's level in the admin dashboard.
 *
 * title: Send Confirmation Email When Admin Changes User's Level
 * layout: snippet
 * collection: email
 * category: emails
 * link: https://www.paidmembershipspro.com/send-members-a-confirmation-email-when-admins-change-their-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_send_pmpro_confirmation_emails_from_dashboard( $level_id, $user_id, $cancel_level_id ) {
	// If we're not in the dashboard, this is probably a checkout on the frontend.
	if ( ! is_admin() ) {
		return;
	}

	// If you want to exclude certain level ids, add them here.
	$excluded_level_ids = array();

	// Send email if level > 0 and is not excluded.
	if ( ! empty( $level_id ) && ! in_array( $level_id, $excluded_level_ids, true ) ) {
		$email_user        = get_userdata( $user_id );
		$membership_levels = pmpro_getMembershipLevelsForUser( $user_id );

		// In case of MMPU, let's find the specific level we're wondering about.
		foreach ( $membership_levels as $membership_level ) {
			if ( (int) $membership_level->id === (int) $level_id ) {
				$email_user->membership_level = $membership_level;
				break;
			}
		}

		// Create a mock order object.
		$mock_order = new MemberOrder();

		// Set minimal required properties.
		$mock_order->membership_id = $level_id;

		$pmproemail = new PMProEmail();
		$pmproemail->sendCheckoutEmail( $email_user, $mock_order );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_send_pmpro_confirmation_emails_from_dashboard', 10, 3 );
