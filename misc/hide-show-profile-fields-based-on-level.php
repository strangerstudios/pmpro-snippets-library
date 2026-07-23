<?php
/**
 * Hide or Show Fields on Member Profiles based on Membership Level
 *
 * title: Hide or Show Fields on Member Profiles based on Membership Level
 * layout: snippet
 * collection: misc
 * category: profile
 * link: https://www.paidmembershipspro.com/hide-or-show-fields-on-member-profiles-based-on-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_hide_show_profile_elements( $elements, $user ) {

	$premium_elements = array( 'user_url', 'facebook', 'twitter', 'linkedin', 'instagram' );
	$premium_levels   = array( 1, 2, 5, 6 );

	if ( ! empty( $elements ) && ! pmpro_hasMembershipLevel( $premium_levels, $user->ID ) ) {

		$new_elements = array();

		foreach ( $elements as $key => $element ) {

			$include = true;

			foreach ( $premium_elements as $premium_element ) {
				if ( ! isset( $element[1] ) || $element[1] == $premium_element ) {
					$include = false;
					break;
				}
			}

			if ( $include ) {
				$new_elements[] = $element;
			}
		}

		$elements = $new_elements;
	}

	return $elements;
}
add_filter( 'pmpro_member_profile_elements', 'my_pmpro_hide_show_profile_elements', 10, 2 );