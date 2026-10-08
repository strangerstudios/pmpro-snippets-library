<?php
/**
 * Prevent the Limit Post Views Add On from granting free access to restricted posts in specific categories.
 *
 * title: Prevent the Limit Post Views Add On From Granting Free Access to Restricted Posts in Specific Categories
 * layout: snippet
 * collection: add-ons
 * category: pmpro-limit-post-views, restricting-content
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Prevent PMPro LPV from granting free access to restricted posts in specific categories.
 *
 * @param  bool    $allow_free_views True if LPV should allow free views of this post.
 * @param  WP_Post $post             The post being checked.
 * @return bool
 */
function my_pmprolpv_always_restrict_posts_from_category( $allow_free_views, $post ) {
	// Add the category IDs, names or slugs whose restricted posts should never be available as free views.
	// Subcategories are not included automatically; list them here too.
	$restricted_categories = array( 5, 'premium', 'members-only' );

	// If the post is in one of the restricted categories, return false to prevent free access.
	if ( has_category( $restricted_categories, $post ) ) {
		return false;
	}

	// Otherwise, return the original value.
	return $allow_free_views;
}
add_filter( 'pmprolpv_has_membership_access', 'my_pmprolpv_always_restrict_posts_from_category', 10, 2 );
