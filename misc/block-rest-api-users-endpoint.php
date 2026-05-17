<?php
/**
 * Block the REST API users endpoint for non-admins.
 *
 * title: Block the REST API users endpoint to hide usernames
 * layout: snippet
 * collection: misc
 * category: security, login
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * By default, GET /wp-json/wp/v2/users returns every user who has authored
 * a published post — login slug, display name, and avatar URL. This snippet
 * blocks the /wp/v2/users route (and the single-user /wp/v2/users/<id> variant)
 * for anyone without the list_users capability, while leaving the rest of
 * the REST API untouched so the block editor, search, forms, and other
 * plugins keep working.
 */
function my_block_rest_api_users_endpoint( $result, $server, $request ) {
	// If another callback already returned a response, respect it.
	if ( null !== $result ) {
		return $result;
	}

	// Allow users who are normally permitted to list users (admins, etc.).
	if ( current_user_can( 'list_users' ) ) {
		return $result;
	}

	// Block /wp/v2/users and /wp/v2/users/<id>.
	if ( strpos( $request->get_route(), '/wp/v2/users' ) === 0 ) {
		return new WP_Error(
			'rest_user_cannot_view',
			'Sorry, you are not allowed to list users.',
			array( 'status' => 401 )
		);
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'my_block_rest_api_users_endpoint', 10, 3 );
