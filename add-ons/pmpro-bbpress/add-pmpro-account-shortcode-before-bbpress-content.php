<?php
/**
 * Add the [pmpro_account] shortcode before other bbPress profile content.
 *
 * title: Add PMPro Account Shortcode Before Other bbPress Profile Content
 * layout: snippet
 * collection: bbpress
 * category: shortcode, bbpress
 * link: https://www.paidmembershipspro.com/set-bbpress-user-profile-membership-account-page/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_bbp_template_before_user_profile() {
	global $current_user;

	$bbp_user_id = bbp_get_user_id( 0, true, false );

	if ( $bbp_user_id == $current_user->ID && shortcode_exists( 'pmpro_account' ) ) {
		echo do_shortcode( '[pmpro_account]' );
	}
}
add_action( 'bbp_template_before_user_profile', 'my_pmpro_bbp_template_before_user_profile', 15, 0 );
