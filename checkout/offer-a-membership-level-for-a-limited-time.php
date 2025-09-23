<?php
/**
 * Prevent registration and stop new signups for a membership level after a given date.
 *
 * title: Offer a Membership Level for a Limited Time
 * layout: snippet
 * collection: checkout
 * category: checkout, membership-levels, registration-check
 * link: https://www.paidmembershipspro.com/offer-a-membership-level-for-a-limited-time/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

global $pmproml_end_date, $pmproml_limited_level_id;
$pmproml_limited_level_id = 1; // change to the ID of the limited-time membership level.
$pmproml_end_date         = '2026/12/31'; // change to the date registration ends, in YYYY/MM/DD format.

/**
 * Prevent registration and stop new signups if checking out after the registration end date.
 */
function pmproml_pmpro_registration_date_checks( $value ) {
	global $wpdb, $pmproml_end_date, $pmproml_limited_level_id;

	$pmpro_level       = pmpro_getLevelAtCheckout();
	$level_id          = $pmpro_level->id;
	$date_format       = 'Y/m/d';
	$registration_date = date( $date_format );
	$limited_level     = pmpro_getLevel( $pmproml_limited_level_id );

	if ( $registration_date >= $pmproml_end_date && (int) $pmproml_limited_level_id === (int) $level_id ) {
		global $pmpro_msg, $pmpro_msgt;
		$pmpro_msg  = "Memberships for this level are no longer available as of $pmproml_end_date";
		$pmpro_msgt = 'pmpro_error';
		$wpdb->update(
			$wpdb->pmpro_membership_levels,
			array(
				'allow_signups' => 0,
			),
			array(
				'id' => $level_id,
			)
		);
		$value = false;
	}
	return $value;
}
add_filter( 'pmpro_registration_checks', 'pmproml_pmpro_registration_date_checks', 10, 1 );

/**
 * Remove level from levels page and stop new signups after the registration end date.
 */
function pmproml_pmpro_hide_limited_levels( $pmpro_levels ) {
	global $wpdb, $pmproml_end_date, $pmproml_limited_level_id;

	$date_format       = 'Y/m/d';
	$registration_date = date( $date_format );
	$limited_level     = pmpro_getLevel( $pmproml_limited_level_id );

	if ( $registration_date >= $pmproml_end_date && isset( $limited_level->allow_signups ) && 0 !== $limited_level->allow_signups ) {
		$wpdb->update(
			$wpdb->pmpro_membership_levels,
			array(
				'allow_signups' => 0,
			),
			array(
				'id' => $pmproml_limited_level_id,
			)
		);
	}
	return $pmpro_levels;
}
add_filter( 'pmpro_levels_array', 'pmproml_pmpro_hide_limited_levels', 10, 1 );
