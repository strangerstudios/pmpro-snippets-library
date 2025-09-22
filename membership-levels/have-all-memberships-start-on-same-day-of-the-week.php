<?php
/**
 * Adjust membership start date to the first following Sunday.
 *
 * title: Have All Memberships Start on Same Day of the Week
 * layout: snippet
 * collection: membership-levels
 * category: checkout
 * link: https://www.paidmembershipspro.com/memberships-start-day-week/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_checkout_start_date( $startdate ) {
	// Which day is it?
	$checkout_day = date( 'N' );

	// Days to sunday.
	$days_to_sunday = 7 - $checkout_day;

	// Add the days.
	$startdate = date( 'Y-m-d', strtotime( '+ ' . $days_to_sunday . ' days' ) );

	return $startdate;
}
add_filter( 'pmpro_checkout_start_date', 'my_pmpro_checkout_start_date' );
