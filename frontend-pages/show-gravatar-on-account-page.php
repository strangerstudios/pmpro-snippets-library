<?php
/**
 * Show a link to Gravatar and an avatar preview on the Profile section of the Membership Account page.
 *
 * title: Show Gravatar on Membership Account Page
 * layout: snippet
 * collection: frontend-pages
 * category: avatar, gravatar, account-page
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function show_gravatar_pmpro_account_bullets_top() {
	global $current_user;
	echo '<li class="alignright" style="display: inline-block; list-style: none; margin: -5rem 0 0 0;">' . get_avatar( $current_user->ID, 160 ) . '</li>';
}
add_action( 'pmpro_account_bullets_top', 'show_gravatar_pmpro_account_bullets_top' );

function show_gravatar_pmpro_account_bullets_bottom() {
	echo '<li><strong>' . __( 'Avatar', 'pmpro' ) . ':</strong> <a href="http://gravatar.com/" target="_blank">' . __( 'Update at Gravatar.com', 'pmpro' ) . '</a></li>';
}
add_action( 'pmpro_account_bullets_bottom', 'show_gravatar_pmpro_account_bullets_bottom' );
