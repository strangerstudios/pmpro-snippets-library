<?php
/**
 * Add German Widerruf (§ 356a) and Kündigung (§ 312k) cancellation buttons.
 *
 * title: Add German Widerruf and Kündigung Cancellation Buttons
 * layout: snippet
 * collection: membership-levels
 * category: cancellation
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 * This reuses PMPro's native cancel flow (the per-membership account link, the
 * cancel confirmation page, and the cancellation email) and adapts it to the
 * German Widerrufs-/Kündigungsbutton rules. Steps 4 and 6 change cancellation
 * timing and email delivery, so test them on staging and confirm the wording
 * with your legal-text provider. Labels are German; swap them for your locale.
 */

/**
 * 1. Account page: add a "Vertrag widerrufen" link per membership and relabel
 *    the existing cancel link to "Verträge hier kündigen".
 */
function my_pmpro_widerruf_action_links( $links, $level_id ) {
	$widerruf_url = pmpro_url( 'cancel', 'levelstocancel=' . intval( $level_id ) . '&widerruf=1' );
	$links['widerruf'] = '<a id="pmpro_actionlink-widerruf" href="' . esc_url( $widerruf_url ) . '">' . esc_html( 'Vertrag widerrufen' ) . '</a>';

	if ( isset( $links['cancel'] ) ) {
		$cancel_url      = pmpro_url( 'cancel', 'levelstocancel=' . intval( $level_id ) );
		$links['cancel'] = '<a id="pmpro_actionlink-cancel" href="' . esc_url( $cancel_url ) . '">' . esc_html( 'Verträge hier kündigen' ) . '</a>';
	}

	return $links;
}
add_filter( 'pmpro_member_action_links', 'my_pmpro_widerruf_action_links', 10, 2 );

/**
 * 2. Carry the withdrawal flag through the confirmation form. This hook fires
 *    inside the cancel <form>, so the hidden field is submitted with it.
 */
function my_pmpro_widerruf_hidden_field( $user, $old_level_ids ) {
	if ( ! empty( $_REQUEST['widerruf'] ) ) {
		echo '<input type="hidden" name="widerruf" value="1" />';
	}
}
add_action( 'pmpro_cancel_before_submit', 'my_pmpro_widerruf_hidden_field', 10, 2 );

/**
 * 3. Relabel the confirmation button. PMPro builds it with _n(), so filter
 *    ngettext. Withdrawal -> "Widerruf bestätigen"; cancellation -> "jetzt kündigen".
 */
function my_pmpro_confirm_button_label( $translation, $single, $plural, $number, $domain ) {
	if ( 'paid-memberships-pro' !== $domain || empty( $_REQUEST['levelstocancel'] ) ) {
		return $translation;
	}

	// Singular forms of the confirm button for specific levels and for all levels.
	// These must match the _n() singular strings in paid-memberships-pro/pages/cancel.php
	// (the "Yes, cancel this membership" and "Yes, cancel my membership" calls). If PMPro
	// renames them, this filter silently stops matching — re-verify against core on upgrade.
	$confirm_singulars = array(
		'Yes, cancel this membership',
		'Yes, cancel my membership',
	);

	if ( in_array( $single, $confirm_singulars, true ) ) {
		return ! empty( $_REQUEST['widerruf'] ) ? 'Widerruf bestätigen' : 'jetzt kündigen';
	}

	return $translation;
}
add_filter( 'ngettext', 'my_pmpro_confirm_button_label', 10, 5 );

/**
 * 4. For a withdrawal, end access immediately instead of running to the next
 *    payment date. A normal cancellation keeps PMPro's default behavior.
 */
function my_pmpro_widerruf_cancel_immediately( $cancel_on_next_payment_date, $level_id, $user_id ) {
	if ( ! empty( $_REQUEST['widerruf'] ) ) {
		return false;
	}
	return $cancel_on_next_payment_date;
}
add_filter( 'pmpro_cancel_on_next_payment_date', 'my_pmpro_widerruf_cancel_immediately', 10, 3 );

/**
 * 5. Add a !!cancellation_datetime!! variable to the cancellation emails. Insert
 *    it into the email body under Memberships > Settings > Email Templates to
 *    record the date and time the cancellation was received.
 */
function my_pmpro_cancel_email_timestamp( $data, $email ) {
	$cancel_templates = array(
		'cancel',
		'cancel_admin',
		'cancel_on_next_payment_date',
		'cancel_on_next_payment_date_admin',
	);

	if ( ! empty( $email->template ) && in_array( $email->template, $cancel_templates, true ) ) {
		$data['cancellation_datetime'] = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
	}

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_cancel_email_timestamp', 10, 2 );

/**
 * 6a. For a withdrawal, let the member confirm (or change) where the acknowledgment
 *     is sent. § 356a (2) / Article 11a(2)(c) allows a different confirmation
 *     address. Pre-filled with the account email so the receipt defaults to a good
 *     address.
 */
function my_pmpro_widerruf_confirmation_email_field( $user, $old_level_ids ) {
	if ( empty( $_REQUEST['widerruf'] ) ) {
		return;
	}

	$default = is_a( $user, 'WP_User' ) ? $user->user_email : '';
	echo '<p class="pmpro_widerruf_confirmation_email">';
	echo '<label for="widerruf_confirmation_email">' . esc_html( 'Bestätigung senden an (E-Mail)' ) . '</label>';
	echo '<input type="email" name="widerruf_confirmation_email" id="widerruf_confirmation_email" size="40" value="' . esc_attr( $default ) . '" />';
	echo '</p>';
}
add_action( 'pmpro_cancel_before_submit', 'my_pmpro_widerruf_confirmation_email_field', 20, 2 );

/**
 * 6b. For a withdrawal, send the member-facing acknowledgment to the address they
 *     provided. Scoped to the member cancel emails only (never the admin copies),
 *     and only when a valid email was submitted.
 */
function my_pmpro_widerruf_confirmation_email_recipient( $recipient, $email ) {
	$member_templates = array( 'cancel', 'cancel_on_next_payment_date' );

	$confirmation_email = isset( $_REQUEST['widerruf_confirmation_email'] ) ? sanitize_email( wp_unslash( $_REQUEST['widerruf_confirmation_email'] ) ) : '';

	if ( ! empty( $_REQUEST['widerruf'] )
		&& ! empty( $email->template )
		&& in_array( $email->template, $member_templates, true )
		&& is_email( $confirmation_email ) ) {
		$recipient = $confirmation_email;
	}

	return $recipient;
}
add_filter( 'pmpro_email_recipient', 'my_pmpro_widerruf_confirmation_email_recipient', 10, 2 );
