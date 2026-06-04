<?php
/**
 * Display a member's membership expiration date or renewal date with custom messaging using a shortcode.
 *
 * title: Display Membership Expiration or Renewal Date via Shortcode
 * layout: snippet-example
 * collection: block-shortcode
 * category: display 
 * link: TBD
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Register the shortcode.
 */
function my_pmpro_expiration_or_renewal_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'level_id'    => 0,
			'date_format' => get_option( 'date_format' ),
		),
		$atts,
		'pmpro_expiration_or_renewal'
	);

	$user_id = get_current_user_id();

	// Not logged in - show nothing.
	if ( ! $user_id ) {
		return '';
	}

	// Get the member's level.
	if ( ! empty( $atts['level_id'] ) ) {
		$level = pmpro_getMembershipLevelForUser( $user_id );

		// Verify the member actually holds this specific level.
		if ( empty( $level ) || (int) $level->id !== (int) $atts['level_id'] ) {
			return '';
		}
	} else {
		$level = pmpro_getMembershipLevelForUser( $user_id );
	}

	// Not a member - show nothing.
	if ( empty( $level ) ) {
		return '';
	}

	$level_name  = esc_html( $level->name );
	$date_format = sanitize_text_field( $atts['date_format'] );

	// Check for an active subscription (recurring membership = renewal date).
	$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $user_id, $level->id );

	if ( ! empty( $subscriptions ) ) {
		$subscription     = $subscriptions[0];
		$next_payment_raw = $subscription->get_next_payment_date( 'U' ); // Get as Unix timestamp for reliable formatting.

		if ( ! empty( $next_payment_raw ) ) {
			$renewal_date = date_i18n( $date_format, (int) $next_payment_raw );

			// Customize the renewal message text below.
			return sprintf(
				/* translators: 1: membership level name, 2: formatted renewal date */
				esc_html__( 'Your %1$s membership will renew on %2$s', 'pmpro-snippets-library' ),
				$level_name,
				esc_html( $renewal_date )
			);
		}
	}

	// No subscription - check for a fixed expiration date.
	if ( ! empty( $level->enddate ) ) {
		$expiration_date = date_i18n( $date_format, (int) $level->enddate );

		// Customize the expiration message text below.
		return sprintf(
			/* translators: 1: membership level name, 2: formatted expiration date */
			esc_html__( 'Your %1$s membership expires on %2$s', 'pmpro-snippets-library' ),
			$level_name,
			esc_html( $expiration_date )
		);
	}

	// No expiration and no renewal date - show nothing.
	return '';
}
add_shortcode( 'pmpro_expiration_or_renewal', 'my_pmpro_expiration_or_renewal_shortcode' );