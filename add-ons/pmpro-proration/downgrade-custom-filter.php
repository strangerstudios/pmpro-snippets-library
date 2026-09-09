<?php
/**
 * Override the default downgrade check in the Proration Add On
 *
 * title: Override the default downgrade check in the Proration Add On
 * layout: snippet
 * collection: add-ons, pmpro-proration
 * category: proration, membership-levels
 * link: https://www.paidmembershipspro.com/add-ons/proration-prorate-membership/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * Learn more at https://www.paidmembershipspro.com/add-ons/proration-prorate-membership/
 */
 
/**
 * Rank membership levels to decide which level changes count as a downgrade.
 *
 * By default, the Proration Add On treats a level change as a downgrade when the new
 * level costs less per day than the old level. Edit the $level_order array below to
 * list your level IDs in order from lowest tier to highest tier instead. If either
 * level in the change is missing from the array, the Add On's default check is used.
 *
 * You can also run your own comparison on the $old_level and $new_level objects,
 * such as checking initial_payment, billing_amount, or a custom level meta value.
 *
 * @param bool         $is_downgrade Whether the level change is currently treated as a downgrade.
 * @param object|int   $old_level    The member's old membership level.
 * @param object|int   $new_level    The membership level being checked out for.
 * @return bool
 */
function pmpro_is_downgrade_custom_filter( $is_downgrade, $old_level, $new_level ) {
	// This array contains all of the level IDs in order of downgrade -> upgrade. Change this as needed.
	$level_order = array( 4, 5, 6, 1, 2, 3 ); // your membership level IDs in order from lowest tier to highest tier

	// Get the level IDs. Older versions of the Add On pass level IDs instead of level objects.
	$old_level_id = is_object( $old_level ) ? (int) $old_level->id : (int) $old_level;
	$new_level_id = is_object( $new_level ) ? (int) $new_level->id : (int) $new_level;

	// Figure out where the levels rank.
	$old_level_rank = array_search( $old_level_id, $level_order, true );
	$new_level_rank = array_search( $new_level_id, $level_order, true );

	// Make sure we have a rank for both levels, otherwise let the Add On calculate the change.
	if ( false === $old_level_rank || false === $new_level_rank ) {
		return $is_downgrade;
	}

	// A lower rank in the array above means the new level is a downgrade.
	return $old_level_rank > $new_level_rank;
}
add_filter( 'pmpro_is_downgrade', 'pmpro_is_downgrade_custom_filter', 10, 3 );