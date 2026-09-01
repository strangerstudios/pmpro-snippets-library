<?php
/**
 * Add a [pmpro_renew_button] shortcode that displays for members
 * with one or more expiring membership levels.
 *
 * title: Insert a renew membership button via shortcode
 * layout: snippet
 * collection: block-shortcodes
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/insert-renew-membership-button/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Shortcode callback: [pmpro_renew_button]
 *
 * Attributes:
 *   expiring_soon_only - "yes" to match the Membership Account "Renew" link
 *                        (only within the expiring-soon window). Default: "no".
 *   button_text        - Button label. Use !!name!! for the level name.
 *                        Default: "Renew Membership" (one level) or
 *                        "Renew !!name!!" (multiple levels).
 *
 * @param array|string $atts Shortcode attributes.
 * @return string HTML for one or more renew buttons, or empty string.
 */
function my_pmpro_renew_button_shortcode( $atts ) {

	// Bail if PMPro isn't active.
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) ) {
		return '';
	}

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

	// Active levels only (expired members no longer hold the level).
	$levels = pmpro_getMembershipLevelsForUser( get_current_user_id() );
	if ( empty( $levels ) || ! is_array( $levels ) ) {
		return '';
	}

	$expiring_soon_only = ( 'yes' === strtolower( $atts['expiring_soon_only'] ) );
	$renewable          = array();

	foreach ( $levels as $level ) {

		// Skip levels with no end date (lifetime / no fixed expiration).
		if ( empty( $level->enddate ) ) {
			continue;
		}

		// Optional: only show inside the same window as the Account page Renew link.
		if ( $expiring_soon_only && ! pmpro_isLevelExpiringSoon( $level ) ) {
			continue;
		}

		$renewable[] = $level;
	}

	if ( empty( $renewable ) ) {
		return '';
	}

	// Name the level only when more than one button is actually rendered.
	$use_level_name = ( count( $renewable ) > 1 );
	$buttons        = array();

	foreach ( $renewable as $level ) {

		if ( ! empty( $atts['button_text'] ) ) {
			$text = $atts['button_text'];
		} elseif ( $use_level_name ) {
			/* translators: !!name!! is replaced with the membership level name. */
			$text = __( 'Renew !!name!!', 'pmpro-snippets-library' );
		} else {
			$text = __( 'Renew Membership', 'pmpro-snippets-library' );
		}

		// Core builds and escapes the checkout URL, class attribute, and label,
		// and swaps !!name!! and the other level tokens into the label.
		$buttons[] = pmpro_getCheckoutButton(
			(int) $level->id,
			$text,
			'pmpro_btn pmpro_btn-select pmpro-renew-button'
		);
	}

	// Button styles are scoped to .pmpro in the core frontend CSS.
	return '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro' ) ) . '">' . implode( "\n", $buttons ) . '</div>';
}
add_shortcode( 'pmpro_renew_button', 'my_pmpro_renew_button_shortcode' );
