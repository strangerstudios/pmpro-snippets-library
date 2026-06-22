<?php
/**
 * Limit the number of Ninja Forms post submissions per month
 * based on the user's PMPro membership level.
 *
 * When a member reaches their level's monthly limit, this snippet
 * replaces the Ninja Form shortcode or block on the submission page
 * with a custom message. Members on a level that is not listed fall
 * back to a default limit.
 *
 * title: Limit the Number of Member Post Submissions per Month When Using Ninja Forms and PMPro
 * layout: snippet
 * collection: integration-compatibility
 * category: ninja-forms, content
 * link: https://www.paidmembershipspro.com/member-submitted-content-with-ninja-forms/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Replace the Ninja Forms shortcode or block with a message when a
 * member has reached their monthly submission limit.
 *
 * @param string $content The post content.
 * @return string Modified content.
 */
function my_pmpro_nf_limit_submissions_by_level( $content ) {
	// Only run on the submission page and single pages/posts.
	if ( ! is_page( 123 ) ) { // TODO: Replace 123 with the ID of your submission page.
		return $content;
	}

	// Bail if PMPro is not active.
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return $content;
	}

	$user_id = get_current_user_id();

	// Build level-based submission limits. Add or remove levels as needed.
	// Format: level_id => max submissions per month.
	$level_limits = array(
		1 => 2,  // Level 1 members may submit 2 posts per month.
		2 => 5,  // Level 2 members may submit 5 posts per month.
	);

	// Determine the limit for the current user. A member may hold more than one
	// level, so use the highest limit among all the levels they have.
	$submission_limit = null;
	foreach ( $level_limits as $level_id => $limit ) {
		if ( pmpro_hasMembershipLevel( $level_id, $user_id ) ) {
			if ( is_null( $submission_limit ) || $limit > $submission_limit ) {
				$submission_limit = $limit;
			}
		}
	}

	// No matching level found - fall back to the default limit.
	if ( is_null( $submission_limit ) ) {
		$submission_limit = 1; // Default limit for members on any other level.
	}

	// Count this user's posts submitted in the current calendar month.
	// We only need to know whether they've reached the limit, so cap the query
	// at $submission_limit rows and skip the expensive full-count (SQL_CALC_FOUND_ROWS).
	$first_of_month = current_time( 'Y-m-01 00:00:00' );
	$post_count_query = new WP_Query(
		array(
			'author'         => $user_id,
			'post_type'      => 'post', // TODO: Change to your submission post type if using a CPT.
			'post_status'    => array( 'publish', 'pending', 'draft' ),
			'date_query'     => array(
				array(
					'after'     => $first_of_month,
					'inclusive' => true,
				),
			),
			'fields'         => 'ids',
			'posts_per_page' => $submission_limit,
			'no_found_rows'  => true,
		)
	);

	// If the member is under their limit, show the form as normal.
	if ( $post_count_query->post_count < $submission_limit ) {
		return $content;
	}

	// Member has reached their limit - replace the form with a message.
	$custom_message = sprintf(
		'<p class="pmpro_message pmpro_error">%s</p>',
		esc_html__(
			'You have reached your monthly submission limit. Please check back next month to submit more content.',
			'pmpro-snippets-library'
		)
	);

	// Strip the Ninja Forms shortcode and Gutenberg block from the content
	// and insert the message in their place.
	$content = preg_replace( '/\[ninja_form[^\]]*\]|<!--\s*wp:ninja-forms\/form\b.*?\/-->/is', $custom_message, $content );

	return $content;
}
// Use priority 8 so this runs before do_blocks (9) and do_shortcode (11),
// while the raw block comment and shortcode are still present in the content.
add_filter( 'the_content', 'my_pmpro_nf_limit_submissions_by_level', 8 );