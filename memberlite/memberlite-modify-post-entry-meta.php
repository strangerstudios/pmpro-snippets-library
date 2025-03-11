<?php
/**
 * Modify the Memberlite post entry meta by adding post_tags only if the post has tags.
 *
 * title: Conditionally Add Tags to Memberlite Post Entry Meta
 * layout: snippet
 * collection: memberlite
 * category: design
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_memberlite_get_entry_meta( $meta, $post, $location ) {
	// Return if this isn't the 'after' meta location.
	if ( $location != 'after' ) {
		return $meta;
	}

	// If this post has tags, include at the start of the meta.
	if ( has_tag( '', $post ) ) {
		$meta = 'Tagged: {post_tags}. ' . $meta;
	}

	// The return variable.
	return $meta;
}
add_filter( 'memberlite_get_entry_meta', 'my_pmpro_memberlite_get_entry_meta', 10, 3 );
