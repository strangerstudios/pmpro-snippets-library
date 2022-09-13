<?php
/*
 * Show a list of the sponsored (child) member accounts on the parent's profile page on the Member Directory's Profile page.
 *
 * title: Show list of child account on parent's profile
 * layout: snippet
 * collection: add-ons, pmpro-sponsored-members
 * category: frontend-pages
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function show_sponsored_child_accounts_pmpro_member_profile_after( $pu ) {
	$sponsored_ids = pmprosm_getChildren( $pu->ID );

	if ( empty( $sponsored_ids ) ) {
		return;
	}

	$sponsored_list = new WP_User_Query(
		array(
			'include' => $sponsored_ids,
			'orderby' => 'name',
			'order'   => 'ASC',
		)
	);

	$sponsored_members = $sponsored_list->get_results();
	if ( ! empty( $sponsored_members ) ) {
		echo '<strong>Sponsored Members</strong>';
		echo '<ul>';
		foreach ( $sponsored_members as $sponsored_member ) {
			echo '<li>' . esc_html( $sponsored_member->data->display_name ) . '</li>';
		}
		echo '</ul>';
	}
}
add_action( 'pmpro_member_profile_after', 'show_sponsored_child_accounts_pmpro_member_profile_after' );
