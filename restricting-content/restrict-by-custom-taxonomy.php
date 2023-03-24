<?php
/**
 * Require Membership to View Certain Taxonomy Terms
 * E.g., the example code here locks down posts in the custom taxonomy 'dietary_requirements' with the 'Keto' term.
 * Note we hook in on priority 15 to run after other filters.
 * Learn more at https://www.paidmembershipspro.com/restrict-by-taxonomy/
 *
 * title: Require Membership to View Certain Taxonomy Terms
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, tags, terms, taxonomies
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_require_membership_for_terms( $hasaccess, $post, $user, $post_levels ) {
	// Make sure we have a post to check.
	if ( empty( $post ) || empty( $post->ID ) ) {
		return $hasaccess;
	}
	
	// First array's keys are taxonomy names.
	// Second array's keys are term slugs.
	// Second array's values are arrays of level IDs.
	$term_levels = array(
		'dietary_requirement' => array(
			'Keto' => array( 1, 2 ),
		),
	);

	foreach ( $term_levels as $taxonomy => $terms ) {
		foreach( $terms as $term => $level_ids ) {
			if ( has_term( $term, $taxonomy, $post ) ) {
				// Post has term, lock it down.
				$hasaccess = false;
				if ( pmpro_hasMembershipLevel( $level_ids, $user->ID ) ) {
					// User has level, unlock it again.
					$hasaccess = true;	// Give access.
					break 2;			// Break both for loops.
				}
			}
		}
	}
	
	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_require_membership_for_terms', 15, 4 );
