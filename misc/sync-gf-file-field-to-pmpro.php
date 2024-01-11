<?php

/**
 * Sync a Gravity Form Upload Field type to a PMPro User File Field type on Gravity Form Submission and Assign a Membership Level to the User
 * This gist requires the Gravity Forms User Registration Add-On to work
 *
 * title: Sync Gravity Forms upload file field to PMPro User file Field
 * layout: snippet
 * collection: misc
 * category: Gravity Forms
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_connect_gf_to_userfields($user_id, $feed, $entry, $user_pass) {
    
    // Get the form ID
    $form_id = 1; // Replace with your actual form ID

    /**
     * Give the user a level
     */
	 
    if( function_exists( 'pmpro_changeMembershipLevel' ) ) {
        pmpro_changeMembershipLevel( 2, $user_id ); //Give the user a membership level ID of 2
    }

    /**
     * Use a Gravity Forms FILE Field and update it to work with a 
     * PMPro User Fields field
     */

    // Get the uploaded file field ID or unique identifier
    $file_field_id = 7; // Replace with the actual field ID or identifier
 
    // Get the field ID or unique identifier for the username field
    $user = get_user_by( 'id', $user_id );

    $username = $user->user_login;
    
    // Get the uploaded file URL
    $file_url = rgar($entry, $file_field_id);

    //Was a file field filled out? If so, lets use it.
    if( ! empty( $file_url ) ) {
    
        // Debugging: Log file URL
        error_log('File URL: ' . $file_url);
    
        // Get the username entered by the user
        $username = rgar($entry, $username_field_id);
    
        // Debugging: Log username
        error_log('Username: ' . $username);
    
        // Modify the file URL to extract the file path
        $file_path = str_replace( content_url(), WP_CONTENT_DIR, $file_url);
    
        if (!empty($file_path)) {
            // Debugging: Log file path
            error_log('File Path: ' . $file_path);
    
            // Debugging: Log destination directory and file
            $destination_dir = WP_CONTENT_DIR . '/uploads/pmpro-register-helper/' . $username . '/';
            $destination_file = $destination_dir . basename($file_path);
            error_log('Destination Directory: ' . $destination_dir);
            error_log('Destination File: ' . $destination_file);
    
            if (!file_exists($file_path)) {
                // Debugging: Log error message
                error_log('Error: File does not exist');
                return; // Stop further execution
            }
    
            if (!file_exists($destination_dir)) {
                wp_mkdir_p($destination_dir); // Create the destination directory if it doesn't exist
            }
    
            if (copy($file_path, $destination_file)) {
                // File copied successfully
    
                // Debugging: Log success message
                error_log('File copied successfully');
    
				        // Replace with the field name or identifier in PMP where you want to store the file information
                $field_name = 'profile_picture'; 
    
                // Debugging: Log user ID and field name
                error_log('User ID: ' . $user_id);
                error_log('Field Name: ' . $field_name);
    
                // Generate the file information array in the format used by Paid Memberships Pro
                $file_info = array(
                    'name' => basename($file_path),
                    'filename' => basename($file_path),
                    'new' => false,
                    'subfolder' => '',
                    'fullurl' => $file_url,
                    'previewurl' => $file_url,
                    'url' => $file_url,
                    'path' => $destination_file,
                    'type' => mime_content_type($destination_file),
                    'ext' => pathinfo($destination_file, PATHINFO_EXTENSION),
                );
              
                error_log( print_r( $file_info, true ) );
                // Update the user meta with the file information
                update_user_meta($user_id, $field_name, $file_info);
            } else {
                // Debugging: Log error message
                error_log('Error occurred while copying the file');
            }
        } else {
            // Debugging: Log error message
            error_log('Error: File path not retrieved');
        }

    }

 }

 add_action( 'gform_user_registered', 'mypmpro_connect_gf_to_userfields', 10, 4 );
