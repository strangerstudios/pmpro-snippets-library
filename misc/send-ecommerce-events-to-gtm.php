<?php
/** 
 * Builds the dataLayer to send ecommerce events for view, add_to_cart, and purchase to Google Tag Manager.
 *
 * title: Send View, Add to Cart, and Purchase Events to Google Tag Manager
 * layout: snippet
 * collection: misc
 * category: analytics
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_add_ga4_ecommerce() {
	global $pmpro_pages, $pmpro_currency;

	// Build an array of events.
	$gtag_config_events_push = array();

	// Track "view" event for levels viewed on the levels page.
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['levels'] ) ) {

		// Make sure pmpro_levels has all levels.
		if ( ! isset( $pmpro_all_levels ) ) {
			$pmpro_all_levels = pmpro_getAllLevels( false, true );
		}

		// Get our specific levels for the levels page.
		$our_levels_to_track = array();
		$our_levels = array( 1, 2, 3 );
		foreach ( $our_levels as $level_id ) {
			foreach ( $pmpro_all_levels as $level ) {
				if ( $level->id == $level_id && true == $level->allow_signups ) {
					$our_levels_to_track[$level->id] = $level;
					break;
				}
			}
		}

		// Create a unique event per level viewed.
		foreach ( $our_levels_to_track as $pmpro_level ) {
			// Set the ecommerce dataLayer script.
			$gtag_config_ecommerce_data = array();
			$gtag_config_ecommerce_data['currency'] = $pmpro_currency;
			$gtag_config_ecommerce_data['value'] = $pmpro_level->initial_payment;

			// Build an array of Product Data.
			$gtag_config_ecommerce_products = array();
			$gtag_config_ecommerce_products['item_id'] = 'pmpro-' . $pmpro_level->id;
			$gtag_config_ecommerce_products['item_name'] = $pmpro_level->name;
			$gtag_config_ecommerce_products['affiliation'] = get_bloginfo( 'name' );
			$gtag_config_ecommerce_products['index'] = 0;
			$gtag_config_ecommerce_products['price'] = $pmpro_level->initial_payment;
			$gtag_config_ecommerce_products['quantity'] = 1;


			// Add the product data to the ecommerce data.
			$gtag_config_ecommerce_data['items'] = array( $gtag_config_ecommerce_products );
			$gtag_config_event_push['event'] = 'view_item';
			$gtag_config_event_push['ecommerce'] = $gtag_config_ecommerce_data;

			// Add this complete event to the array of events.
			$gtag_config_events_push[] = $gtag_config_event_push;
		}
	}

	// Add ecommerce tracking if this is the checkout page.
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['checkout'] ) ) {
		global $pmpro_level;

		// Set the ecommerce dataLayer script.
		$gtag_config_ecommerce_data = array();
		$gtag_config_ecommerce_data['currency'] = $pmpro_currency;
		$gtag_config_ecommerce_data['value'] = $pmpro_level->initial_payment;
		
		// Build an array of Product Data.
		$gtag_config_ecommerce_products = array();
		$gtag_config_ecommerce_products['item_id'] = 'pmpro-' . $pmpro_level->id;
		$gtag_config_ecommerce_products['item_name'] = $pmpro_level->name;
		$gtag_config_ecommerce_products['affiliation'] = get_bloginfo( 'name' );
		$gtag_config_ecommerce_products['index'] = 0;
		$gtag_config_ecommerce_products['price'] = $pmpro_level->initial_payment;
		$gtag_config_ecommerce_products['quantity'] = 1;

		
		// Add the product data to the ecommerce data.
		$gtag_config_ecommerce_data['items'] = array( $gtag_config_ecommerce_products );
		$gtag_config_event_push['event'] = 'add_to_cart';
		$gtag_config_event_push['ecommerce'] = $gtag_config_ecommerce_data;

		// Add this complete event to the array of events.
		$gtag_config_events_push[] = $gtag_config_event_push;
	}

	// Add ecommerce tracking if this is the confirmation page.
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['confirmation'] ) ) {
		global $pmpro_invoice, $current_user;

		// User completed a free checkout. Get their last invoice for the data layer.
		if ( empty( $pmpro_invoice ) && pmpro_isLevelFree( $current_user->membership_level ) ) {
			$pmpro_invoice = new MemberOrder();
			$pmpro_invoice->getLastMemberOrder( $current_user->ID, apply_filters( 'pmpro_confirmation_order_status', array( 'success', 'pending', 'token' ) ) );
		}

		// Set the ecommerce dataLayer script.
		if ( ! empty( $pmpro_invoice ) && ! empty( $pmpro_invoice->id ) ) {
			$pmpro_invoice->getMembershipLevel();

			// Set the ecommerce dataLayer script.
			$gtag_config_ecommerce_data = array();
			$gtag_config_ecommerce_data['transaction_id'] = $pmpro_invoice->code;
			$gtag_config_ecommerce_data['value'] = $pmpro_invoice->membership_level->initial_payment;
			if ( ! empty( $pmpro_invoice->tax ) ) {
				$gtag_config_ecommerce_data['tax'] = $pmpro_invoice->tax;
			}
			$gtag_config_ecommerce_data['currency'] = $pmpro_currency;
			if ( $pmpro_invoice->getDiscountCode() ) {
				$gtag_config_ecommerce_data['coupon'] = $pmpro_invoice->discount_code->code;
			}

			// Build an array of product data.
			$gtag_config_ecommerce_products = array();
			$gtag_config_ecommerce_products['item_id'] = 'pmpro-' . $pmpro_invoice->membership_level->id;
			$gtag_config_ecommerce_products['item_name'] = $pmpro_invoice->membership_level->name;
			$gtag_config_ecommerce_products['affiliation'] = get_bloginfo( 'name' );
			if ( $pmpro_invoice->getDiscountCode() ) {
				$gtag_config_ecommerce_products['coupon'] = $pmpro_invoice->discount_code->code;
			}
			$gtag_config_ecommerce_products['index'] = 0;
			$gtag_config_ecommerce_products['price'] = $pmpro_invoice->membership_level->initial_payment;
			$gtag_config_ecommerce_products['quantity'] = 1;

			// Add the product data to the ecommerce data.
			$gtag_config_ecommerce_data['items'] = array( $gtag_config_ecommerce_products );
			$gtag_config_event_push['event'] = 'purchase';
			$gtag_config_event_push['ecommerce'] = $gtag_config_ecommerce_data;

			// Add this complete event to the array of events.
			$gtag_config_events_push[] = $gtag_config_event_push;
		}
	}
	?>
	<script>
		window.dataLayer = window.dataLayer || [];
		<?php if ( ! empty( $gtag_config_ecommerce_data ) && ! empty( $gtag_config_events_push ) ) {
			// We have an array of events to track.
			foreach( $gtag_config_events_push as $gtag_config_event_push ) { ?>
				dataLayer.push(<?php echo wp_json_encode( $gtag_config_event_push, JSON_NUMERIC_CHECK ); ?>);
			<?php }
			}
		?>
	</script>
	<?php
}
add_action( 'wp_head', 'my_pmpro_add_ga4_ecommerce' );
