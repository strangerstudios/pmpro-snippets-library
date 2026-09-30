<?php
/**
 * Email a daily CSV export of active members for configured membership levels.
 *
 * Hooks into PMPro's built-in Action Scheduler event (pmpro_schedule_daily) to
 * generate one CSV per level and email it to the site admin (or a configured address).
 * Levels with zero active members still get a notice email (no attachment).
 *
 * title: Send a daily members list CSV export email by membership level
 * layout: snippet
 * collection: email
 * category: export, action-scheduler, csv
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Level IDs to export. Replace with real level IDs (add/remove as needed).
define( 'MY_CSV_LEVEL_IDS', '1,2' );

// Email recipient. Defaults to site admin email when left empty.
define( 'MY_CSV_EMAIL_TO', '' );

/**
 * Hook into PMPro's Action Scheduler daily event.
 * PMPro registers and fires this automatically — no separate cron setup needed.
 */
add_action( 'pmpro_schedule_daily', 'my_pmpro_daily_csv_emailer' );

/**
 * Generate and email CSVs for each configured membership level.
 *
 * @return void
 */
function my_pmpro_daily_csv_emailer() {
	$level_ids = array_filter( array_map( 'intval', explode( ',', (string) MY_CSV_LEVEL_IDS ) ) );
	if ( empty( $level_ids ) ) {
		return;
	}

	$email_to = MY_CSV_EMAIL_TO ? MY_CSV_EMAIL_TO : get_option( 'admin_email' );

	foreach ( $level_ids as $level_id ) {
		my_pmpro_generate_and_email_csv( $level_id, $email_to );
	}
}

/**
 * Generate a members CSV for a given level and email it as an attachment.
 * Empty levels still send a short notice with no attachment.
 *
 * @param int    $level_id The membership level ID to export.
 * @param string $email_to The recipient email address.
 * @return void
 */
function my_pmpro_generate_and_email_csv( $level_id, $email_to ) {
	global $wpdb;

	// Validate the level exists.
	$level = pmpro_getLevel( $level_id );
	if ( empty( $level ) ) {
		return;
	}

	$today = date_i18n( 'Y-m-d', current_time( 'timestamp' ) );

	// Get active members for this level.
	$members = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT u.ID, u.user_login, u.user_email, u.user_registered, mu.startdate, mu.enddate
			 FROM {$wpdb->users} u
			 INNER JOIN {$wpdb->pmpro_memberships_users} mu ON u.ID = mu.user_id
			 WHERE mu.membership_id = %d
			   AND mu.status = 'active'
			 ORDER BY u.user_registered ASC",
			$level_id
		)
	);

	$member_count = is_array( $members ) ? count( $members ) : 0;
	$temp_path    = '';

	// Build CSV only when there are active members.
	if ( $member_count > 0 ) {
		// Date format used across PMPro member exports.
		$date_format = apply_filters( 'pmpro_memberslist_csv_dateformat', 'Y-m-d' );

		// Allow filtering of column headers to match or extend PMPro's native export.
		$columns = apply_filters(
			'my_pmpro_daily_csv_header_columns',
			array( 'id', 'username', 'firstname', 'lastname', 'email', 'registered', 'startdate', 'enddate', 'billing_amount', 'cycle_period', 'next_payment_date' )
		);

		$filename  = sanitize_file_name( 'pmpro-members-level-' . $level_id . '-' . $today . '.csv' );
		$temp_path = trailingslashit( get_temp_dir() ) . $filename;

		$handle = fopen( $temp_path, 'w' );
		if ( ! $handle ) {
			return;
		}

		// Escape param required on PHP 8.4+ (avoids deprecation warnings).
		fputcsv( $handle, $columns, ',', '"', '\\' );

		foreach ( $members as $member ) {
			$first_name = get_user_meta( $member->ID, 'first_name', true );
			$last_name  = get_user_meta( $member->ID, 'last_name', true );

			// Pull subscription data via PMPro's built-in class.
			$subscriptions  = PMPro_Subscription::get_subscriptions_for_user( $member->ID, $level_id );
			$billing_amount = '';
			$cycle_period   = '';
			$next_payment   = '';

			if ( ! empty( $subscriptions ) ) {
				$sub            = reset( $subscriptions );
				$billing_amount = $sub->get_billing_amount();
				$cycle_period   = $sub->get_cycle_number() . ' ' . $sub->get_cycle_period();
				$next_payment   = $sub->get_next_payment_date( $date_format );
			}

			$row = array(
				'id'                => $member->ID,
				'username'          => $member->user_login,
				'firstname'         => $first_name,
				'lastname'          => $last_name,
				'email'             => $member->user_email,
				'registered'        => date_i18n( $date_format, strtotime( $member->user_registered ) ),
				// Guard MySQL zero-date sentinel (imports/legacy) — same pattern as core profile/history.
				'startdate'         => ! empty( $member->startdate ) && '0000-00-00 00:00:00' !== $member->startdate
										? date_i18n( $date_format, strtotime( $member->startdate ) )
										: '',
				'enddate'           => ! empty( $member->enddate ) && '0000-00-00 00:00:00' !== $member->enddate
										? date_i18n( $date_format, strtotime( $member->enddate ) )
										: '',
				'billing_amount'    => $billing_amount,
				'cycle_period'      => $cycle_period,
				'next_payment_date' => $next_payment,
			);

			// Filter to match any custom column additions.
			$row = apply_filters( 'my_pmpro_daily_csv_row', $row, $member, $level_id );

			fputcsv( $handle, array_values( $row ), ',', '"', '\\' );
		}

		fclose( $handle );
	}

	// Send via PMPro's email class so from/fromname settings are respected.
	$pmpro_email          = new PMProEmail();
	$pmpro_email->email   = $email_to;
	$pmpro_email->subject = sprintf(
		/* translators: 1: membership level name, 2: date (Y-m-d) */
		__( 'Daily PMPro Members Export - %1$s (%2$s)', 'paid-memberships-pro' ),
		$level->name,
		$today
	);

	if ( $member_count > 0 ) {
		$pmpro_email->data = array(
			'body' => sprintf(
				/* translators: 1: membership level name, 2: active member count */
				__( '<p>Please find attached the daily members export for the <strong>%1$s</strong> membership level (%2$d active members).</p>', 'paid-memberships-pro' ),
				esc_html( $level->name ),
				$member_count
			),
		);
	} else {
		$pmpro_email->data = array(
			'body' => sprintf(
				/* translators: %s: membership level name */
				__( '<p>No active members found today for the <strong>%s</strong> membership level. No CSV is attached.</p>', 'paid-memberships-pro' ),
				esc_html( $level->name )
			),
		);
	}

	// sendEmail() resets $this->attachments, so inject the file via filter when present.
	$attach_filter = null;
	if ( ! empty( $temp_path ) && file_exists( $temp_path ) ) {
		$attach_filter = function ( $attachments ) use ( $temp_path ) {
			$attachments[] = $temp_path;
			return $attachments;
		};
		add_filter( 'pmpro_email_attachments', $attach_filter );
	}

	$pmpro_email->sendEmail();

	if ( null !== $attach_filter ) {
		remove_filter( 'pmpro_email_attachments', $attach_filter );
	}

	// Clean up the temp file.
	if ( ! empty( $temp_path ) && file_exists( $temp_path ) ) {
		wp_delete_file( $temp_path );
	}
}
