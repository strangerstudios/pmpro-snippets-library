<?php
/**
 * Score members and manage them in a sales pipeline from a Lead Scoring report.
 *
 * title: Lead Scoring CRM Report For Paid Memberships Pro
 * layout: snippet
 * collection: misc
 * category: reports, crm
 * link: https://www.paidmembershipspro.com/build-lead-scoring-crm-report-inside-paid-memberships-pro/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
 * ===========================================================================
 * CONFIG — the only things you'd change for your own site.
 * ===========================================================================
 */

// The meta keys of the two fields you created in the GUI.
define( 'PMPROCRM_PHONE_META', 'phone' );
define( 'PMPROCRM_STAGE_META', 'dog_stage' );

/**
 * The "where are you at?" answers and how many points each is worth.
 * The key must match the option VALUE of your Select field.
 * Higher score = closer to buying / higher need.
 */
function pmprocrm_stages() {
	return array(
		'ready-1on1'       => array(
			'label' => 'Ready for one-on-one help',
			'score' => 12,
		),
		'behavior-problem' => array(
			'label' => 'Working through a specific problem',
			'score' => 10,
		),
		'new-dog'          => array(
			'label' => 'Just brought home a new dog or puppy',
			'score' => 8,
		),
		'ongoing'          => array(
			'label' => 'Wants ongoing training and guidance',
			'score' => 7,
		),
		'browsing'         => array(
			'label' => 'Just curious, looking around',
			'score' => 3,
		),
	);
}

/**
 * Pages we treat as high-intent. Each view by a logged-in member adds its
 * weight to the score. Key by page slug. The "membership-checkout" entry is
 * detected automatically via pmpro_is_checkout() — no slug needed.
 */
function pmprocrm_tracked_pages() {
	return array(
		'membership-levels'   => 3, // The join / pricing page.
		'membership-checkout' => 5, // Landed on checkout = strong intent.
		'about'               => 8, // Looking at the premium 1:1 offer = hottest.
	);
}

// Sales-pipeline statuses (stored in the pmprocrm_status user meta).
function pmprocrm_statuses() {
	return array(
		'new'          => 'Unassigned',
		'qualified'    => 'Qualified',
		'contacted'    => 'Contacted',
		'converted'    => 'Converted',
		'disqualified' => 'Disqualified',
		'stale'        => 'Stale',
	);
}

/*
 * ===========================================================================
 * 1. TRACK high-intent page views onto the member record.
 * ===========================================================================
 */

function pmprocrm_track_page_views() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = get_current_user_id();
	$tracked = pmprocrm_tracked_pages();

	// Which tracked page are we on, if any?
	$current = '';
	if ( function_exists( 'pmpro_is_checkout' ) && pmpro_is_checkout() && isset( $tracked['membership-checkout'] ) ) {
		$current = 'membership-checkout';
	} else {
		foreach ( array_keys( $tracked ) as $slug ) {
			if ( 'membership-checkout' !== $slug && is_page( $slug ) ) {
				$current = $slug;
				break;
			}
		}
	}

	if ( empty( $current ) ) {
		return;
	}

	$data = get_user_meta( $user_id, 'pmprocrm_page_views', true );
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	if ( ! isset( $data[ $current ] ) ) {
		$data[ $current ] = array(
			'count' => 0,
			'last'  => '',
		);
	}
	++$data[ $current ]['count'];
	$data[ $current ]['last'] = current_time( 'mysql' );

	update_user_meta( $user_id, 'pmprocrm_page_views', $data );

	// Keep the stored score (and its ranking) fresh as behavior changes.
	pmprocrm_calculate_lead_score( $user_id );
}
add_action( 'template_redirect', 'pmprocrm_track_page_views' );

/*
 * ===========================================================================
 * 2. SCORE the member: stage answer + behavior + phone + paid.
 * ===========================================================================
 */

