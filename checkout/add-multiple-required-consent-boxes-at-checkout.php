<?php
/**
 * Add additional required consent boxes at checkout and log all agreements to the consent log.
 * 
 * title: Add Multiple Required Consent Boxes at Checkout
 * layout: snippet
 * collection: checkout
 * category: tos
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Set the IDs of the additional TOS pages.
 */
function pmpro_get_required_tos_pages() {
	return array( 382087, 382089 );
}

/**
 * Add multiple TOS checkboxes to checkout.
 */
function pmpro_show_multiple_tos_at_checkout() {
	global $pmpro_review;

	if ( $pmpro_review ) {
		return;
	}

	$tos_pages = pmpro_get_required_tos_pages();

	if ( empty( $tos_pages ) ) {
		return;
	}

	echo '<fieldset id="pmpro_tos_fields" class="' . esc_attr( pmpro_get_element_class( 'pmpro_form_fieldset', 'pmpro_tos_fields' ) ) . '">';
	echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_form_fields' ) ) . '">';

	foreach ( $tos_pages as $page_id ) {
		$page = get_post( $page_id );
		if ( empty( $page ) ) {
			continue;
		}

		$tos_checked = isset( $_REQUEST['tos_' . $page_id] ) ? intval( $_REQUEST['tos_' . $page_id] ) : 0;

		echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-checkbox pmpro_form_field-required' ) ) . '">';
		echo '<label class="' . esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_clickable', 'tos_' . $page_id ) ) . '">';
		echo '<input type="checkbox" name="tos_' . esc_attr( $page_id ) . '" value="1" id="tos_' . esc_attr( $page_id ) . '" ' . checked( 1, $tos_checked, false ) . '>';
		echo sprintf( __( 'I agree to the <a href="%1$s" target="_blank">%2$s</a>', 'paid-memberships-pro' ), esc_url( get_permalink( $page->ID ) ), esc_html( $page->post_title ) );
		echo '</label></div>';
	}

	echo '</div></fieldset>';
}
add_action( 'pmpro_checkout_before_submit_button', 'pmpro_show_multiple_tos_at_checkout', 5 );

/**
 * Validate multiple TOS checkboxes at checkout.
 */
function pmpro_validate_multiple_tos_at_checkout( $pmpro_continue_registration ) {
	global $pmpro_error_fields;

	if ( ! $pmpro_continue_registration ) {
		return false;
	}

	$tos_pages = pmpro_get_required_tos_pages();

	foreach ( $tos_pages as $page_id ) {
		if ( empty( $_REQUEST['tos_' . $page_id] ) ) {
			$pmpro_continue_registration = false;
			$pmpro_error_fields[]        = 'tos_' . $page_id;
			$page                        = get_post( $page_id );
			pmpro_setMessage( sprintf( __( "Please check the box to agree to the %s.", 'paid-memberships-pro' ), esc_html( $page->post_title ) ), "pmpro_error" );
		}
	}

	return $pmpro_continue_registration;
}
add_filter( 'pmpro_checkout_user_creation_checks', 'pmpro_validate_multiple_tos_at_checkout' );
add_filter( 'pmpro_checkout_order_creation_checks', 'pmpro_validate_multiple_tos_at_checkout' );

/**
 * Log multiple TOS consents after checkout.
 */
function pmpro_after_checkout_update_multiple_consent( $user_id, $order ) {
	$tos_pages = pmpro_get_required_tos_pages();

	if ( empty( $tos_pages ) ) {
		return;
	}

	// Get existing consent log
	$existing_log = get_user_meta( $user_id, 'pmpro_consent_log', true );
	if ( empty( $existing_log ) ) {
		$existing_log = array();
	}

	foreach ( $tos_pages as $page_id ) {
		if ( ! empty( $_REQUEST['tos_' . $page_id] ) ) {
			$post = get_post( $page_id );
			if ( empty( $post ) ) {
				continue;
			}

			// Create a new consent log entry
			$new_entry = array(
				'user_id'       => $user_id,
				'post_id'       => $page_id,
				'post_modified' => $post->post_modified,
				'order_id'      => $order->id,
				'consented'     => true,
				'timestamp'     => current_time( 'timestamp' ),
			);

			// Append to the log instead of overwriting
			$existing_log[] = $new_entry;
		}
	}

	// Save updated log
	update_user_meta( $user_id, 'pmpro_consent_log', $existing_log );
}
add_action( 'pmpro_after_checkout', 'pmpro_after_checkout_update_multiple_consent', 10, 2 );
