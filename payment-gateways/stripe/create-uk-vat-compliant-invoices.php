<?php
/**
 * Add UK VAT-Compliant Invoicing to PMPro with Stripe
 * Requires PMPro 3.6.1+
 *
 * - Fetches the member's own VAT registration number from their Stripe customer record
 *   and caches it in user meta (pmpro_stripe_tax_ids).
 * - Injects the member's own VAT number, a VAT invoice number, and company VAT details
 *   into payment-related PMPro emails using template variables (!!vat_info!!,
 *   !!vat_invoice_number!!, !!company_name!!, etc.).
 * - Renders the VAT invoice block on the frontend invoice page and admin order views.
 *
 * title: How to Add UK VAT-Compliant Invoicing to PMPro with Stripe
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: orders
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Fetch tax IDs from Stripe and cache them in user meta.
 * Returns the cached value immediately if one already exists.
 *
 * @param int         $user_id
 * @param MemberOrder $order
 * @return string Formatted tax IDs e.g. "EU_VAT: DE123456789, AU_ABN: 12345678901", or empty string.
 */
function my_pmpro_fetch_stripe_tax_ids( $user_id, $order ) {
	if ( empty( $order->gateway ) || strpos( $order->gateway, 'stripe' ) === false ) {
		return '';
	}

	// Return cached value if already fetched. 'none' means the user had no tax IDs at fetch time.
	$vat_info = get_user_meta( $user_id, 'pmpro_stripe_tax_ids', true );
	if ( ! empty( $vat_info ) ) {
		return $vat_info === 'none' ? '' : $vat_info;
	}

	try {
		// Resolve the Stripe customer ID from the most direct source available.
		// $order->stripe_customer is set during process() for checkout orders.
		// pmpro_stripe_customerid meta is reliable for renewals and repeat checkouts.
		// Subscription expand is the last resort for renewals with no cached customer ID.
		$customer_id = ! empty( $order->stripe_customer->id )
			? $order->stripe_customer->id
			: get_user_meta( $user_id, 'pmpro_stripe_customerid', true );

		if ( ! empty( $customer_id ) ) {
			$customer = \Stripe\Customer::retrieve( array(
				'id'     => $customer_id,
				'expand' => array( 'tax_ids' ),
			) );
		} elseif ( ! empty( $order->subscription_transaction_id ) ) {
			$subscription = \Stripe\Subscription::retrieve( array(
				'id'     => $order->subscription_transaction_id,
				'expand' => array( 'customer', 'customer.tax_ids' ),
			) );
			$customer = $subscription->customer;
		} else {
			return '';
		}

		if ( empty( $customer->tax_ids->data ) ) {
			update_user_meta( $user_id, 'pmpro_stripe_tax_ids', 'none' );
			return '';
		}

		$tax_ids_string = '';
		foreach ( $customer->tax_ids->data as $tax_id ) {
			$value = sanitize_text_field( $tax_id->value );
			if ( ! empty( $value ) ) {
				$tax_ids_string = $value;
				break;
			}
		}

		update_user_meta( $user_id, 'pmpro_stripe_tax_ids', $tax_ids_string );

		return $tax_ids_string;

	} catch ( \Stripe\Exception\ApiErrorException $e ) {
		error_log( 'PMPro Stripe Tax IDs: order ' . $order->id . ' — ' . $e->getMessage() );
		return '';
	}
}

/**
 * Fetch and cache at priority 5 so user meta is populated before PMPro
 * sends the checkout confirmation email at priority 10.
 */
function my_pmpro_checkout_fetch_tax_ids( $user_id, $morder ) {
	if ( empty( $user_id ) || empty( $morder ) ) {
		return;
	}

	if ( empty( $morder->total ) || $morder->total <= 0 ) {
		return;
	}

	my_pmpro_fetch_stripe_tax_ids( $user_id, $morder );
}
add_action( 'pmpro_after_checkout', 'my_pmpro_checkout_fetch_tax_ids', 5, 2 );

