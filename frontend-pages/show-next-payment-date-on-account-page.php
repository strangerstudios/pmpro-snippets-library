<?php
/**
 * Show the next payment date for applicable levels on the Membership Account page.
 * 
 * title: Show Next Payment Date on the Membership Account Page
 * layout: snippet
 * collection: frontend-pages
 * category: next-payment-date
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_next_payment_date_pmpro_membership_expiration_text( $text, $level, $user, $show_time ) {
	// Return early if we're not on the site's frontend.
	if ( is_admin() ) {
		return $text;
	}

	// Return early if the level has an expiration date.
	if ( ! empty( $level->expiration_number ) ) {
		return $text;
	}

	// Get the subscription.
	$subscriptions =  PMPro_Subscription::get_subscriptions_for_user( $user->ID, $level->id );

	// Return early if there are no subscriptions.
	if ( empty( $subscriptions ) ) {
		return $text;
	}

	// Show the next payment date if the subscription is active.
	$subscription = $subscriptions[0];
	$next_payment_date = $subscriptions[0]->get_next_payment_date( get_option( 'date_format' ) );
	$next_payment_time = $subscriptions[0]->get_next_payment_date( get_option( 'time_format' ) );
	if ( ! empty( $next_payment_date ) ) {
		$text = esc_html( sprintf(
			// translators: %1$s is the date and %2$s is the time.
			__( 'Your next payment will process on %1$s at %2$s', 'pmpro-customizations' ),
			esc_html( $next_payment_date ),
			esc_html( $next_payment_time )
		) );
	}

	// Return the text.
	return $text;
}
add_filter( 'pmpro_membership_expiration_text', 'my_pmpro_next_payment_date_pmpro_membership_expiration_text', 10, 4 );
