<?php
/**
 * Show the "Renew" link on the Membership Account page when a membership is within X days of expiration.
 *
 * title: Change when the Renew link displays on the Membership Account page
 * layout: snippet
 * collection: frontend-pages
 * category: renewal
 * link: https://www.paidmembershipspro.com/schedule-renew-link-display/
 *
 * Change the value of $days to the number of days before expiration you want the renew link to display.
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_show_renewal_link_after_x_days( $r, $level ) {

	// If there is no expiration date, do not show the renew link.
	if ( empty( $level->enddate ) ) {
		return false;
	}

	// Number of days before expiration to begin showing the renew link.
	$days = 30;

	// Get the current timestamp.
	$now = current_time( 'timestamp' );

	// Show the renew link if the membership expires within X days.
	if ( $now + ( $days * 3600 * 24 ) >= $level->enddate ) {
		$r = true;
	} else {
		$r = false;
	}

	return $r;
}
add_filter( 'pmpro_is_level_expiring_soon', 'my_pmpro_show_renewal_link_after_x_days', 10, 2 );
