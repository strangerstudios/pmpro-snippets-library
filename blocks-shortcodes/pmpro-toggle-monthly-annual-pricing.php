<?php
/**
 * Add a monthly/annual pricing toggle to the PMPro Levels Page and Advanced Levels Page shortcodes/blocks.
 *
 * The toggle switches how each level's price is displayed: monthly levels can be shown as their yearly
 * total, and annual levels as their per-month equivalent. The amount members are charged does not change.
 *
 * Only levels billed every 1 month or every 1 year (or 12 months) are converted. One-time, free, trial,
 * payment-limited and other billing frequencies are left unchanged, as are prices shown with a discount code.
 *
 * Style the toggle with CSS using the following classes: .pmpro-pricing-toggle (wrapper),
 * .pmpro-pricing-toggle-btn (buttons), .pmpro-pricing-toggle-btn.is-active (active button),
 * .pmpro-pricing-toggle-note (the "Billed annually/monthly" note under a converted price).
 *
 * title: Add a Monthly/Annual Pricing Toggle to the PMPro Levels Page
 * layout: snippet-example
 * collection: block-shortcode
 * category: display
 * link: TBD
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_pricing_toggle() {
	global $post;

	if ( ! is_a( $post, 'WP_Post' ) || ! function_exists( 'pmpro_getAllLevels' ) ) {
		return;
	}

	// Only load on pages with the core or Advanced Levels Page shortcode/block.
	if ( ! has_shortcode( $post->post_content, 'pmpro_levels' )
		&& ! has_block( 'pmpro/levels-page', $post )
		&& ! has_shortcode( $post->post_content, 'pmpro_advanced_levels' )
		&& ! has_block( 'pmpro-advanced-levels/advanced-levels-page', $post ) ) {
		return;
	}

	// Build the converted price for each monthly/annual level, keyed by the level's original
	// cost text so the JS only swaps price elements that show exactly that price.
	$prices = array();
	foreach ( pmpro_getAllLevels( true, true ) as $level ) {
		// Skip free, one-time, trial and payment-limited levels.
		if ( (float) $level->billing_amount <= 0 || ! empty( $level->billing_limit ) || ! empty( $level->trial_limit ) ) {
			continue;
		}

		if ( 'Month' === $level->cycle_period && 1 === (int) $level->cycle_number ) {
			$native = 'monthly';
		} elseif ( ( 'Year' === $level->cycle_period && 1 === (int) $level->cycle_number ) || ( 'Month' === $level->cycle_period && 12 === (int) $level->cycle_number ) ) {
			$native = 'annual';
		} else {
			continue;
		}

		// Copy the level with its billing converted to the other period and let PMPro build the cost text.
		$converted                 = clone $level;
		$converted->billing_amount = ( 'monthly' === $native ) ? $level->billing_amount * 12 : $level->billing_amount / 12;
		$converted->cycle_number   = 1;
		$converted->cycle_period   = ( 'monthly' === $native ) ? 'Year' : 'Month';

		// Keep a different up-front payment (e.g. a signup fee) as-is.
		if ( pmpro_round_price( $level->initial_payment ) === pmpro_round_price( $level->billing_amount ) ) {
			$converted->initial_payment = $converted->billing_amount;
		}

		$note = ( 'monthly' === $native ) ? __( 'Billed monthly', 'pmpro-snippets-library' ) : __( 'Billed annually', 'pmpro-snippets-library' );

		// Short cost text is used by the core levels page, full cost text by the Advanced Levels Page Add-on's price="full" option.
		foreach ( array( true, false ) as $short ) {
			$text = html_entity_decode( wp_strip_all_tags( pmpro_getLevelCost( $level, true, $short ) ), ENT_QUOTES, 'UTF-8' );
			$key  = trim( preg_replace( '/[\s\x{00A0}]+/u', ' ', $text ) );

			$prices[ $key ] = array(
				'native' => $native,
				'html'   => wp_kses_post( pmpro_getLevelCost( $converted, true, $short ) ) . '<span class="pmpro-pricing-toggle-note">' . esc_html( $note ) . '</span>',
			);
		}
	}

	if ( empty( $prices ) ) {
		return;
	}

	$toggle = '<div class="pmpro-pricing-toggle" role="group" aria-label="' . esc_attr__( 'Pricing display', 'pmpro-snippets-library' ) . '">'
		. '<button type="button" class="pmpro-pricing-toggle-btn" data-view="monthly">' . esc_html__( 'Monthly', 'pmpro-snippets-library' ) . '</button>'
		. '<button type="button" class="pmpro-pricing-toggle-btn" data-view="annual">' . esc_html__( 'Annual', 'pmpro-snippets-library' ) . '</button>'
		. '<span class="pmpro-pricing-toggle-live" aria-live="polite"></span>'
		. '</div>';

	// Register a dummy handle so we have something to attach inline JS/CSS to.
	wp_register_script( 'pmpro-pricing-toggle', '', array(), '1.0.0', true );
	wp_enqueue_script( 'pmpro-pricing-toggle' );
	wp_register_style( 'pmpro-pricing-toggle', false, array(), '1.0.0' );
	wp_enqueue_style( 'pmpro-pricing-toggle' );

	wp_localize_script( 'pmpro-pricing-toggle', 'pmproPricingToggle', array(
		'defaultView' => 'monthly', // 'monthly' or 'annual'
		'prices'      => $prices,
		'toggle'      => $toggle,
		'showMonthly' => __( 'Showing monthly pricing.', 'pmpro-snippets-library' ),
		'showAnnual'  => __( 'Showing annual pricing.', 'pmpro-snippets-library' ),
	) );

	$js = <<<'JS'
// Runs in the footer, after the levels markup.
( function () {
	var settings = window.pmproPricingToggle;
	var container = document.querySelector( '#pmpro_levels' );
	var items = [];
	document.querySelectorAll( '.pmpro_level-price' ).forEach( function ( node ) {
		var data = settings.prices[ node.textContent.replace( /\s+/g, ' ' ).trim() ];
		if ( data ) {
			items.push( { node: node, data: data, original: node.innerHTML } );
		}
	} );
	if ( ! container || ! items.length ) {
		return;
	}

	container.insertAdjacentHTML( 'beforebegin', settings.toggle );
	var toggle = container.previousElementSibling;

	function render( view ) {
		items.forEach( function ( item ) {
			item.node.innerHTML = item.data.native === view ? item.original : item.data.html;
		} );
		toggle.querySelectorAll( '.pmpro-pricing-toggle-btn' ).forEach( function ( btn ) {
			btn.classList.toggle( 'is-active', btn.dataset.view === view );
			btn.setAttribute( 'aria-pressed', btn.dataset.view === view ? 'true' : 'false' );
		} );
	}
	render( settings.defaultView );

	toggle.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '.pmpro-pricing-toggle-btn' );
		if ( btn ) {
			render( btn.dataset.view );
			toggle.querySelector( '.pmpro-pricing-toggle-live' ).textContent = btn.dataset.view === 'monthly' ? settings.showMonthly : settings.showAnnual;
		}
	} );
} )();
JS;
	wp_add_inline_script( 'pmpro-pricing-toggle', $js );

	// Uses PMPro's own color variables (with fallbacks) so the toggle matches the site's PMPro styles.
	$css = <<<'CSS'
.pmpro-pricing-toggle {
	display: inline-flex;
	gap: 4px;
	margin: 0 0 var(--pmpro--base--spacing--medium, 18px);
	padding: 4px;
	border: 1px solid var(--pmpro--color--border, #777777);
	border-radius: 999px;
	background: var(--pmpro--color--base, #ffffff);
}
.pmpro-pricing-toggle .pmpro-pricing-toggle-btn {
	margin: 0;
	padding: 8px 20px;
	border: none;
	border-radius: 999px;
	background: transparent;
	box-shadow: none;
	color: var(--pmpro--color--contrast, #222222);
	cursor: pointer;
	font: inherit;
	font-weight: 600;
	line-height: 1.2;
	text-transform: none;
}
.pmpro-pricing-toggle .pmpro-pricing-toggle-btn.is-active {
	background: var(--pmpro--color--accent, #0c3d54);
	color: var(--pmpro--color--base, #ffffff);
}
.pmpro-pricing-toggle .pmpro-pricing-toggle-btn:focus-visible {
	outline: 2px solid var(--pmpro--color--accent, #0c3d54);
	outline-offset: 2px;
}
.pmpro-pricing-toggle-note {
	display: block;
	font-size: 0.875em;
	opacity: 0.8;
}
.pmpro-pricing-toggle-live {
	position: absolute;
	width: 1px;
	height: 1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
}
CSS;
	wp_add_inline_style( 'pmpro-pricing-toggle', $css );
}
add_action( 'wp_enqueue_scripts', 'my_pmpro_pricing_toggle' );
