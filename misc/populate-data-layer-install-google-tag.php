<?php
/**
 * Builds the dataLayer and loads GTM tracking in the head.
 * Includes Custom Dimensions and Ecommerce data.
 *
 */
function pmpro_add_google_tag_manager_to_head() {
	global $pmpro_pages;

	// Don't track admins.
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	// Set up the Custom Dimension data.
	$gtag_config_custom_dimensions = array();
	$post_type = '';
	if ( is_singular() ) {
		$post_type = get_post_type( get_the_ID() );
	}
	if ( ! empty( $post_type ) ) {
		$gtag_config_custom_dimensions['post_type'] = esc_html( $post_type );
	}

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

	$category = '';
	if ( is_single() ) {
		$categories = get_the_category( get_the_ID() );
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$category_names[] = $category->slug;
			}
		    $category =  implode( ',', $category_names );
		}
	}
	if ( ! empty( $category ) ) {
		$gtag_config_custom_dimensions['category'] = esc_html( $category );
	}

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

	// Add ecommerce tracking if this is the confirmation page.
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['confirmation'] ) && function_exists( 'pmpro_get_session_var' ) && ! empty( pmpro_get_session_var( 'pmpro_gtag_order' ) ) ) {
		global $pmpro_invoice;

		// Get the order in the session variable.
		$pmpro_gtag_order = pmpro_get_session_var( 'pmpro_gtag_order' );

		// Set the ecommerce dataLayer script if the order ID matches the session variable.
		if ( ! empty( $pmpro_invoice ) && ! empty( $pmpro_invoice->id ) && $pmpro_invoice->id === $pmpro_gtag_order ) {
			$pmpro_invoice->getMembershipLevel();

			// Build an array of actionField data.
			$gtag_config_ecommerce_action_field = array();
			$gtag_config_ecommerce_action_field['id'] = $pmpro_invoice->code;
			$gtag_config_ecommerce_action_field['affiliation'] = get_bloginfo( 'name' );
			$gtag_config_ecommerce_action_field['revenue'] = $pmpro_invoice->total;
			if ( ! empty( $pmpro_invoice->tax ) ) {
				$gtag_config_ecommerce_action_field['tax'] = $pmpro_invoice->tax;
			}
			if ( $pmpro_invoice->getDiscountCode() ) {
				$gtag_config_ecommerce_action_field['coupon'] = $pmpro_invoice->discount_code->code;
			}

			// Build an array of Product Data.
			$gtag_config_ecommerce_products = array();
			$gtag_config_ecommerce_products['name'] = $pmpro_invoice->membership_level->name;
			$gtag_config_ecommerce_products['id'] = 'pmpro-' . $pmpro_invoice->membership_level->id;
			$gtag_config_ecommerce_products['price'] = $pmpro_invoice->membership_level->initial_payment;
			$gtag_config_ecommerce_products['quantity'] = 1;
		}
	}

	// Add the checkout level to tracking if this is the checkout page. Useful for setting up "Add to Cart" events.
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['checkout'] ) ) {
		global $pmpro_level;
		$gtag_config_custom_dimensions['checkout_level'] = esc_html( $pmpro_level->id );
	}
	?>
	<script>
		window.dataLayer = window.dataLayer || [];
		<?php if ( ! empty( $gtag_config_custom_dimensions ) ) { ?>
			dataLayer.push(<?php echo wp_json_encode( $gtag_config_custom_dimensions, JSON_NUMERIC_CHECK ); ?>);
		<?php } ?>
		<?php if ( ! empty( $gtag_config_ecommerce_action_field ) ) { ?>
			dataLayer.push({ ecommerce: null });
			dataLayer.push({
				'ecommerce': {
    				'purchase': {
    					'actionField': <?php echo wp_json_encode( $gtag_config_ecommerce_action_field, JSON_NUMERIC_CHECK ); ?>,
    					'products': <?php echo wp_json_encode( array( $gtag_config_ecommerce_products ), JSON_NUMERIC_CHECK ); ?>
    				}
    			}
    		});
    	<?php } ?>
	</script>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-YOURIDHERE');</script>
	<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'pmpro_add_google_tag_manager_to_head' );
