<?php
/**
 * Add PMPro report widgets to the WordPress dashboard.
 *
 * title: Add report widgets to the WordPress Dashboard.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_add_report_widgets_to_dashboard() {
	if( ! defined( 'PMPRO_DIR' )  || ! current_user_can( 'manage_options' ) ){
		return;
	}
	wp_add_dashboard_widget(
		'pmpro_membership_dashboard',
		__( 'Paid Membership Pro Reports' , 'paid-memberships-pro' ),
		'my_pmpro_add_report_widgets_to_dashboard_callback'
	);
}
add_action( 'wp_dashboard_setup', 'my_pmpro_add_report_widgets_to_dashboard' );

function my_pmpro_add_report_widgets_to_dashboard_callback() {
	// Included report pages
	require_once( PMPRO_DIR . '/adminpages/reports/logins.php' );
	require_once( PMPRO_DIR . '/adminpages/reports/memberships.php' );
	require_once( PMPRO_DIR . '/adminpages/reports/sales.php' );

	// Show Visits/Views/Logins report.
	echo '<h3>' . __( 'Visit, Views and Logins', 'paid-memberships-pro' ) . '</h3>';
	pmpro_report_login_widget();
	// Show Membership report.
	echo '<br /><h3>' . __( 'Membership Stats', 'paid-memberships-pro' ) . '</h3>';
	pmpro_report_memberships_widget();
	// Show Sales and Revenue report.
	echo '<br /><h3>' . __( 'Sales and Revenue', 'paid-memberships-pro' ) . '</h3>';
	pmpro_report_sales_widget();
	//show link to all PMPro reports.
	echo '<p style="text-align: center;"><a class="button-primary" href="' . admin_url( 'admin.php?page=pmpro-reports' ) . '">' . __( 'View All Reports', 'paid-memberships-pro' ) . '</a></p>';
}
