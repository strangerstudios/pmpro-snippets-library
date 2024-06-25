<?php
/**
 * Show an expiring soon banner - Memberlite
 * Learn more at https://www.paidmembershipspro.com/notification-banner-upcoming-membership-expiration/
 *
 * title: Display a renewal reminder notification banner at the top of your website for members whose membership
 * layout: snippet
 * collection: misc
 * category: expiration
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function memberlite_show_banner_renewal_message(){
	global $pmpro_pages;

	// Bail early if the current user does not have a membership level.
	if ( ! pmpro_hasMembershipLevel() ) {
		return;
	}

	// Load custom CSS for banner.
	?>
		<style>
			.pmpro_banner_renewal_wrapper h4 {
				color: white;
				margin: 0;
				padding: 1rem;
				text-align: center;
			}
			.pmpro_banner_renewal_wrapper a {
				color: white;
				text-decoration: underline;
			}
			.pmpro_banner_renewal_wrapper a:hover {
				color: rgba(255,255,255,0.8);
			}
		</style>
	<?php

	$user_id = get_current_user_id();
	$levels = pmpro_getMembershipLevelsForUser( $user_id );

	// Bail if this is the checkout page.
	if ( is_page( $pmpro_pages['checkout'] ) ) {
		return;
	}

	$expiring = array();

	foreach( $levels as $level ) {

		// Bail if the user does not have an enddate set.
		if ( ! empty( $level->enddate ) ) {
			$expiring[$level->enddate] = $level;
		}
		
	}

	if ( empty( $expiring ) ) {
		return;
	}

	//Order by level that is expiring soonest
	ksort( $expiring );

	$message = "";
	$today = time();

	$expiring_level = reset( $expiring );

	// if today is more than 7 days before enddate, bail.
	if ( $today <= strtotime( '- 7 days', $expiring_level->enddate ) ) {
		return;
	}

	$message = 'Your ' . $expiring_level->name . ' membership will expire soon. <a href="' . pmpro_url( "checkout", "?pmpro_level=" . $expiring_level->id ) . '"> Click here to renew membership.</a>';

	echo '<div class="pmpro_banner_renewal_wrapper banner banner_secondary"><h4> ' . $message . ' </h4></div>';

}
add_action( 'before_page', 'memberlite_show_banner_renewal_message' );
