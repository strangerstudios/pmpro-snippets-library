<?php
/**
 * Filter to only allow access to a protected post by post ID after a specific calendar date.
 * Updated to show the date on the post and archive page.
 *
 * Customize for your site: Update the $posts_in_series_with_dates array with your post IDs => date (formatted as YYYY-MM-DD).
 *
 * title: Unlock post IDs on specific dates.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/unlock-content-specific-dates/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_filter_content_with_unlock_message( $content ) {
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	global $post;

	// Define your post IDs and their unlock dates.
	$posts_in_series_with_dates = array(
		'29' => '2025-07-14',
		'32' => '2025-10-29',
		'34' => '2025-10-30'
	);

	if ( array_key_exists( $post->ID, $posts_in_series_with_dates ) ) {
		$unlock_timestamp  = strtotime( $posts_in_series_with_dates[ $post->ID ] );
		$current_timestamp = current_time( 'timestamp' );

		if ( $current_timestamp < $unlock_timestamp ) {
			$formatted_date = date_i18n( get_option( 'date_format' ), $unlock_timestamp );
			$message = '<div class="pmpro-content-note">This content will be available on <strong>' . esc_html( $formatted_date ) . '</strong>.</div>';

			if ( is_singular() ) {
				// On single post/page, replace full content.
				return $message;
			} else {
				// On archive/search, show message instead of excerpt/full content.
				return $message;
			}
		}
	}

	return $content;
}
add_filter( 'the_content', 'my_pmpro_filter_content_with_unlock_message' );
add_filter( 'the_excerpt', 'my_pmpro_filter_content_with_unlock_message' ); // For themes using excerpts