<?php // do not copy this line

/**
 *  Redirect users to home page or any other page on your site after logging out of WordPress
 * 
 * title: Redirect users on logout
 * layout: snippet
 * collection: misc
 * category: logout redirect
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 */

  function my_redirect_after_logout_wp(){
   // redirect to site home page
      wp_redirect( home_url() );
   // uncomment line 22 and comment line 20 if you want to redirect to a specified page instead
   // wp_redirect( home_url('your-page-slug-here') ); 
 exit;
}

add_action('wp_logout','my_redirect_after_logout_wp');
