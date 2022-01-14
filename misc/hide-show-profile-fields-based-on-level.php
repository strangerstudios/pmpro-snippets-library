<?php
/**
 * Hide or Show Fields on Member Profiles based on Membership Level
 *
 * title: Hide or Show Fields on Member Profiles based on Membership Level
 * layout: snippet
 * collection: misc
 * category: profile
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_profile_fields( $fields, $user ) {

	$premium_fields = array( 'user_url', 'facebook', 'twitter', 'linkedin', 'instagram' );
	$premium_levels = array( 1, 2, 5, 6 );

	if ( ! empty( $fields ) && ! pmpro_hasMembershipLevel( $premium_levels, $user->ID ) ) {

		$new_fields = array();

		foreach ( $fields as $key => $field ) {

			$include = true;

			foreach ( $premium_fields as $pfield ) {
				if ( ! isset( $field[1] ) || $field[1] == $pfield ) {
					$include = false;
					break;
				}
			}

			if ( $include ) {
				$new_fields[] = $field;
			}
		}

		$fields = $new_fields;
	}

	return $fields;
}
add_filter( 'pmpro_member_profile_fields', 'my_pmpro_member_profile_fields', 10, 2 );
