<?php
/**
 * Remove tags for previously held levels after all level changes are made.
 * Note that this will make a call to the ConvertKit API for each tag to remove and can be a slow process. 
 * Requires ConvertKit Integation for Paid Memberships Pro.
 *
 * title: Remove Tags After All Membership Level Changes
 * layout: snippet
 * collection: add-ons
 * category: convertkit
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_convertkit_after_all_membership_level_changes_remove_tags( $remove_tags, $unsubscribe_tags ) {
	$remove_tags = true;
	return $remove_tags;
}
add_filter( 'pmpro_convertkit_after_all_membership_level_changes_remove_tags', 'my_pmpro_convertkit_after_all_membership_level_changes_remove_tags', 10, 2 );
