<?php
/**
 * Builds the dataLayer to send custom dimensions for post_type, author, and the user's membership_level to Google Tag Manager.
 *
 * title: Send Custom Dimensions to the dataLayer with Google Tag Manager
 * layout: snippet
 * collection: misc
 * category: analytics
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_custom_dimensions_gtm() {
	// Set up the Custom Dimension for post_type.
	$gtag_config_custom_dimensions = array();
	$post_type = '';
	if ( is_singular() ) {
		$post_type = get_post_type( get_the_ID() );
	}
	if ( ! empty( $post_type ) ) {
		$gtag_config_custom_dimensions['post_type'] = esc_html( $post_type );
	}

	// Set up the Custom Dimension for author.
    $author = '';
	if ( is_singular() ) {
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
			}
		}
		$firstname = get_the_author_meta( 'user_firstname' );
		$lastname  = get_the_author_meta( 'user_lastname' );
		if ( ! empty( $firstname ) || ! empty( $lastname ) ) {
				$author = trim( $firstname . ' ' . $lastname );
		} else {
			$author = 'user-' . get_the_author_meta( 'ID' );
		}
	}
	if ( ! empty( $author ) ) {
		$gtag_config_custom_dimensions['author'] = esc_html( $author );
	}

	// Set up the Custom Dimension for membership_level.
    $membership_level = '';
	// Get the value to track for the current user.
	if ( is_user_logged_in() && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		// Get the current users's membership level ID. 
		$current_user_membership_level = pmpro_getMembershipLevelForUser( get_current_user_id() );
		if ( empty( $current_user_membership_level ) ) {
			// Set the tracked membership level ID to no_level.
			$membership_level = 'no_level';			
		} else {
			$membership_level = $current_user_membership_level->ID;
		}
	} else {
		// Set the tracked membership level ID to no_level.
		$membership_level = 'no_level';
	}
	if ( ! empty( $membership_level ) ) {
		$gtag_config_custom_dimensions['membership_level'] = esc_html( $membership_level );
	}
	?>
	<script>
		window.dataLayer = window.dataLayer || [];
		<?php if ( ! empty( $gtag_config_custom_dimensions ) ) { ?>
			dataLayer.push(<?php echo wp_json_encode( $gtag_config_custom_dimensions, JSON_NUMERIC_CHECK ); ?>);
		<?php } ?>
	</script>
	<?php
}
add_action( 'wp_head', 'my_pmpro_add_custom_dimensions_gtm' );
