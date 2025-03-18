<?php
/** 
 * Prevent members from being able to login unless they have an active membership
 *
 * title: Restrict User Login to Active Members
 * layout: snippet
 * collection: misc
 * category: login 
 * link:https://www.paidmembershipspro.com/restrict-user-login-for-members-only/#h-code-recipe-1-restrict-user-login-to-active-members
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_validate_membership_login( $user, $username, $password ) {
	
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) ) {		
		return $user;
	}
	
	$user = get_user_by( 'login', $username ); 
	
	if ( empty( $user ) ) {
	   return;
	}

	if ( in_array( 'administrator', $user->roles ) ) {
		return $user;
	}

	$membership_levels = pmpro_getMembershipLevelsForUser( $user->ID );

	//No membership level assigned - no login allowed
	if ( empty( $membership_levels ) ) { 
		return;
	}

	return $user;

}
add_filter( 'authenticate', 'my_pmpro_validate_membership_login', 99, 3 );