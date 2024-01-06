<?php
/**
 * Change the reports shown on the reports dashboard app.
 *
 * title: Change the reports shown on the reports dashboard app.
 * layout: snippet
 * collection: pmpro-reports-dashboard
 * category: reports
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_reports_dashboard_reports( $reports ) {
	// Remove the active members per level report.
	unset( $reports['members_per_level'] );

	// Rename the Sales report.
	$reports['sales'] = 'You are doing great!';

	// Move the Membership Stats report to the end
	$memberships_report = $reports['memberships'];
	unset( $reports['memberships'] );
	$reports['memberships'] = $memberships_report;
	
	return $reports;
}
add_filter( 'pmpro_reports_dashboard_reports', 'my_pmpro_reports_dashboard_reports' );
