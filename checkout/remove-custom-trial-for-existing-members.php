<?php
/**
 * Remove custom trial for existing members (when existing member changes levels/renews)
 * 
 * title: Remove custom trial for existing memebrs.
 * layout: snippet
 * collection: checkout
 * category: membership-levels, trial
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_level_adjustment( $level ) {

	// Bail if the user currently doesn't have a membership level.
	if ( ! pmpro_hasMembershipLevel() ) {
		return $level;
	}

	// If it's not the level with the trial amount, just bail. Adjust the numeric value for the level ID that should be altered.
	if ( $level->id != '9' ) {
		return $level;
	}

	$level->trial_limit  = '0';
	$level->trial_amount = '0';

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_level_adjustment', 10, 1 );
