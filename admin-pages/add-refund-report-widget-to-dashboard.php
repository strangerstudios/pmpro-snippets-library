<?php
/**
 * Add a new "Refund Rate" report widget and page to the PMPro Reports Dashboard.
 *
 * title: Add refund rate report widget and page to PMPro Reports Dashboard.
 * layout: snippet
 * collection: admin-pages
 * category: admin, reports
 * link: https://www.paidmembershipspro.com/refund-rate-report/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Register the report.
 */
global $pmpro_reports;
$pmpro_reports['refunds'] = __( 'Refund Rate', 'pmpro-snippets-library' );

/**
 * Refund Rate widget on Memberships > Reports.
 */
if ( ! function_exists( 'pmpro_report_refunds_widget' ) ) {
	function pmpro_report_refunds_widget() {
		?>
		<span id="pmpro_report_refunds" class="pmpro_report-holder">

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php esc_html_e( 'Sales', 'pmpro-snippets-library' ); ?></th>
						<th><?php esc_html_e( 'Refunds', 'pmpro-snippets-library' ); ?></th>
						<th><?php esc_html_e( 'Refund Rate', 'pmpro-snippets-library' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$periods = array(
						'this month' => esc_html__( 'This Month', 'pmpro-snippets-library' ),
						'this year'  => esc_html__( 'This Year', 'pmpro-snippets-library' ),
						'all time'   => esc_html__( 'All Time', 'pmpro-snippets-library' ),
					);

					foreach ( $periods as $period => $label ) :
						$sales   = pmpro_getSales( $period );
						$refunds = pmpro_getRefunds( $period );
						?>
						<tr>
							<th><?php echo esc_html( $label ); ?></th>
							<td><?php echo number_format_i18n( $sales ); ?></td>
							<td><?php echo number_format_i18n( $refunds ); ?></td>
							<td>
								<?php
								if ( $sales > 0 ) {
									echo esc_html( sprintf( '%.2f%%', ( $refunds / $sales ) * 100 ) );
								} else {
									esc_html_e( 'N/A', 'pmpro-snippets-library' );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php if ( function_exists( 'pmpro_report_refunds_page' ) ) { ?>
				<p class="pmpro_report-button">
					<a class="button button-primary"
					   href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=refunds' ) ); ?>">
						<?php esc_html_e( 'Details', 'pmpro-snippets-library' ); ?>
					</a>
				</p>
			<?php } ?>

		</span>
		<?php
	}
}

/**
 * Refund Rate dedicated report page.
 */
if ( ! function_exists( 'pmpro_report_refunds_page' ) ) {
	function pmpro_report_refunds_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Refund Rate', 'pmpro-snippets-library' ); ?></h1>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php esc_html_e( 'Sales', 'pmpro-snippets-library' ); ?></th>
						<th><?php esc_html_e( 'Refunds', 'pmpro-snippets-library' ); ?></th>
						<th><?php esc_html_e( 'Refund Rate', 'pmpro-snippets-library' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$periods = array(
						'this month' => esc_html__( 'This Month', 'pmpro-snippets-library' ),
						'this year'  => esc_html__( 'This Year', 'pmpro-snippets-library' ),
						'all time'   => esc_html__( 'All Time', 'pmpro-snippets-library' ),
					);

					foreach ( $periods as $period => $label ) :
						$sales   = pmpro_getSales( $period );
						$refunds = pmpro_getRefunds( $period );
						?>
						<tr>
							<th><?php echo esc_html( $label ); ?></th>
							<td><?php echo number_format_i18n( $sales ); ?></td>
							<td><?php echo number_format_i18n( $refunds ); ?></td>
							<td>
								<?php
								if ( $sales > 0 ) {
									echo esc_html( sprintf( '%.2f%%', ( $refunds / $sales ) * 100 ) );
								} else {
									esc_html_e( 'N/A', 'pmpro-snippets-library' );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}

/**
 * Get refunded orders count for a period.
 */
if ( ! function_exists( 'pmpro_getRefunds' ) ) {
	function pmpro_getRefunds( $period, $levels = null ) {

		$cache_key = serialize( $levels );
		$cache     = get_transient( 'pmpro_report_refunds' );
		if ( isset( $cache[ $period ][ $cache_key ] ) ) {
			return (int) $cache[ $period ][ $cache_key ];
		}

		// Determine start date.
		switch ( $period ) {
			case 'this month':
				$startdate = wp_date( 'Y-m-01' );
				break;
			case 'this year':
				$startdate = wp_date( 'Y-01-01' );
				break;
			default:
				$startdate = '';
				break;
		}

		global $wpdb;
		$gateway_environment = pmpro_getOption( 'gateway_environment' );

		$sql = "
			SELECT COUNT(*)
			FROM {$wpdb->pmpro_membership_orders}
			WHERE status = 'refunded'
				AND total > 0
				AND gateway_environment = %s
		";

		$params = array( $gateway_environment );

		if ( ! empty( $startdate ) ) {
			$sql     .= " AND timestamp >= %s";
			$params[] = $startdate;
		}

		if ( ! empty( $levels ) ) {
			$sql .= " AND membership_id IN (" . implode( ',', array_map( 'intval', (array) $levels ) ) . ")";
		}

		$refunds = (int) $wpdb->get_var( $wpdb->prepare( $sql, $params ) );

		// Cache results.
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		$cache[ $period ][ $cache_key ] = $refunds;
		set_transient( 'pmpro_report_refunds', $cache, DAY_IN_SECONDS );

		return $refunds;
	}
}

/**
 * Clear refund report cache when orders change.
 */
function my_pmpro_report_refunds_delete_transient() {
	delete_transient( 'pmpro_report_refunds' );
}
add_action( 'pmpro_updated_order', 'my_pmpro_report_refunds_delete_transient' );
add_action( 'pmpro_added_order', 'my_pmpro_report_refunds_delete_transient' );

