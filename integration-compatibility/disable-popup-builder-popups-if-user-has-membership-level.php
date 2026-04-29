<?php
/**
 * Disable Popup Builder popups if the user has a membership level.
 *
 * Popup Builder is a plugin available in the WordPress.org Repo here:
 * https://wordpress.org/plugins/popup-builder/
 *
 * title: Disable Popup Builder popups if the user has a membership level.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, popup-builder
 * link: https://www.paidmembershipspro.com/membership-logic-enable-disable-popups-popup-maker-popups-optinmonster/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_pub_disable_popups_for_members( $popup ) {
	// Bail if the user has a membership level.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && ! pmpro_hasMembershipLevel() ) {
		return $popup;
	}

	// Popups with these IDs will be disabled for members.
	$non_member_popup_ids = array( 123, 335 );

	// Disable popup.
	if ( in_array( $popup['id'], $non_member_popup_ids ) ) {
		$popup['status'] = false;
	}

	return $popup;
}
add_filter( 'sgpbOtherConditions', 'my_pmpro_pub_disable_popups_for_members' );
