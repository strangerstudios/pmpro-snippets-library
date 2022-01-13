<?php
/**
 * Delete the WordPress user when a PMPro member cancels
 *
 * Requires PMPro v1.4+
 * Note - Users are not deleted if:
 * (1) They are not cancelling their membership (i.e. $level_id != 0)
 * (2) They are an admin.
 * (3) The level change was initiated from the WP Admin Dashboard 
 * (e.g. when an admin changes a user's level via the edit user's page)
 * 
 * title: Delete the WordPress user when a PMPro member cancels
 * layout: snippet
 * collection: misc
 * category: user
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


function my_pmpro_after_change_membership_level( $level_id, $user_id ) {

    //are they cancelling? and don't do this from admin (e.g. when admin's are changing levels)
    if( empty( $level_id ) && !is_admin() ) {

        //only delete non-admins
        if( !user_can( $user_id, "manage_options" ) ) {

            //remove the delete hooks so we don't try to cancel the membership again
            remove_action( 'delete_user', 'pmpro_delete_user' );
            remove_action( 'wpmu_delete_user', 'pmpro_delete_user' );
            
            //delete the user
            require_once( ABSPATH . "/wp-admin/includes/user.php" );
            wp_delete_user( $user_id );   
            
            //add the delete hooks back in
            add_action( 'delete_user', 'pmpro_delete_user' );
            add_action( 'wpmu_delete_user', 'pmpro_delete_user' );

        }

    }

}
add_action( "pmpro_after_change_membership_level", "my_pmpro_after_change_membership_level", 10, 2 );