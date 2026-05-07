<?php
/**
 * Show an expiring soon banner for memberships expiring within 7 days using the Memberlite theme.
 * Learn more at https://www.paidmembershipspro.com/notification-banner-upcoming-membership-expiration/
 *
 * title: Display a Banner Encouraging Renewals in Your Memberlite Site
 * layout: snippet
 * collection: memberlite
 * category: expiration
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_show_banner_renewal_message() {
	global $pmpro_pages;

	// Bail early if PMPro is not active.
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return;
	}

	// Bail early if the current user does not have a membership level.
	if ( ! pmpro_hasMembershipLevel() ) {
		return;
	}

	// Only load inline CSS if the banner will be shown.
	?>
		<style>
			.pmpro_banner_renewal_wrapper {
				color: white;
				font-family: var(--memberlite-header-font);
				margin: 0;
				padding: 2.9rem;
				text-align: center;
			}
			.pmpro_banner_renewal_wrapper a {
				color: white;
				font-weight: bold;
				text-decoration: underline;
			}
		</style>
	<?php

	$user_id = get_current_user_id();
	$levels  = pmpro_getMembershipLevelsForUser( $user_id );

	// Bail if this is the checkout page.
	if ( is_page( $pmpro_pages['checkout'] ) ) {
		return;
	}

	$expiring = array();

	foreach ( $levels as $level ) {
		// Only consider levels with an end date.
		if ( ! empty( $level->enddate ) ) {
			$expiring[] = $level;
		}
	}

	if ( empty( $expiring ) ) {
		return;
	}

	// Order by the earliest expiring membership.
	usort( $expiring, function( $a, $b ) {
		return strtotime( $a->enddate ) - strtotime( $b->enddate );
	} );

	$expiring_level = $expiring[0];
	$today          = current_time( 'timestamp' );

	// If today is more than 7 days before the end date, bail.
	if ( $today <= strtotime( '-7 days', strtotime( $expiring_level->enddate ) ) ) {
		return;
	}

	// Build the renewal URL and message.
	$renew_url = esc_url( pmpro_url( "checkout", "?pmpro_level=" . $expiring_level->id ) );
	$message   = sprintf(
		/* translators: %1$s is the membership name, %2$s is the renewal URL */
		__( 'Your %1$s membership will expire soon. <a href="%2$s">Click here to renew your membership.</a>', 'pmpro-snippets-library' ),
		esc_html( $expiring_level->name ),
		$renew_url
	);

	echo '<div class="pmpro_banner_renewal_wrapper banner banner_primary">' . $message . '</div>';
}
add_action( 'memberlite_before_page', 'my_memberlite_show_banner_renewal_message' );
