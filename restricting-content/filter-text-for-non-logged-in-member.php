<?php
/**
 * Note: This recipe is using a deprecated filter. Please use the pmpro_no_access_message_body instead.
 *
 * Filter the restricted message shown to logged-in non-member users.
 * 
 * title: Filter the teaser message text for a logged in non-member
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_non_member_text_filter( $text ) {
	global $post;

	$access    = pmpro_has_membership_access( $post->ID, null, true );
	$level_ids = $access[1];

	if ( is_array( $level_ids ) && in_array( 2, $level_ids ) ) {
		$text = '<h4>This page requires a Bronze Account of higher.</h4><p><a href="/membership-checkout/?pmpro_level=2">Upgrade Now »</a></p>';
	} elseif ( is_array( $level_ids ) && in_array( 3, $level_ids ) ) {
		$text = '<h4>This page requires a Silver Account or higher.</h4><p><a href="/membership-checkout/?pmpro_level=3">Upgrade Now »</a></p>';
	} else {
		$text = '<h4>This page requires a Gold Account or higher.</h4><p><a href="/membership-checkout/?pmpro_level=4">Upgrade Now »</a></p>';
	}

	return $text;
}
add_filter( 'pmpro_non_member_text_filter', 'my_pmpro_non_member_text_filter', 5 );
