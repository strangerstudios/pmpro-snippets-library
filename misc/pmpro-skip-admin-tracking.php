<?php
/**
 * Remove stats/report tracking for admins.
 *
 * title: Remove stats/report tracking for admins
 * layout: snippet
 * collection: misc
 * category: stats, reporting
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_remove_admin_tracking_skip() {
	if ( current_user_can( 'administrator' ) ) {
		remove_action( 'wp_head', 'pmpro_report_login_wp_views' );
	}
}
 add_action( 'init', 'pmpro_remove_admin_tracking_skip' );

function pmpro_pre_login_check( $user_login, $user ) {
	if ( $user->has_cap( 'administrator' ) ) {
		remove_action( 'wp_login', 'pmpro_report_login_wp_login', 10, 2 );
	}
}
 add_action( 'wp_login', 'pmpro_pre_login_check', 9, 2 );

function pmpro_remove_visits_check() {
	if ( current_user_can( 'administrator' ) ) {
		remove_action( 'wp', 'pmpro_report_login_wp_visits' );
	}
}
 add_action( 'wp', 'pmpro_remove_visits_check', 9 );
