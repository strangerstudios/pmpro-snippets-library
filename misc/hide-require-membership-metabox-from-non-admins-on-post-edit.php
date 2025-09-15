<?php
/**
 * Hide the PMPro "Require Membership" meta box from non-admins 
 * on the Classic Editor add/edit post screen.
 *
 * title: Hide the PMPro Require Membership meta box from non-admins
 * layout: snippet
 * collection: misc
 * category: content
 * link: https://www.paidmembershipspro.com/hide-the-pmpro-require-membership-meta-box-from-non-admins-on-addedit-post-screen/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_hide_require_membership_meta_box() {
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_action( 'add_meta_boxes', 'pmpro_page_meta_wrapper' );
	}
}
add_action( 'init', 'my_pmpro_hide_require_membership_meta_box' );
