<?php
/**
 * Handle Forminator submissions for a PMPro cancellation/retention survey.
 *
 * title: PMPro Cancellation Survey - Forminator Submission Handler
 * layout: snippet
 * collection: misc
 * category: cancellation
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Set this to the Form ID Forminator assigned to your Cancellation Survey.
 */
function pmpro_cancel_survey_form_id() {
	return 29; // Change me.
}

/**
 * Handle a submission of the PMPro cancellation survey.
 */
function pmpro_cancel_survey_handle_submission( $form_id, $response ) {
	if ( (int) $form_id !== pmpro_cancel_survey_form_id() ) {
		return;
	}
	if ( empty( $response ) || ! is_array( $response ) || empty( $response['success'] ) ) {
		return;
	}

	$entry_id = isset( $response['entry_id'] ) ? (int) $response['entry_id'] : 0;
	if ( ! $entry_id || ! class_exists( 'Forminator_Form_Entry_Model' ) ) {
		return;
	}

	$entry = new Forminator_Form_Entry_Model( $entry_id );
	if ( empty( $entry->entry_id ) ) {
		return;
	}

	$survey = pmpro_cancel_survey_extract_values( $entry->meta_data );

	$user_id = pmpro_cancel_survey_resolve_user_id( $survey );
	if ( ! $user_id ) {
		return;
	}

	$survey['user_id']   = $user_id;
	$survey['entry_id']  = $entry_id;
	$survey['submitted'] = current_time( 'mysql' );

	pmpro_cancel_survey_save_user_meta( $user_id, $survey );
	pmpro_cancel_survey_append_member_note( $user_id, $survey );

	if ( 'cancel_confirmed' === $survey['route'] ) {
		pmpro_cancel_survey_cancel_membership( $user_id, $survey );
	} elseif ( 'contact_team' === $survey['route'] ) {
		/**
		 * Hook this to push the request into Slack, Help Scout, etc.
		 */
		do_action( 'pmpro_cancellation_retention_request', $user_id, $survey );
	}
}
add_action( 'forminator_form_after_save_entry', 'pmpro_cancel_survey_handle_submission', 10, 2 );

function pmpro_cancel_survey_extract_values( $meta_data ) {
	$values = array(
		'reason'         => '',
		'feedback'       => '',
		'route'          => '',
		'hidden_user_id' => 0,
	);
	if ( empty( $meta_data ) || ! is_array( $meta_data ) ) {
		return $values;
	}

	$map = array(
		'radio-1'    => 'reason',
		'textarea-1' => 'feedback',
		'radio-2'    => 'route',
		'hidden-1'   => 'hidden_user_id',
	);
	foreach ( $map as $element_id => $key ) {
		if ( isset( $meta_data[ $element_id ]['value'] ) ) {
			$raw = $meta_data[ $element_id ]['value'];
			$values[ $key ] = is_array( $raw ) ? implode( ', ', $raw ) : (string) $raw;
		}
	}
	$values['hidden_user_id'] = (int) $values['hidden_user_id'];
	return $values;
}

function pmpro_cancel_survey_resolve_user_id( $survey ) {
	$user_id = get_current_user_id();
	if ( ! $user_id && ! empty( $survey['hidden_user_id'] ) ) {
		$user_id = (int) $survey['hidden_user_id'];
	}
	return $user_id && get_userdata( $user_id ) ? (int) $user_id : 0;
}

function pmpro_cancel_survey_save_user_meta( $user_id, $survey ) {
	$history   = get_user_meta( $user_id, 'pmpro_cancel_survey_history', true );
	$history   = is_array( $history ) ? $history : array();
	$history[] = $survey;
	update_user_meta( $user_id, 'pmpro_cancel_survey_history', $history );
	update_user_meta( $user_id, 'pmpro_cancellation_reason', $survey['reason'] );
}

function pmpro_cancel_survey_append_member_note( $user_id, $survey ) {
	$prefix = 'cancel_confirmed' === $survey['route']
		? __( '[Cancellation Survey]', 'pmpro-snippets-library' )
		: __( '[Retention Request]', 'pmpro-snippets-library' );

	$lines = array(
		sprintf( '%s %s', $prefix, $survey['submitted'] ),
		sprintf(
			/* translators: %s: the reason the member selected. */
			__( 'Reason: %s', 'pmpro-snippets-library' ),
			pmpro_cancel_survey_label( $survey['reason'] )
		),
	);
	if ( ! empty( $survey['feedback'] ) ) {
		$lines[] = sprintf(
			/* translators: %s: the free-text feedback the member entered. */
			__( 'Feedback: %s', 'pmpro-snippets-library' ),
			$survey['feedback']
		);
	}
	$lines[] = sprintf(
		/* translators: %d: the Forminator entry ID. */
		__( 'Forminator entry #%d', 'pmpro-snippets-library' ),
		$survey['entry_id']
	);

	$new_note = implode( "\n", $lines );
	$existing = get_user_meta( $user_id, 'user_notes', true );
	$combined = $existing ? $existing . "\n\n" . $new_note : $new_note;
	update_user_meta( $user_id, 'user_notes', $combined );
}

function pmpro_cancel_survey_label( $slug ) {
	$labels = array(
		'too_expensive'     => __( 'Too expensive', 'pmpro-snippets-library' ),
		'not_using'         => __( 'Not using it enough', 'pmpro-snippets-library' ),
		'missing_feature'   => __( 'Missing a feature I need', 'pmpro-snippets-library' ),
		'found_alternative' => __( 'Found an alternative', 'pmpro-snippets-library' ),
		'exploring'         => __( 'Just exploring', 'pmpro-snippets-library' ),
		'other'             => __( 'Other', 'pmpro-snippets-library' ),
		'cancel_confirmed'  => __( 'Cancel my membership', 'pmpro-snippets-library' ),
		'contact_team'      => __( 'Talk to the team first', 'pmpro-snippets-library' ),
	);
	return isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;
}

function pmpro_cancel_survey_cancel_membership( $user_id, $survey ) {
	if ( ! function_exists( 'pmpro_cancelMembershipLevel' ) ) {
		return;
	}
	$levels = pmpro_getMembershipLevelsForUser( $user_id );
	if ( empty( $levels ) ) {
		return;
	}
	foreach ( $levels as $level ) {
		pmpro_cancelMembershipLevel( $level->id, $user_id, 'cancelled' );
	}
}
