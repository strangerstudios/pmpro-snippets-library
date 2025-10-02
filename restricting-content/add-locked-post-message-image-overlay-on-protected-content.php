<?php
/**
 * Add a Locked Post Message Image Overlay on Protected Content
 *
 * title: Add Locked Post Message Image Overlay On Protected Content
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * link: https://www.paidmembershipspro.com/locked-post-message-image-overlay/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function protected_post_custom_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	global $pmpro_pages;

	// Return early if the function we are checking doesn't exist.
	if ( ! function_exists( 'pmpro_has_membership_access' ) ) {
		return $html;
	}

	// If the user doesn't have access, add the overlay.
	if ( function_exists( 'pmpro_has_membership_access' ) ) {
		if ( pmpro_has_membership_access() ) {
			return $html;
		} else {
			// If you're on a single post, the overlay includes a button.
			// Update this line to change the link or set it to another URL.
			if ( ! empty( $pmpro_pages['levels'] ) ) {
				$levels_page_link = get_permalink( $pmpro_pages['levels'] );
			}

			// Build the custom overlay.
			$new_html  = '<div class="pmpro_protected_post_featured_image">';
			$new_html .= $html;
			$new_html .= '<div class="pmpro_protected_post_blur_mask">';
			$new_html .= '<p><i class="dashicons dashicons-lock"></i><br />Unlock this post by becoming a member.</p>';
			if ( is_single() && ! empty( $levels_page_link ) ) {
				$new_html .= '<p><a class="pmpro_protected_post_button" href="' . esc_url( $levels_page_link ) . '">Join Now</a></p>';
			}
			$new_html .= '</div> <!-- end pmpro_protected_post_blur_mask -->';
			$new_html .= '</div> <!-- end pmpro_protected_post_featured_image -->';
			$html      = $new_html;
		}
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'protected_post_custom_post_thumbnail_html', 10, 5 );
