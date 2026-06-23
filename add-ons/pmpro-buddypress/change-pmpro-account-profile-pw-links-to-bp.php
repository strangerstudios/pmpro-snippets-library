<?php
/**
 * title: Change Edit Profile Action Links on the Membership Account Page to Use BuddyPress Profile
 * layout: snippet
 * collection: profile
 * category: integration
 * link: TBD
 * 
 * Change the "Edit Profile" and "Change Password" action links on the
 * Paid Memberships Pro Membership Account page to use the logged-in
 * member's BuddyPress profile and settings screens.
 *
 * @param array $pmpro_profile_action_links The profile action links shown on the Membership Account page.
 * @return array The updated profile action links.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_account_profile_action_links_to_buddypress( $pmpro_profile_action_links ) {
    // BuddyPress must be active and the user must be logged in.
    if ( ! is_user_logged_in() || ! function_exists( 'bp_loggedin_user_domain' ) ) {
        return $pmpro_profile_action_links;
    }

    $user_domain = bp_loggedin_user_domain();

    if ( empty( $user_domain ) ) {
        return $pmpro_profile_action_links;
    }

    $edit_profile_url    = trailingslashit( $user_domain . 'profile' );
    $change_password_url = trailingslashit( $user_domain . 'settings/general' );

    if ( isset( $pmpro_profile_action_links['edit-profile'] ) ) {
        $pmpro_profile_action_links['edit-profile'] = sprintf(
            '<span class="%1$s"><a id="pmpro_actionlink-profile" href="%2$s">%3$s</a></span>',
            esc_attr( pmpro_get_element_class( 'pmpro_card_action' ) ),
            esc_url( $edit_profile_url ),
            esc_html__( 'Edit Profile', 'paid-memberships-pro' )
        );
    }

    if ( isset( $pmpro_profile_action_links['change-password'] ) ) {
        $pmpro_profile_action_links['change-password'] = sprintf(
            '<span class="%1$s"><a id="pmpro_actionlink-change-password" href="%2$s">%3$s</a></span>',
            esc_attr( pmpro_get_element_class( 'pmpro_card_action' ) ),
            esc_url( $change_password_url ),
            esc_html__( 'Change Password', 'paid-memberships-pro' )
        );
    }

    return $pmpro_profile_action_links;
}
add_filter( 'pmpro_account_profile_action_links', 'my_pmpro_account_profile_action_links_to_buddypress' );