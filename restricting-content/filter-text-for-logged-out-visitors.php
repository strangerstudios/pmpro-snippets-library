<?php
/**
 * Note: This recipe is using a deprecated filter. Please use the pmpro_no_access_message_body instead.
 *
 * Filter the restricted message shown to logged-out visitors.
 *
 * title: Filter the teaser message text for logged-out visitors.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_not_logged_in_text_filter( $text ) {
	global $post;

	$access    = pmpro_has_membership_access( $post->ID, null, true );
	$level_ids = $access[1];

	if ( is_array( $level_ids ) && in_array( 2, $level_ids ) ) {
		$text = '<h4>This page requires a Bronze Account or higher.</h4><p>Already have an account? <a href="/login?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) . '">Login Now »</a></p><p>New to this site? <a href="/membership-checkout/?pmpro_level=2">Register Now »</a></p>';
	} elseif ( is_array( $level_ids ) && in_array( 4, $level_ids ) && ! in_array( 3, $level_ids ) ) {
		$text = '<h4>This page requires a Gold Account or higher.</h4><p>Already have an account? <a href="/login?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) . '">Login Now »</a></p><p>New to this site? <a href="/membership-checkout/?pmpro_level=4">Register Now »</a></p>';
	} else {
		$text = '<h4>This page requires a Silver Account or higher.</h4><p>Already have an account? <a href="/login?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) . '">Login Now »</a></p><p>New to this site? <a href="/membership-checkout/?pmpro_level=3">Register Now »</a></p>';
	}

	return $text;
}
add_filter( 'pmpro_not_logged_in_text_filter', 'my_pmpro_not_logged_in_text_filter', 5 );