/**
 * Fetch and cache at priority 5 so user meta is populated before PMPro
 * sends the renewal invoice email at priority 10.
 */
function my_pmpro_updated_order_fetch_tax_ids( $morder ) {
	if ( 'success' !== $morder->status || empty( $morder->user_id ) ) {
		return;
	}
	my_pmpro_fetch_stripe_tax_ids( $morder->user_id, $morder );
}
add_action( 'pmpro_updated_order', 'my_pmpro_updated_order_fetch_tax_ids', 5 );

/**
 * Inject VAT invoice variables into PMPro emails.
 *
 * Use !!variable!! syntax in email templates, e.g.:
 *   !!vat_info!! — member's own VAT registration number (if any)
 *   !!vat_invoice_number!! — VAT invoice number (e.g. "1234")
 *
 * Available in payment emails only:
 *   !!company_name!!, !!company_address!!, !!company_vat_number!!,
 *   !!company_email!!, !!company_url!!
 */
function my_pmpro_inject_tax_ids_into_email( $data, $email ) {
	// Member's own VAT registration number — available in all emails.
	if ( ! empty( $data['user_login'] ) ) {
		$user = get_user_by( 'login', $data['user_login'] );

		if ( ! empty( $user ) ) {
			$user_id  = (int) $user->ID;
			$vat_info = get_user_meta( $user_id, 'pmpro_stripe_tax_ids', true );

			// If no VAT info stored yet, try to pull it from the user's last non-free order.
			if ( empty( $vat_info ) || $vat_info === 'none' ) {
				$order_lookup = new MemberOrder();
				$morder       = $order_lookup->getLastMemberOrder( $user_id, 'success' );
				if ( ! empty( $morder ) && ! empty( $morder->id ) ) {
					my_pmpro_fetch_stripe_tax_ids( $user_id, $morder );
					$vat_info = get_user_meta( $user_id, 'pmpro_stripe_tax_ids', true );
				}
			}

			$data['vat_info'] = ( ! empty( $vat_info ) && $vat_info !== 'none' ) ? esc_html( $vat_info ) : '';
		}
	}

	// VAT invoice number derived from the order behind this email, if any.
	$data['vat_invoice_number'] = '';
	if ( ! empty( $data['order_id'] ) ) {
		$morder = new MemberOrder( $data['order_id'] );
		if ( ! empty( $morder->id ) ) {
			$data['vat_invoice_number'] = (string) (int) $morder->id;
		}
	}

	$data['company_name']       = 'Your Company Name';
	$data['company_address']    = '123 Example Street, Anytown, AB1 2CD';
	$data['company_vat_number'] = 'GB123456789';
	$data['company_email']      = 'billing@example.com';
	$data['company_url']        = 'https://example.com';

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_inject_tax_ids_into_email', 10, 2 );

/**
 * Render the VAT invoice block on the PMPro frontend invoice page.
 */
function my_pmpro_invoice_show_tax_ids( $morder ) {
	if ( empty( $morder->user_id ) ) {
		return;
	}

	$vat_invoice_number = (int) $morder->id;

	echo '<div class="pmpro_vat_invoice" style="margin-top:1.5em;border-top:1px solid #ddd;padding-top:1em;">';
	echo '<p><strong>' . esc_html__( 'VAT Invoice', 'pmpro-snippets-library' ) . ' #' . esc_html( $vat_invoice_number ) . '</strong><br>';
	echo 'Your Company Name<br>';
	echo '123 Example Street, Anytown, AB1 2CD<br>';
	echo 'VAT Number: GB123456789<br>';
	echo '<a href="https://example.com">https://example.com</a>';
	echo ' &bull; <a href="mailto:billing@example.com">billing@example.com</a></p>';
	echo '</div>';
}
add_action( 'pmpro_invoice_bullets_bottom', 'my_pmpro_invoice_show_tax_ids' );


/**
 * Register VAT column for orders list table.
 */
function my_pmpro_admin_orderlist_add_tax_ids_column( $columns ) {
	$columns['tax_ids'] = 'VAT Number';
	return $columns;
}
add_filter( 'pmpro_manage_orderslist_columns', 'my_pmpro_admin_orderlist_add_tax_ids_column', 10 );

/**
 * Add VAT breakdown rows to the admin orders list table.
 */
function my_pmpro_admin_order_show_tax_ids( $column_name, $order_id ) {
    if ( $column_name !== 'tax_ids' ) {
		return;
	}

	$morder = new MemberOrder( $order_id );
	if ( empty( $morder->user_id ) ) {
		return;
	}

	$vat_info = get_user_meta( (int) $morder->user_id, 'pmpro_stripe_tax_ids', true );
	if ( ! empty( $vat_info ) && $vat_info !== 'none' ) {
		echo esc_html( $vat_info );
	}
}
add_action( 'pmpro_manage_orderlist_custom_column', 'my_pmpro_admin_order_show_tax_ids', 10, 2 );

/**
 * Add customer VAT number to the Bill to section and VAT breakdown to the single order view (admin).
 */
function my_pmpro_order_single_meta_tax_ids( $meta, $order ) {
	if ( empty( $order->user_id ) ) {
		return $meta;
	}

	// Normalize the Bill To value: strip any trailing <br> tags so our appends are consistent.
	if ( isset( $meta['bill_to'] ) ) {
		$meta['bill_to']['value'] = preg_replace( '/(<br\s*\/?>\s*)+$/i', '', $meta['bill_to']['value'] );
	}

	// Append the customer's VAT registration number to the Bill to section.
	$vat_info = get_user_meta( (int) $order->user_id, 'pmpro_stripe_tax_ids', true );
	if ( ! empty( $vat_info ) && $vat_info !== 'none' && isset( $meta['bill_to'] ) ) {
		$sep = ! empty( trim( strip_tags( $meta['bill_to']['value'] ) ) ) ? '<br>' : '';
		$meta['bill_to']['value'] .= $sep . 'VAT Number: ' . esc_html( $vat_info );
	}

	// Add a dedicated VAT Invoice section in the admin only (frontend uses pmpro_invoice_bullets_bottom).
	if ( is_admin() ) {
		$vat_invoice_number = (int) $order->id;
		$meta['vat_invoice'] = array(
			'label' => 'VAT Invoice #' . $vat_invoice_number,
			'value' => 'Your Company Name<br>'
				. '123 Example Street, Anytown, AB1 2CD<br>'
				. 'VAT Number: GB123456789<br>'
				. 'https://example.com<br>'
				. 'billing@example.com',
		);
	}

	return $meta;
}
add_filter( 'pmpro_order_single_meta', 'my_pmpro_order_single_meta_tax_ids', 10, 2 );

// Replace Tax with "VAT" instead everywhere in PMPro.
function my_pmpro_replace_tax_with_vat( $translated_text, $text, $domain ) {
	if ( 'paid-memberships-pro' === $domain ) {
		if ( 'Tax' === $text ) {
			return 'VAT';
		}
		if ( 'Tax:' === $text ) {
			return 'VAT:';
		}
	}
	return $translated_text;
}
add_filter( 'gettext', 'my_pmpro_replace_tax_with_vat', 10, 3 );

/**
 * Show "No VAT charged" note below the Total row for $0 orders.
 */
function my_pmpro_vat_exempt_price_part( $price_parts, $order ) {
	$is_free_order = empty( $order->total ) || (float) $order->total <= 0;
	$is_zero_tax   = ! isset( $order->tax ) || (float) $order->tax <= 0;

	if ( ! $is_free_order && ! $is_zero_tax ) {
		return $price_parts;
	}

	$price_parts['vat_exempt'] = array(
		'label' => '',
		'value' => 'No VAT charged — free membership / 100% discount applied',
	);

	return $price_parts;
}
add_filter( 'pmpro_get_price_parts_with_total', 'my_pmpro_vat_exempt_price_part', 10, 2 );
