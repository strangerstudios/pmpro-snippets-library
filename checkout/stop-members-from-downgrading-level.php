<?php
/**
 * Define a level hierarchy and stop members from downgrading their membership level
 *
 * This recipe is not compatible with sites that allow multiple memberships per user
 *
 * title: Stop members downgrading their membership level
 * layout: snippet
 * collection: checkout
 * category: registration-check
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_tiered_levels_prevent_downgrade( $okay ) {
	// Bail early if something else isn't okay.
	if ( ! $okay ) {
		return $okay;
	}

	// get user's level
	$member_level = pmpro_getMembershipLevelForUser();

	// If the user doesn't have a membership level carry on with checkout.
	if ( false === $member_level ) {
		return $okay;
	}

	// member's current level ID
	$member_level_id = $member_level->id;

	// Get level for checkout.
	$checkout_level = pmpro_getLevelAtCheckout();
	$checkout_level_id = $checkout_level->id;

	// bail if renewal 
	if( $checkout_level_id === $member_level_id ) {
		return $okay;
	}

	// Specify the level hierarchy, lowest to highest
	$level_order = array( 1, 2, 3, 4, 5 );

	// figure out where the levels rank, bail if not in level order
	$checkout_level_rank = array_search( $checkout_level_id, $level_order );
	$member_level_rank = array_search( $member_level_id, $level_order );
	if ( false === $checkout_level_rank || false === $member_level_rank ) {
		return $okay;
	}

	// are they checking out for a lower level?
	if( $member_level_rank > $checkout_level_rank ) {
		$okay = false;
		// show an error message on checkout page
		pmpro_setMessage( 'You are not allowed to checkout for this membership level.', 'pmpro_error' );
	}

	return $okay;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_tiered_levels_prevent_downgrade' );
