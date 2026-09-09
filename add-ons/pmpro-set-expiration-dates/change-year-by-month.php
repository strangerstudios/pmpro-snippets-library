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
 * The Set Expiration Date add-on stores its raw expiration string using year
 * placeholders: "Y1" (or the shorthand "Y") is the current year and "Y2" is the
 * next year. Once the site's current month reaches the cutoff month set in
 * $cutoff_month, this recipe rewrites the current-year placeholder to the
 * next-year placeholder so new signups expire the following year instead of
 * the current one. Change $cutoff_month to set the month that rolls forward.
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

	// First month (1-12) that rolls new signups forward to next year. Inclusive:
	// this month and every month after it roll forward.
	$cutoff_month = 10; // October.

	$current_month = (int) current_time( 'n' );

	if ( $current_month >= $cutoff_month ) {
		// Handle both the shorthand "Y" and the explicit "Y1" current-year placeholder.
		$raw_date = preg_replace( '/^Y-/', 'Y2-', $raw_date );
		$raw_date = str_replace( 'Y1', 'Y2', $raw_date );
	}

	return $raw_date;
}
add_filter( 'pmprosed_expiration_date_raw', 'my_pmpro_adjust_expiration_year_after_month' );