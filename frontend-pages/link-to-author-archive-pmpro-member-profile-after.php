<?php
/*
 * Show a link to the profile user's author post archive on the Member Profile page.

 * title: Link to profile user's author list archive.
 * layout: snippet
 * collection: frontend-pages
 * category: author-post
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function link_to_author_archive_pmpro_member_profile_after( $pu ) {
	// Get a count of the profile user's published posts.
	$author_posts_count = count_user_posts( $pu->ID, 'post', true );

	// If no posts found, return nothing.
	if ( empty( $author_posts_count ) ) {
		return;
	}

	// Get the profile user's author post archive URL.
	$author_posts_url = get_author_posts_url( $pu->ID );

	// If no link found, return nothing.
	if ( empty( $author_posts_url ) ) {
		return;
	}

	// Show a link to the profile user's posts.
	echo '<p class="pmpro_member_directory_author_posts_url">';
	printf(
		'<a class="author-link" href="%1$s" rel="author">%2$s</a>',
		esc_url( $author_posts_url ),
		sprintf(
			/* translators: %s: Author name. */
			esc_html__( 'View all of %s\'s posts.', 'pmpro-customizations' ),
			$pu->display_name
		)
	);
	echo '</p>';
}
add_action( 'pmpro_member_profile_after', 'link_to_author_archive_pmpro_member_profile_after' );
