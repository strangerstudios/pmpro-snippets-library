<?php
/**
 * Create a custom report
 *
 * title: Create a custom report
 * layout: snippet
 * collection: admin-pages
 * category: reports
 * link: https://www.paidmembershipspro.com/custom-reports-memberships-admin/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * Learn more at https://www.paidmembershipspro.com/custom-reports-memberships-admin/
 */

/**
 * Register a custom report on the Memberships > Reports screen.
 *
 * Each report needs a slug and title here, plus two functions named after the slug:
 * pmpro_report_{slug}_widget() renders the summary box on the Reports dashboard.
 * pmpro_report_{slug}_page() renders the detail view when someone clicks "Details".
 *
 * @param array $pmpro_reports Registered reports, keyed by slug.
 * @return array
 */
function my_pmpro_register_sample_report( $pmpro_reports ) {
	$pmpro_reports['sample'] = __( 'My Sample Report', 'pmpro-snippets-library' );
	return $pmpro_reports;
}
add_filter( 'pmpro_registered_reports', 'my_pmpro_register_sample_report' );

/**
 * Render the summary widget on the Reports dashboard.
 *
 * @return void
 */
function pmpro_report_sample_widget() { ?>
	<span id="pmpro_report_sample" class="pmpro_report-holder">
		<p><?php esc_html_e( 'Hi! I am a sample report.', 'pmpro-snippets-library' ); ?></p>
		<?php if ( function_exists( 'pmpro_report_sample_page' ) ) { ?>
			<p class="pmpro_report-button">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=sample' ) ); ?>"><?php esc_html_e( 'Details', 'pmpro-snippets-library' ); ?></a>
			</p>
		<?php } ?>
	</span>
	<?php
}

/**
 * Render the detail view for the report.
 *
 * @return void
 */
function pmpro_report_sample_page() { ?>
	<h1 class="wp-heading-inline"><?php esc_html_e( 'This is a Sample', 'pmpro-snippets-library' ); ?></h1>
	<p><?php esc_html_e( 'This report demonstrates how to add a custom report to PMPro.', 'pmpro-snippets-library' ); ?></p>
	<?php
}
