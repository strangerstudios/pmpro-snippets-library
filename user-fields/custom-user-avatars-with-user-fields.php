<?php
/**
 * Create Custom User Avatars with the User Fields
 *
 * Allow members to upload their avatar using a User field during checkout or on the Member Profile Edit page.
 *  
 * title: Create Custom User Avatars with User Fields
 * layout: snippet
 * collection: user-fields
 * category: user-avatars
 * link: https://www.paidmembershipspro.com/custom-user-avatars-member-profile-edit-page/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Filter the saved or updated User Avatar meta field value and add the image to the Media Library.
function my_updated_user_avatar_user_meta( $meta_id, $user_id, $meta_key, $meta_value ) {
    if ( 'user_avatar' === $meta_key ) {
        $filename = $meta_value['fullpath'];
        $filetype = wp_check_filetype( basename( $filename ), null );
        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_status'    => 'inherit',
        );
        $attach_id = wp_insert_attachment( $attachment, $filename );
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_user_meta( $user_id, 'wp_user_avatar', $attach_id );
    }
}
add_action( 'added_user_meta', 'my_updated_user_avatar_user_meta', 10, 4 );
add_action( 'updated_user_meta', 'my_updated_user_avatar_user_meta', 10, 4 );

// Filter the display of the get_avatar function to use our local avatar.
function my_user_avatar_filter( $avatar, $id_or_email, $size, $default, $alt ) {
    $my_user = get_userdata( $id_or_email );
    if ( ! empty( $my_user ) ) {
        $avatar_id = get_user_meta( $my_user->ID, 'wp_user_avatar', true );
        if ( ! empty( $avatar_id ) ) {
            $avatar = wp_get_attachment_image_src( $avatar_id, array( $size, $size) );
            $avatar = "<img alt='{$alt}' src='{$avatar[0]}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
    }
    return $avatar;
}
add_filter( 'get_avatar', 'my_user_avatar_filter', 20, 5 );

// Add the User Avatar field at checkout and on the profile edit forms.
function my_pmpro_add_user_fields() {
    if ( ! function_exists( 'pmpro_add_user_field' ) ) {
        return false;
    }

    // Define the field for avatar upload
    $fields = array();
    $fields[] = new PMPro_Field(
        'user_avatar',              // input name and user meta key
        'file',                     // type of field
        array(
            'label'         => 'My Photo',               // custom field label
            'hint'          => 'Recommended size is 100px X 100px',
            'profile'       => true,                   // show in user profile
            'preview'       => true,                   // show a preview-sized version of the image
            'allow_delete'  => true,                   // Allow user to see Delete option on Avatar
        )
    );

    // Add a field group to put our fields into
    pmpro_add_field_group( 'profile-picture', 'Profile Picture' );

    // Add all of our fields into that group
    foreach ( $fields as $field ) {
        pmpro_add_user_field(
            'profile-picture',      // Which group to add to
            $field                  // PMPro_Field object
        );
    }
}

add_action( 'init', 'my_pmpro_add_user_fields' );
