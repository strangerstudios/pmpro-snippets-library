<?php
/**
 * Add a lock icon before the title of posts that require membership,
 * shown only to users who do not have access to the post.
 *
 * title: Add a Lock Icon to Protected Post Titles
 * layout: snippet
 * collection: frontend-pages
 * category: content-protection
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_lock_icon_in_title( $title, $id = null ) {

	// Skip in the admin, in feeds, and when no post ID is available.
	if ( is_admin() || is_feed() || ! $id ) {
		return $title;
	}

	// Only add the icon to titles rendered inside The Loop.
	if ( ! in_the_loop() ) {
		return $title;
	}

	// Bail if PMPro is not active.
	if ( ! defined( 'PMPRO_URL' ) || ! function_exists( 'pmpro_has_membership_access' ) ) {
		return $title;
	}

	// Show the lock only when the post requires membership and the current user lacks access.
	$access = pmpro_has_membership_access( $id, null, true );
	if ( ! empty( $access[1] ) && empty( $access[0] ) ) {
		$icon = sprintf(
			'<img class="my-pmpro-lock-icon" src="%1$s" alt="%2$s" width="24" height="24" style="display: inline-block; vertical-align: middle;" /> ',
			esc_url( PMPRO_URL . '/images/lock.svg' ),
			esc_attr__( 'Members only', 'pmpro-snippets-library' )
		);

		$title = $icon . $title;
	}

	return $title;
}
add_filter( 'the_title', 'my_pmpro_lock_icon_in_title', 10, 2 );