function pmprocrm_calculate_lead_score( $user_id = null ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	if ( empty( $user_id ) ) {
		return 0;
	}

	$score = 0;

	// Where are they at? This is the one GUI field that does the most work.
	$stage  = get_user_meta( $user_id, PMPROCRM_STAGE_META, true );
	$stages = pmprocrm_stages();
	if ( ! empty( $stage ) && isset( $stages[ $stage ] ) ) {
		$score += $stages[ $stage ]['score'];
	}

	// Behavior: high-intent page views.
	$views   = get_user_meta( $user_id, 'pmprocrm_page_views', true );
	$tracked = pmprocrm_tracked_pages();
	if ( is_array( $views ) ) {
		foreach ( $views as $slug => $data ) {
			$weight = isset( $tracked[ $slug ] ) ? $tracked[ $slug ] : 1;
			$score += $weight * intval( $data['count'] );
		}
	}

	// Phone on file = real intent.
	if ( ! empty( get_user_meta( $user_id, PMPROCRM_PHONE_META, true ) ) ) {
		$score += 7;
	}

	// Already paying? Bump it.
	$level = pmprocrm_get_main_level_for_user( $user_id );
	if ( ! empty( $level ) && ! empty( $level->initial_payment ) && floatval( $level->initial_payment ) > 0 ) {
		$score += 10;
	}

	update_user_meta( $user_id, 'pmprocrm_lead_score', $score );

	return $score;
}
// Recalculate after checkout and whenever a profile is saved.
add_action( 'pmpro_after_checkout', 'pmprocrm_calculate_lead_score', 30 );
add_action( 'profile_update', 'pmprocrm_calculate_lead_score', 30 );

/*
 * ===========================================================================
 * 3. PIPELINE — the "CRM view" under Memberships > Reports.
 * ===========================================================================
 */

// First active membership level for a user (good enough for a demo).
function pmprocrm_get_main_level_for_user( $user_id ) {
	if ( empty( $user_id ) || ! function_exists( 'pmpro_getMembershipLevelsForUser' ) ) {
		return null;
	}
	$levels = pmpro_getMembershipLevelsForUser( $user_id );
	return ! empty( $levels ) ? reset( $levels ) : null;
}

// Register the report with PMPro.
global $pmpro_reports;
$pmpro_reports['pmprocrm'] = 'Lead Scoring';

// Dashboard widget — just a button to the full report.
function pmpro_report_pmprocrm_widget() {
	?>
	<span id="pmpro_report_pmprocrm" class="pmpro_report-holder">
		<p class="pmpro_report-button">
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=pmprocrm' ) ); ?>">Details</a>
		</p>
	</span>
	<?php
}

// Save a status change from the dropdown.
function pmprocrm_handle_status_action() {
	if ( empty( $_GET['pmprocrm_action'] ) || 'update_status' !== $_GET['pmprocrm_action'] ) {
		return;
	}
	if ( empty( $_GET['user_id'] ) || empty( $_GET['_wpnonce'] ) || ! isset( $_GET['new_status'] ) ) {
		return;
	}

	$user_id = intval( $_GET['user_id'] );
	$status  = sanitize_text_field( wp_unslash( $_GET['new_status'] ) );

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'pmprocrm_status_' . $user_id ) ) {
		wp_die( 'Security check failed.' );
	}
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	if ( array_key_exists( $status, pmprocrm_statuses() ) ) {
		update_user_meta( $user_id, 'pmprocrm_status', $status );
	}
}
add_action( 'admin_init', 'pmprocrm_handle_status_action' );

