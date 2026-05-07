<?php
/**
 * Add a Meta Box to Make Individual Posts in a Protected Category Publicly Accessible
 *
 * This recipe adds a checkbox to the post edit screen that allows authors to
 * mark individual posts as publicly accessible, even if the post's category
 * is restricted to members only.
 *
 * title: Make Individual Posts in a Protected Category Publicly Accessible
 * layout: snippet
 * collection: restricting-content
 * category: content, categories
 * link: https://www.paidmembershipspro.com/allow-public-access-to-post-in-protected-category/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add a meta box to the post edit screen.
 *
 * Only attached to the 'post' post type. To support pages or custom post types,
 * call add_meta_box() again with the desired screen.
 */
function my_pmpro_override_meta_box() {
	add_meta_box(
		'pmpro_override_access',
		'Access Override',
		'my_pmpro_override_meta_box_html',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'my_pmpro_override_meta_box' );

/**
 * Render the meta box HTML.
 */
function my_pmpro_override_meta_box_html( $post ) {
	// Note: post authors can toggle this on their own posts (edit_post cap is true for them).
	// If you want to restrict the override to admins only, tighten this check to current_user_can( 'edit_others_posts' ).
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}
	wp_nonce_field( 'my_pmpro_override_save', 'my_pmpro_override_nonce' );
	$value = get_post_meta( $post->ID, '_pmpro_override_access', true );
	?>
	<label>
		<input type="checkbox" name="pmpro_override_access" value="1" <?php checked( $value, '1' ); ?> />
		Make this post public (bypass membership restrictions).
	</label>
	<?php
}

/**
 * Save the meta box value.
 */
function my_pmpro_override_save_meta( $post_id ) {
	if ( ! isset( $_POST['my_pmpro_override_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_pmpro_override_nonce'] ) ), 'my_pmpro_override_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! empty( $_POST['pmpro_override_access'] ) ) {
		update_post_meta( $post_id, '_pmpro_override_access', '1' );
	} else {
		delete_post_meta( $post_id, '_pmpro_override_access' );
	}
}
add_action( 'save_post_post', 'my_pmpro_override_save_meta' );

/**
 * Override the PMPro access filter to allow public access, if set.
 *
 * Runs at priority 99 to ensure it runs after PMPro's default access filters.
 */
function my_pmpro_override_access_filter( $hasaccess, $mypost, $myuser, $post_membership_levels ) {
	if ( $hasaccess ) {
		return $hasaccess;
	}
	if ( get_post_meta( $mypost->ID, '_pmpro_override_access', true) === '1' ) {
		$hasaccess = true;
	}
	return $hasaccess;
}
add_filter('pmpro_has_membership_access_filter', 'my_pmpro_override_access_filter', 99, 4);
