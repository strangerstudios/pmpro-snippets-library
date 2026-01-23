<?php
/**
 * This recipe will generate an affiliate code for all existing users when using PMPro Affiliates.
 * To run this script, you'll need to add /wp-admin/?mypmpro_create_affiliates=true to your URL
 *
 * The default roles that we're looking for are subscribers. You can change this on line 18
 * The number of days a cookie will remain active before expiring is 30 days (Line 49)
 *
 * title: Generate affiliate code for existing users
 * layout: snippet
 * collection: add-ons
 * category: pmpro-affiliates
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpro_run_affiliate_creation() {

	if ( isset( $_REQUEST['mypmpro_create_affiliates'] ) && function_exists( 'mypmpro_create_affiliate' ) ) {

		$level_id = isset( $_REQUEST['pmpro_level_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['pmpro_level_id'] ) ) : '';

		// Bail if no level ID was provided.
		if ( empty( $level_id ) ) {
			echo 'To run this script, include a membership level ID in your URL.<br/> e.g. /wp-admin/?mypmpro_create_affiliates=true<strong>&pmpro_level_id=3</strong>';
			exit();
		}

		// Bail if the level does not exist.
		$pmpro_level = pmpro_getLevel( $level_id );
		if ( empty( $pmpro_level ) ) {
			exit( 'No level with ID ' . $level_id );
		}

		global $wpdb;

		// Get all User IDs for members of the level.
		$user_ids = $wpdb->get_col(
			"SELECT user_id FROM {$wpdb->pmpro_memberships_users}
			WHERE membership_id = '{$pmpro_level->id}'
			AND status = 'active'"
		);

		// Bail if no members found.
		if ( empty( $user_ids ) ) {
			exit( 'No active members found for level ID ' . $level_id );
		}

		// Get the members' user objects.
		$users = get_users(
			array(
				'include' => $user_ids,
			)
		);

		if ( $users ) {
			foreach ( $users as $user ) {
				mypmpro_create_affiliate( $user->display_name, $user->user_login );
			}
			exit( 'End' );
		}
	}
}
add_action( 'admin_init', 'mypmpro_run_affiliate_creation' );

function mypmpro_create_affiliate( $name = '', $username = '' ) {

	if ( ! function_exists( 'pmpro_affiliates_getNewCode' ) ) {
		return;
	}

	global $wpdb;

	// Skip adding if the user is already an affiliate.
	$affiliates = $wpdb->get_results(
		"SELECT * FROM {$wpdb->pmpro_affiliates}
		WHERE affiliateuser = '{$username}'
		AND enabled = '1'",
	);

	if ( ! empty( $affiliates ) ) {
		echo "Skipped $username (already an affiliate)<br/>";
		return;
	}

	$code = pmpro_affiliates_getNewCode();

	$trackingcode   = '';
	$commissionrate = '.1'; // Change the commission rate .1 is 10%.
	$cookiedays     = '30'; // Change number of days the cookie should be valid for.
	$enabled        = true;

	// Insert affiliate.
	$wpdb->insert(
		$wpdb->pmpro_affiliates,
		array(
			'code'           => $code,
			'name'           => $name,
			'affiliateuser'  => $username,
			'trackingcode'   => $trackingcode,
			'cookiedays'     => $cookiedays,
			'commissionrate' => $commissionrate,
			'enabled'        => $enabled,
		),
		array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%f',
			'%d',
		)
	);

	if ( $wpdb->insert_id ) {
		echo 'Affiliate Created Successfully - ' . $username . '<br/>';
	} else {
		echo 'Affiliate Could Not Be Created  - ' . $username . '<br/>';
	}
}
