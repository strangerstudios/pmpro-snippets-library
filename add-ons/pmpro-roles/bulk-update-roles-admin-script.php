<?php
/*
 * Bulk update user's based off their level via admin page.
 * Learn more at https://www.paidmembershipspro.com/update-role-capabilities/
 *
 * title: Bulk update exsisting user's based off their level via admin page.
 * layout: snippet
 * collection: add-ons, pmpro-roles
 * category: capabilities
 * link: https://www.paidmembershipspro.com/how-to-bulk-update-user-roles-to-a-custom-role-for-their-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * To run add 'updateroles' to the URL with the value of the level ID you want to update user's for.
 * This will only affect non-admins with an active membership level. Be sure to setup your level role settings per level before running this script.
 * EXAMPLE -  http://yoursite.com/wp-admin/?updateroles=1
 */
 
function bulk_update_roles_for_member_levels() {
	global $wpdb;
	if ( ! empty( $_REQUEST[ 'updateroles' ] ) && current_user_can( 'manage_options' )) {

        $level_id = intval( $_REQUEST['updateroles'] );

        $SQL = "SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE status = 'active' AND membership_id = $level_id";

        $results = $wpdb->get_results( $SQL, ARRAY_A );

        if ( empty( $results ) ) {
            echo "no members found for level ID (" . $level_id . ")";
            exit;
        }

		// PMPro Roles Add On not installed, bail.
		if ( ! class_exists( 'PMPro_Roles' ) ) {
			echo "Paid Memberships Pro - Roles Add On not installed. Please install and try again.";
			exit;
		}
        
        $role = new PMPro_Roles();
        foreach( $results as $users ) {
            $user_id = $users['user_id'];
            $user = new WP_User( $user_id );
            $ignored_roles = array( 'administrator', 'editor' );
			if ( array_intersect( $ignored_roles, $user->roles ) ) {
				continue;
			}

            // If the user exists only.
            if ( empty( $user->ID ) ) {
                continue;
            }


           $role_changes = $role->user_change_level( $level_id, $user_id );

        }

        echo 'Script Finished.';
        exit;
  
	}
}
add_action( 'init', 'bulk_update_roles_for_member_levels' );
