<?php
/**
 * Disable all Popup Maker popups if user has membership level 1.
 *
 * title: Disable all Popup Maker popups if user has membership level 1.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, popup-maker
 * link: https://www.paidmembershipspro.com/membership-logic-enable-disable-popups-popup-maker-popups-optinmonster/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_pum_popup_is_loadable( $is_loadable, $popup_id ) {
	// Bail if the popup is already disabled.
	if ( ! $is_loadable ) {
		return $is_loadable;
	}

	// Disable popups if current user is a member of level 1.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( 1 ) ) {
		$is_loadable = false;
	}

	return $is_loadable;
}
add_filter( 'pum_popup_is_loadable', 'my_pmpro_pum_popup_is_loadable', 1000, 2 );
