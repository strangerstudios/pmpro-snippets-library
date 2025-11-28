<?php
/**
 * Display a report of membership level changes in the PMPro admin.
 *
 * title: Membership Level Upgrades and Downgrades Report
 * layout: snippet
 * collection: admin-pages
 * category: admin,reports
 * link: https://www.paidmembershipspro.com/new-report-view-membership-level-changes-upgrades-downgrades/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Define the level change reports.
 *
 * Update this array for your desired reports.
 * Format: "Report Name" => array(initial_level_id, current_level_id)
 */
global $pmpro_reports_level_changes;
$pmpro_reports_level_changes = array(
	'Members upgrading from Level 1 to 2' => array(1,2),
	'Members downgrading from Level 3 to 2' => array(3,2),
);

/**
 * Add the "Membership Level Changes" report to the PMPro reports array.
 */
global $pmpro_reports;
$pmpro_reports['changes'] = __('Membership Level Changes', 'pmpro-reports-changes');

/**
 * Report Widget: Displays a summary table of level changes.
 *
 * This widget is shown in the Reports Dashboard for quick viewing.
 *
 * @since 1.0
 */
function pmpro_report_changes_widget() {
	?>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e('Name', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Initial Level', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Current Level', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Count', 'pmpro-reports-changes'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			global $wpdb, $pmpro_reports_level_changes;

			foreach ( $pmpro_reports_level_changes as $key => $pmpro_report_level_changes ) {
				$current_level = pmpro_getLevel($pmpro_report_level_changes[1]);
				$initial_level = pmpro_getLevel($pmpro_report_level_changes[0]);

				$changes_count_query = $wpdb->prepare(
					"
					SELECT COUNT(mu1.id)
					FROM $wpdb->pmpro_memberships_users mu1
					LEFT JOIN $wpdb->pmpro_memberships_users mu2 
						ON mu1.user_id = mu2.user_id 
						AND mu2.membership_id = %d 
						AND mu2.id < mu1.id
					WHERE mu1.membership_id = %d
						AND mu1.status = 'active'
						AND mu2.id IS NOT NULL
					",
					$initial_level->id,
					$current_level->id
				);

				$changes_count = $wpdb->get_var($changes_count_query);
				?>
				<tr>
					<th scope="row"><?php echo esc_html( $key ); ?></th>
					<td><?php echo empty( $initial_level ) ? '-' : esc_html( $initial_level->name ); ?></td>
					<td><?php echo esc_html( $current_level->name ); ?></td>
					<td><strong><?php echo number_format_i18n( $changes_count ); ?></strong></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}

/**
 * Report Page: Displays a full-page view of level changes.
 *
 * This report can be accessed under Memberships > Reports > Membership Level Changes.
 *
 * @since 1.0
 */
function pmpro_report_changes_page() {
	?>
	<h1><?php esc_html_e( 'Membership Level Changes Report', 'pmpro-reports-changes' ); ?></h1>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e('Name', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Initial&nbsp;Level', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Current&nbsp;Level', 'pmpro-reports-changes'); ?></th>
				<th><?php esc_html_e('Count', 'pmpro-reports-changes'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			global $wpdb, $pmpro_reports_level_changes;

			foreach ( $pmpro_reports_level_changes as $key => $pmpro_report_level_changes ) {
				$current_level = pmpro_getLevel($pmpro_report_level_changes[1]);
				$initial_level = pmpro_getLevel($pmpro_report_level_changes[0]);

				$changes_count_query = $wpdb->prepare(
					"
					SELECT COUNT(mu1.id)
					FROM $wpdb->pmpro_memberships_users mu1
					LEFT JOIN $wpdb->pmpro_memberships_users mu2 
						ON mu1.user_id = mu2.user_id 
						AND mu2.membership_id = %d 
						AND mu2.id < mu1.id
					WHERE mu1.membership_id = %d
						AND mu1.status = 'active'
						AND mu2.id IS NOT NULL
					",
					$initial_level->id,
					$current_level->id
				);

				$changes_count = $wpdb->get_var( $changes_count_query );
				?>
				<tr>
					<th scope="row"><?php echo esc_html( $key ); ?></th>
					<td><?php echo empty( $initial_level ) ? '-' : esc_html( $initial_level->name ); ?></td>
					<td><?php echo esc_html( $current_level->name ); ?></td>
					<td><strong><?php echo number_format_i18n( $changes_count ); ?></strong></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