// The report page: every member, scored and ranked, with a pipeline status.
function pmpro_report_pmprocrm_page() {
	global $wpdb;

	$statuses = pmprocrm_statuses();
	$stages   = pmprocrm_stages();

	// Read-only report tab filter; no state change, so no nonce is required.
	$status = ! empty( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$base   = admin_url( 'admin.php?page=pmpro-reports&report=pmprocrm' );
	?>
	<div class="metabox-holder">
		<h1>Lead Scoring</h1>
		<p>Every member, scored on what they told us plus what they did, with a sales-pipeline status you control. Your membership site, working as a CRM.</p>

		<ul class="subsubsub">
			<?php
			$tabs = array_merge( array( 'all' => 'All' ), $statuses );
			$i    = 0;
			foreach ( $tabs as $key => $label ) {
				++$i;
				$url        = add_query_arg( array( 'status' => $key ), $base );
				$is_current = ( $status === $key );
				echo '<li><a href="' . esc_url( $url ) . '"' . ( $is_current ? ' class="current"' : '' ) . '>' . esc_html( $label ) . '</a>';
				echo ( $i < count( $tabs ) ) ? ' <span class="sep">|</span> ' : '';
				echo '</li>';
			}
			?>
		</ul>

		<table class="wp-list-table widefat striped" style="width:100%">
			<thead>
				<tr>
					<th>Status</th>
					<th>Member</th>
					<th>Where They&rsquo;re At</th>
					<th>Activity</th>
					<th>Lead Score</th>
				</tr>
			</thead>
			<tbody>
			<?php
			// Active members, joined to their stored score + status.
			$sql = "
				SELECT u.ID
				FROM {$wpdb->users} u
				JOIN {$wpdb->pmpro_memberships_users} mu ON u.ID = mu.user_id AND mu.status = 'active'
				LEFT JOIN {$wpdb->usermeta} um_score ON u.ID = um_score.user_id AND um_score.meta_key = 'pmprocrm_lead_score'
			";
			if ( 'all' !== $status ) {
				$sql .= " LEFT JOIN {$wpdb->usermeta} um_status ON u.ID = um_status.user_id AND um_status.meta_key = 'pmprocrm_status' ";
				if ( 'new' === $status ) {
					$sql .= " WHERE ( um_status.meta_value = 'new' OR um_status.meta_value = '' OR um_status.meta_value IS NULL ) ";
				} else {
					$sql .= $wpdb->prepare( ' WHERE um_status.meta_value = %s ', $status );
				}
			}
			$sql .= ' GROUP BY u.ID ORDER BY CAST(um_score.meta_value AS SIGNED) DESC LIMIT 200 ';

			// Custom admin report JOIN with no core API equivalent. Interpolated
			// values are $wpdb table names or already run through $wpdb->prepare().
			$user_ids = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

			if ( empty( $user_ids ) ) {
				echo '<tr><td colspan="5">No members in this status yet.</td></tr>';
			}

			foreach ( $user_ids as $user_id ) {
				$user = get_userdata( $user_id );
				// Use the stored score (also the ORDER BY key) so viewing the report
				// doesn't recalculate and write meta for every member on every load.
				$score          = intval( get_user_meta( $user_id, 'pmprocrm_lead_score', true ) );
				$current_status = get_user_meta( $user_id, 'pmprocrm_status', true );
				if ( empty( $current_status ) ) {
					$current_status = 'new';
				}
				$level = pmprocrm_get_main_level_for_user( $user_id );
				$phone = get_user_meta( $user_id, PMPROCRM_PHONE_META, true );
				$stage = get_user_meta( $user_id, PMPROCRM_STAGE_META, true );
				?>
				<tr>
					<td>
						<form method="get">
							<input type="hidden" name="page" value="pmpro-reports" />
							<input type="hidden" name="report" value="pmprocrm" />
							<input type="hidden" name="pmprocrm_action" value="update_status" />
							<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
							<input type="hidden" name="status" value="<?php echo esc_attr( $status ); ?>" />
							<?php wp_nonce_field( 'pmprocrm_status_' . $user_id ); ?>
							<select name="new_status" onchange="this.form.submit();">
								<?php foreach ( $statuses as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_status, $key ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</form>
					</td>
					<?php
					$member_url = add_query_arg(
						array(
							'page'    => 'pmpro-member',
							'user_id' => $user_id,
						),
						admin_url( 'admin.php' )
					);
					?>
					<td>
						<strong><a href="<?php echo esc_url( $member_url ); ?>"><?php echo esc_html( $user->display_name ); ?></a></strong><br />
						<?php echo esc_html( $user->user_email ); ?><br />
						<?php echo ! empty( $level ) ? 'Level: ' . esc_html( $level->name ) : 'No level'; ?>
						<?php
						if ( ! empty( $phone ) ) {
							echo '<br />Phone: ' . esc_html( $phone ); }
						?>
					</td>
					<td>
						<?php
						if ( ! empty( $stage ) && isset( $stages[ $stage ] ) ) {
							echo '<strong>' . esc_html( $stages[ $stage ]['label'] ) . '</strong>';
						} else {
							echo '&mdash;';
						}
						?>
					</td>
					<td>
						<?php
						$views = get_user_meta( $user_id, 'pmprocrm_page_views', true );
						if ( is_array( $views ) ) {
							foreach ( $views as $slug => $data ) {
								echo esc_html( ucwords( str_replace( '-', ' ', $slug ) ) ) . ': x' . intval( $data['count'] ) . '<br />';
							}
						} else {
							echo '&mdash;';
						}
						?>
					</td>
					<td style="font-size:18px;font-weight:700;"><?php echo intval( $score ); ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
}
