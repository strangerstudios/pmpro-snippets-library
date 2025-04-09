<?php
/**
 * Adjust the Set Expiration Year after a specific month
 *
 * title: Change Y1 to Y2 specific on a specific month
 * layout: snippet
 * collection: add-ons, pmpro-set-expiration-dates
 * category: expiration date
 * link: 
 * 
 * Automatically adjust Y1-xx-xx to be Y2-xx-xx if the current month is set.
 * Change line 35 for the month to change to next year.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_programmatically_change_set_expiration_date( $raw_date ) {

	// No Set Expiration Date, just bail.
	if ( empty( $raw_date ) ) {
		return $raw_date;
	}
	
	$todays_date =  new DateTime();

	// Get the current month (1-12)
	$month = (int) $todays_date->format('n');
	
	/**
	 * Check if the month is October or later and update the Expiration Date to be 1 year later.
	 * This will keep the month and day in tact.
	 */
	if ( $month >= 10 ) {
		// str_replace 'Y1' with the 'Y2'
		$raw_date = str_replace( 'Y1', 'Y2', $raw_date ); // Here you may adjust the date format to your needs.
	}
	
	return $raw_date;
}
add_filter( 'pmprosed_expiration_date_raw', 'my_pmpro_programmatically_change_set_expiration_date', 10, 1 );
