<?php
/**
 * Example of how to add conditional links to the Membership Account > My Memberships section.
 *
 * title: Add Member Action links on the Membership Account Page 
 * layout: snippet
 * collection: frontend-pages
 * category: membership-action-links
 * link: https://www.paidmembershipspro.com/add-custom-links-to-my-memberships-dashboard/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_action_links( $links, $level_id ) {
    // Check if the member is on Level 2.
    if ( $level_id == 2 ) {
        // Add an Upgrade link to checkout for Level 3.
        $links['upgrade'] = '<a id="pmpro_actionlink-upgrade" href="' . esc_url( add_query_arg( 'pmpro_level', 3, pmpro_url( 'checkout' ) ) ) . '">' . esc_html__( 'Upgrade to Pro Membership', 'pmpro-snippets-library' ) . '</a>';
    }

    // Example: Add a bonus link for Level ID 3.
    if ( $level_id == 3 ) {
        $links['bonuses'] = '<a id="pmpro_actionlink-bonuses" href="https://yoursite.com/bonuses">' . esc_html__( 'View Your Exclusive Bonuses', 'pmpro-snippets-library' ) . '</a>';
    }

    return $links;
}
add_filter( 'pmpro_member_action_links', 'my_pmpro_member_action_links', 10, 2 );
