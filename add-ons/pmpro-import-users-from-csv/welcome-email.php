<?php
/**
 * Send a welcome email after a user has been imported
 *
 * title: Send a welcome email after a user has been imported
 * layout: snippet
 * collection: add-ons, import-user-from-csv
 * category: members, import, email
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmproiufcsv_email( $user_id ) {

    if ( ! function_exists( 'pmproiufcsv_is_iu_post_user_import' ) ) {
        return;
    }
    global $pmproiufcsv_email;

    wp_cache_delete( $user_id, 'users' );
    
    $sitename = get_bloginfo( 'sitename' );
    $user     = get_userdata( $user_id );

    //look for a membership level
    $membership_id = $user->import_membership_id;

    // get level object
    $level = pmpro_getLevel( $membership_id );

    $pmproiufcsv_email = array(
        'subject' => sprintf( 'Welcome to %s', $sitename ), //email subject, "Welcome to Sitename"
        'body'    => '<p>Your welcome email body text will go here.</p><p>Site: ' . $sitename . ' (' . site_url() . ')<br />Your login name: ' . $user->user_login . '<br />Membership Level: ' . $level->name . '</p>',        //email body
    );
}
add_action( 'is_iu_post_user_import', 'my_pmproiufcsv_email' );