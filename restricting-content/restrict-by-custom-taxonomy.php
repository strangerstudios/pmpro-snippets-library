<?php
/**
 * Require Membership to View Certain Taxonomy Terms
 * Adds the "Require Membership" level checkboxes to the terms of a custom taxonomy, the same as Paid Memberships Pro offers for post categories and tags.
 * E.g., the example code here makes terms in the custom taxonomy 'dietary_requirement' restrictable. Edit a term like 'Keto' and check the levels that can view its posts.
 * If the taxonomy is used by a custom post type, add that post type with the pmpro_search_filter_post_types filter to hide its restricted posts from searches and archives.
 * Requires Paid Memberships Pro v3.8.1 or higher.
 * Learn more at https://www.paidmembershipspro.com/restrict-by-taxonomy/
 *
 * title: Require Membership to View Certain Taxonomy Terms
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, tags, terms, taxonomies
 * link: https://www.paidmembershipspro.com/restrict-by-taxonomy/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_restrictable_taxonomies( $taxonomies ) {
	// Add the name of each custom taxonomy whose terms can be restricted.
	$taxonomies[] = 'dietary_requirement';

	return $taxonomies;
}
add_filter( 'pmpro_restrictable_taxonomies', 'my_pmpro_restrictable_taxonomies' );
