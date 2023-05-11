<?php // do not copy this line

/**
 *  Redirect members only to home page or any other page on your site after logging out of WordPress
 * 
 * title: Redirect members on logout
 * layout: snippet
 * collection: misc
 * category: members logout redirect
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 */

 function redirect_members_on_logout( $user_id ) {
    // Check if the user has membership levels 1, 2, or 3, replace with your membership level ID
    if (function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel(array(1, 2, 3), $user_id)) {
	// to redirect to your home page - comment out line 24 and uncomment line 22
   	// wp_safe_redirect( home_url() );
    // Replace '/blah' with the actual URL or slug of your preferred page
         wp_redirect('/blah'); 
        exit;
    }
}

add_action('wp_logout', 'redirect_members_on_logout', 10, 1);
