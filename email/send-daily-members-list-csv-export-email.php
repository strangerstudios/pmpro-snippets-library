<?php
/**
 * Daily CSV Export Emailer for PMPro Members by Level
 *
 * Hooks into PMPro's built-in Action Scheduler (pmpro_schedule_daily) to
 * generate a CSV export per membership level and email it to the site admin.
 *
 * Configure the level IDs and recipient email below.
 *
 * @link https://www.paidmembershipspro.com/
 */

// Level IDs to export. Replace 1 and 2 with real level IDs.
define( 'MY_CSV_LEVEL_ID_1', 1 );
define( 'MY_CSV_LEVEL_ID_2', 2 );

// Email recipient. Defaults to site admin email.
define( 'MY_CSV_EMAIL_TO', get_option( 'admin_email' ) );

/**
 * Hook into PMPro's Action Scheduler daily event.
 * PMPro registers and fires this automatically - no cron setup needed.
 */
add_action( 'pmpro_schedule_daily', 'my_pmpro_daily_csv_emailer' );

/**
 * Generate and email CSVs for each configured membership level.
 *
 * @return void
 */
function my_pmpro_daily_csv_emailer() {
	$level_ids = array( MY_CSV_LEVEL_ID_1, MY_CSV_LEVEL_ID_2 );

	foreach ( $level_ids as $level_id ) {
		my_pmpro_generate_and_email_csv( $level_id, MY_CSV_EMAIL_TO );
	}
}

/**
 * Generate a members CSV for a given level and email it as an attachment.
 *
 * @param int    $level_id  The membership level ID to export.
 * @param string $email_to  The recipient email address.
 * @return void
 */
function my_pmpro_generate_and_email_csv( $level_id, $email_to ) {
	global $wpdb;

	// Validate the level exists.
	$level = pmpro_getLevel( $level_id );
	if ( empty( $level ) ) {
		return;
	}

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

	if ( empty( $members ) ) {
		return;
	}

	// Build the date format used across PMPro member exports.
	$date_format = apply_filters( 'pmpro_memberslist_csv_dateformat', 'Y-m-d' );

	// Allow filtering of column headers to match or extend PMPro's native export.
	$columns = apply_filters(
		'my_pmpro_daily_csv_header_columns',
		array( 'id', 'username', 'firstname', 'lastname', 'email', 'registered', 'startdate', 'enddate', 'billing_amount', 'cycle_period', 'next_payment_date' )
	);

	// Write to a temp file.
	$filename  = sanitize_file_name( 'pmpro-members-level-' . $level_id . '-' . date( 'Y-m-d' ) . '.csv' );
	$temp_path = trailingslashit( get_temp_dir() ) . $filename;

	$handle = fopen( $temp_path, 'w' );
	if ( ! $handle ) {
		return;
	}

	// Write header row.
	fputcsv( $handle, $columns );

	// Write one row per member.
	foreach ( $members as $member ) {
		$user       = get_userdata( $member->ID );
		$first_name = get_user_meta( $member->ID, 'first_name', true );
		$last_name  = get_user_meta( $member->ID, 'last_name', true );

		// Pull subscription data via PMPro's built-in class.
		$subscriptions   = PMPro_Subscription::get_subscriptions_for_user( $member->ID, $level_id );
		$billing_amount  = '';
		$cycle_period    = '';
		$next_payment    = '';

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
			'registered'        => date( $date_format, strtotime( $member->user_registered ) ),
			'startdate'         => date( $date_format, strtotime( $member->startdate ) ),
			'enddate'           => ! empty( $member->enddate ) && '0000-00-00 00:00:00' !== $member->enddate
								   ? date( $date_format, strtotime( $member->enddate ) )
								   : '',
			'billing_amount'    => $billing_amount,
			'cycle_period'      => $cycle_period,
			'next_payment_date' => $next_payment,
		);

		// Filter to match any custom column additions.
		$row = apply_filters( 'my_pmpro_daily_csv_row', $row, $member, $level_id );

		fputcsv( $handle, array_values( $row ) );
	}

	fclose( $handle );

	// Send via PMPro's email class so from/fromname settings are respected.
	$pmpro_email          = new PMProEmail();
	$pmpro_email->email   = $email_to;
	$pmpro_email->subject = sprintf( 'Daily PMPro Members Export - %s (%s)', $level->name, date( 'Y-m-d' ) );
	$pmpro_email->data    = array(
		'body' => sprintf(
			'<p>Please find attached the daily members export for the <strong>%s</strong> membership level (%d active members).</p>',
			esc_html( $level->name ),
			count( $members )
		),
	);
	$pmpro_email->attachments = array( $temp_path );
	$pmpro_email->sendEmail();

	// Clean up the temp file.
	wp_delete_file( $temp_path );
}
