<?php
/**
 * Add the Member Menu navigation to the Member Links section on the Account Page.
 *
 * title: Add Member Menu to Account Page Member Links
 * layout: snippet
 * collection: frontend-pages
 * category: account-page
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_links_top() {
	echo '<li><h3>Account Menu</h3>';
	$pmpro_login_widget_menu_defaults = array(
		'theme_location'  => 'pmpro-login-widget',
		'container'       => 'nav',
		'container_id'    => 'pmpro-member-navigation',
		'container_class' => 'pmpro-member-navigation',
		'fallback_cb'	  => false,
		'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
	);
	wp_nav_menu( $pmpro_login_widget_menu_defaults );
	echo '</li>';

	// Add link to onboarding page.
	if ( pmpro_hasMembershipLevel( '1' ) ) {
		echo '<li><hr /><a href="/membership-checkout/?pmpro_level=5">Upgrade to Full Membership</a></li>';
	}
}
add_action( 'pmpro_member_links_top', 'my_pmpro_member_links_top' );