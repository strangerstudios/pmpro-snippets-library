<?php
/**
 * Prevents late-year signups from getting a shrinking membership term when a
 * level's Expiration Date field is set to expire on the current year.
 *
 * title: Automatically adjust a Y1 (or bare Y) expiration date to Y2 after a chosen month.
 * layout: snippet
 * collection: add-ons, pmpro-set-expiration-dates
 * category: expiration date
 * link: https://www.paidmembershipspro.com/adjust-set-expiration-year-after-specific-month/
 * 
 * Automatically adjust Y1-xx-xx to be Y2-xx-xx if the current month is set.
 * Change line 35 for the month to change to next year.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_adjust_expiration_year_after_month( $raw_date ) {

	// No Set Expiration Date configured, nothing to adjust.
	if ( empty( $raw_date ) ) {
		return $raw_date;
	}

	// Change this to the month (1-12) after which new signups should roll to next year.
	$cutoff_month = 10; // October.

	$current_month = (int) gmdate( 'n' );

	if ( $current_month >= $cutoff_month ) {
		// Handle both the shorthand "Y" and the explicit "Y1" current-year placeholder.
		$raw_date = preg_replace( '/^Y-/', 'Y2-', $raw_date );
		$raw_date = str_replace( 'Y1', 'Y2', $raw_date );
	}

	return $raw_date;
}
add_filter( 'pmprosed_expiration_date_raw', 'my_pmpro_adjust_expiration_year_after_month' );