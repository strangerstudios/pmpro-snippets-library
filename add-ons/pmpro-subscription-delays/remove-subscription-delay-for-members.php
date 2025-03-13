<?php
/**
 * Remove subscription delay for current or past members. This will remove all subscription delays from checkout for any level if the user is an active member or had a prior level (and is renewing).
 *
 * title: Subscription Delay that can only be used once.
 * layout: snippet
 * collection: add-ons, pmpro-subscription-delays
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_remove_subscription_delay_for_members() {

	// Do not run this code for logged-out users or while in the WordPress dashboard.
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}

	// Check if the user has any old levels at any point in time.
	$prev_member = false;
	if ( ! pmpro_hasMembershipLevel() && is_user_logged_in() ) {
		$order = new MemberOrder();
		$lastorder = $order->getLastMemberOrder( NULL, array( 'success', 'cancelled' ));
			if ( ! empty( $lastorder ) ) {
				$prev_member = true;
			}
	}

	// If user currently has a membership level or previously had a membership level, remove custom trial.
	if ( pmpro_hasMembershipLevel() || $prev_member ) {
		//for backwards compatibility with PMPro < 3.4
		remove_filter( 'pmpro_profile_start_date', 'pmprosd_pmpro_profile_start_date', 10, 2 );
		remove_filter( 'pmpro_checkout_level', 'pmprosd_pmpro_checkout_level' ); //for PMPro 3.4+
		remove_action( 'pmpro_after_checkout', 'pmprosd_pmpro_after_checkout' );
		remove_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3 );
		remove_filter( 'pmpro_level_cost_text', 'pmprosd_level_cost_text', 10, 2 );
		remove_action( 'pmpro_save_discount_code_level', 'pmprosd_pmpro_save_discount_code_level', 10, 2 );
	}
}
add_filter( 'init', 'my_pmpro_remove_subscription_delay_for_members' );