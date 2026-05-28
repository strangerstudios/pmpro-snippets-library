<?php
/**
 * Only allow specific users to edit certain posts and pages.
 *
 * Map post IDs to an array of user IDs allowed to edit/delete/publish it.
 * Any user not in the list will be blocked from those actions on that post.
 * Being listed only exempts a user from this block.
 * It does not grant users any capabilities they don't already have.
 *
 * title: Restrict Editing of Specific Posts to a Specific List of Users
 * layout: snippet
 * collection: misc
 * category: content, admin
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_limit_post_editing_to_specific_users( $allcaps, $caps, $args ) {

	// Add or remove entries here to control which posts are limited and who can edit each one.
	$allowed_editors_by_post_id = array(
		440 => array( 1, 2 ), // Only users with IDs 1 and 2 will be allowed to edit the post/page with ID 440.
		// 441 => array( 1, 3, 4 ),
	);

	// Bail early if the filter wasn't called with a capability, user, and object ID.
	if ( ! isset( $args[0], $args[1], $args[2] ) ) {
		return $allcaps;
	}

	// Pull the values we need out of $args.
	$cap     = $args[0];
	$user_id = (int) $args[1];
	$post_id = (int) $args[2];

	// If this post isn't in our allowlist, leave capabilities untouched.
	if ( ! isset( $allowed_editors_by_post_id[ $post_id ] ) ) {
		return $allcaps;
	}

	// Only intercept editing-related capabilities. Other caps (e.g. read_post) pass through.
	if ( ! in_array( $cap, array( 'edit_post', 'delete_post', 'publish_post' ), true ) ) {
		return $allcaps;
	}

	// If the current user isn't in the allowlist for this post, deny the capability.
	if ( ! in_array( $user_id, $allowed_editors_by_post_id[ $post_id ], true ) ) {
		$allcaps[ $caps[0] ] = false;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'my_pmpro_limit_post_editing_to_specific_users', 10, 3 );
