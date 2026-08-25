<?php
/**
 * Add a [pmpro_renew_button] shortcode that displays a renew button for members
 * with an expiring membership level.
 *
 * title: Insert a Renew Membership button via shortcode
 * layout: snippet
 * collection: blocks-shortcodes
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/insert-renew-membership-button/
 *
 * Shortcode: [pmpro_renew_button]
 *
 * Shows a renew button for logged-in members whose active level has an end date.
 * Supports multiple memberships — one button per qualifying level.
 * Levels with no expiration (lifetime / open-ended recurring) output nothing.
 *
 * Attributes:
 *   expiring_soon_only - "yes" to match the Membership Account "Renew" link
 *                        (only within the expiring-soon window). Default: "no"
 *                        (any active level with an end date).
 *   button_text        - Button label. Use %s for the level name.
 *                        Default: "Renew Membership" (one level) or "Renew %s"
 *                        (multiple levels).
 *
 * Examples:
 *   [pmpro_renew_button]
 *   [pmpro_renew_button expiring_soon_only="yes"]
 *   [pmpro_renew_button button_text="Renew Your %s Plan"]
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Shortcode callback: [pmpro_renew_button]
 *
 * @param array|string $atts Shortcode attributes.
 * @return string HTML for one or more renew buttons, or empty string.
 */
function my_pmpro_renew_button_shortcode( $atts ) {
	// Bail if PMPro is not active.
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) || ! function_exists( 'pmpro_url' ) ) {
		return '';
	}

	// Must be logged in.
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$atts = shortcode_atts(
		array(
			'expiring_soon_only' => 'no',
			'button_text'        => '',
		),
		$atts,
		'pmpro_renew_button'
	);

	$expiring_soon_only = ( 'yes' === strtolower( $atts['expiring_soon_only'] ) );
	$user_id            = get_current_user_id();

	// Active levels only (expired members no longer hold the level).
	$levels = pmpro_getMembershipLevelsForUser( $user_id );

	if ( empty( $levels ) || ! is_array( $levels ) ) {
		return '';
	}

	$use_level_name = ( count( $levels ) > 1 );
	$buttons        = array();

	foreach ( $levels as $level ) {
		// Skip levels with no end date (lifetime / no fixed expiration).
		if ( empty( $level->enddate ) ) {
			continue;
		}

		// Optional: only show inside the same window as the Account page Renew link.
		if ( $expiring_soon_only && ! pmpro_isLevelExpiringSoon( $level ) ) {
			continue;
		}

		// Modern checkout query arg (core uses pmpro_level=).
		$checkout_url = pmpro_url( 'checkout', '?pmpro_level=' . (int) $level->id, 'https' );

		if ( empty( $checkout_url ) ) {
			continue;
		}

		// Button label.
		if ( ! empty( $atts['button_text'] ) ) {
			$label = sprintf( $atts['button_text'], $level->name );
		} elseif ( $use_level_name ) {
			/* translators: %s: membership level name */
			$label = sprintf( __( 'Renew %s', 'pmpro-snippets-library' ), $level->name );
		} else {
			$label = __( 'Renew Membership', 'pmpro-snippets-library' );
		}

		$buttons[] = sprintf(
			'<a class="%1$s" href="%2$s" aria-label="%3$s">%4$s</a>',
			esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-renew pmpro_btn-select pmpro-renew-button', 'pmpro-renew-button' ) ),
			esc_url( $checkout_url ),
			esc_attr(
				sprintf(
					/* translators: %s: membership level name */
					__( 'Renew your %s membership', 'pmpro-snippets-library' ),
					$level->name
				)
			),
			esc_html( $label )
		);
	}

	if ( empty( $buttons ) ) {
		return '';
	}

	return implode( "\n", $buttons );
}
add_shortcode( 'pmpro_renew_button', 'my_pmpro_renew_button_shortcode' );