<?php
/**
 * Change the excerpt length using PHP for non-members.
 *
 * title: How to Show a Longer Post Excerpt for Non-Members and SEO
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * link: https://www.paidmembershipspro.com/how-to-show-a-longer-post-excerpt-for-non-members-and-seo/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * To use this customization, be sure "Show Excerpts to Non-Members?"
 * is set to "Yes, show excerpts" in Memberships -> Settings -> Advanced Settings -> Content Settings.
 * Will only affect auto generated excerpts. Manually created excerpts will remain unchanged.
 */

function my_pmpro_non_member_excerpt_length( $length ) {
	if ( ! pmpro_hasMembershipLevel() ) {
		$length = 200; // Change the number of words for non-members.
	}

	return $length;
}
add_filter( 'excerpt_length', 'my_pmpro_non_member_excerpt_length', 20 );
