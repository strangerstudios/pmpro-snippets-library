<?php
/**
 * Add a monthly/annual pricing toggle to the PMPro Levels Page and Advanced Levels Page shortcodes/blocks.
 * 
 * Style the toggle with CSS using the following classes: .pmpro-pricing-toggle (wrapper),
 * .pmpro-pricing-toggle-btn (buttons), .pmpro-pricing-toggle-btn.is-active (active button),
 * .pmpro-pricing-toggle-btn:not(.is-active) (inactive button).
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
add_action( 'wp_enqueue_scripts', 'my_pmpro_pricing_toggle_inline_assets' );
function my_pmpro_pricing_toggle_inline_assets() {
	global $post;

	if ( is_admin() || ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	// Detect either the default levels shortcode or the Advanced Levels
	// Page Add-on's shortcode/block, anywhere on the site — not just a
	// specific page slug, since PMPro's own docs note these can be placed
	// on any page.
	$has_levels_shortcode = has_shortcode( $post->post_content, 'pmpro_levels' )
		|| has_shortcode( $post->post_content, 'pmpro_advanced_levels' )
		|| has_block( 'pmpro-advanced-levels/advanced-levels-page', $post );

	if ( ! $has_levels_shortcode ) {
		return;
	}

	// Register a dummy handle so we have something to attach inline JS/CSS to.
	wp_register_script( 'pmpro-pricing-toggle', '', array(), '1.0.0', true );
	wp_enqueue_script( 'pmpro-pricing-toggle' );

	wp_register_style( 'pmpro-pricing-toggle', false, array(), '1.0.0' );
	wp_enqueue_style( 'pmpro-pricing-toggle' );

	// Settings available to the inline JS below.
	wp_localize_script( 'pmpro-pricing-toggle', 'pmproPricingToggleSettings', array(
		'priceSelector'     => '.pmpro_level-price',   // Element that wraps just the price text.
		'containerSelector' => '.pmpro_levels_table',  // Element to insert the toggle above.
		'defaultView'       => 'monthly',              // 'monthly' or 'annual'
	) );

	$js = <<<'JS'
( function () {
	'use strict';
	var settings = window.pmproPricingToggleSettings || {};
	var PRICE_SELECTOR = settings.priceSelector || '.pmpro_level-price';
	var CONTAINER_SELECTOR = settings.containerSelector || '.pmpro_levels_table';
	var state = { view: settings.defaultView === 'annual' ? 'annual' : 'monthly' };
	var ANNUAL_PATTERN = /([£$€])\s?([\d,]+(?:\.\d{1,2})?)\s*(?:USD\s*)?(?:per|\/|every)\s*(?:1\s*)?year(?:\(s\))?/i;
	function formatCurrency( symbol, amount ) {
		return symbol + amount.toLocaleString( undefined, {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		} );
	}
	function parsePrice( node ) {
		var match = node.textContent.match( ANNUAL_PATTERN );
		if ( ! match ) {
			return null;
		}
		return {
			symbol: match[ 1 ],
			annual: parseFloat( match[ 2 ].replace( /,/g, '' ) )
		};
	}
	function renderNode( node ) {
		var symbol = node.dataset.pmproCurrencySymbol;
		var annual = parseFloat( node.dataset.pmproAnnualAmount );
		if ( ! symbol || isNaN( annual ) ) {
			return;
		}
		var monthly = annual / 12;
		node.textContent = '';
		var wrapper = document.createElement( 'span' );
		wrapper.className = 'pmpro-pricing-toggle-price';
		var mainLine = document.createElement( 'strong' );
		var disclosure = document.createElement( 'span' );
		disclosure.className = 'pmpro-pricing-toggle-disclosure';
		if ( state.view === 'monthly' ) {
			mainLine.textContent = formatCurrency( symbol, monthly ) + ' /month';
			disclosure.textContent = ' (billed as ' + formatCurrency( symbol, annual ) + ' per year)';
		} else {
			mainLine.textContent = formatCurrency( symbol, annual ) + ' /year';
			disclosure.textContent = ' (equivalent to ' + formatCurrency( symbol, monthly ) + '/month)';
		}
		wrapper.appendChild( mainLine );
		wrapper.appendChild( disclosure );
		node.appendChild( wrapper );
	}
	function initNodes() {
		var nodes = document.querySelectorAll( PRICE_SELECTOR );
		var matched = [];
		nodes.forEach( function ( node ) {
			var parsed = parsePrice( node );
			if ( ! parsed ) {
				return;
			}
			node.dataset.pmproCurrencySymbol = parsed.symbol;
			node.dataset.pmproAnnualAmount = parsed.annual;
			matched.push( node );
			// TESTING AID: visibly highlight the row this script converted,
			// so it's obvious at a glance which level(s) were matched.
			// Remove this classList.add line (and the matching CSS rule)
			// once you're done testing.
			node.classList.add( 'pmpro-pricing-toggle-highlight' );
			var row = node.closest( 'tr' );
			if ( row ) {
				row.classList.add( 'pmpro-pricing-toggle-highlight-row' );
			}
		} );
		return matched;
	}
	function renderAll( nodes ) {
		nodes.forEach( renderNode );
	}
	function buildToggle() {
		var wrap = document.createElement( 'div' );
		wrap.className = 'pmpro-pricing-toggle';
		wrap.setAttribute( 'role', 'group' );
		wrap.setAttribute( 'aria-label', 'Pricing display' );
		var monthlyBtn = document.createElement( 'button' );
		monthlyBtn.type = 'button';
		monthlyBtn.dataset.view = 'monthly';
		monthlyBtn.className = 'pmpro-pricing-toggle-btn';
		monthlyBtn.textContent = 'Monthly';
		var annualBtn = document.createElement( 'button' );
		annualBtn.type = 'button';
		annualBtn.dataset.view = 'annual';
		annualBtn.className = 'pmpro-pricing-toggle-btn';
		annualBtn.textContent = 'Annual';
		var live = document.createElement( 'span' );
		live.className = 'screen-reader-text pmpro-pricing-toggle-live';
		live.setAttribute( 'aria-live', 'polite' );
		wrap.appendChild( monthlyBtn );
		wrap.appendChild( annualBtn );
		wrap.appendChild( live );
		return wrap;
	}
	function setActiveButton( toggleEl ) {
		toggleEl.querySelectorAll( '.pmpro-pricing-toggle-btn' ).forEach( function ( btn ) {
			var isActive = btn.dataset.view === state.view;
			btn.classList.toggle( 'is-active', isActive );
			btn.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		} );
	}
	function run() {
		var matchedNodes = initNodes();
		if ( ! matchedNodes.length ) {
			return;
		}
		renderAll( matchedNodes );
		var container = document.querySelector( CONTAINER_SELECTOR );
		var toggleEl = buildToggle();
		if ( container && container.parentNode ) {
			container.parentNode.insertBefore( toggleEl, container );
		} else {
			// containerSelector didn't match anything — fall back to walking up
			// from the first matched price node to the nearest reasonable
			// wrapper, covering both table layouts and card/grid layouts
			// (like the Advanced Levels Page Add-on).
			var fallbackAncestor = matchedNodes[ 0 ].closest( 'table, ul, ol, .pmpro_levels, .pmpro_table, .entry-content, main, article' );
			if ( fallbackAncestor && fallbackAncestor.parentNode ) {
				fallbackAncestor.parentNode.insertBefore( toggleEl, fallbackAncestor );
			} else {
				console.warn( 'PMPro Pricing Toggle: could not find a container to insert the toggle before. Update containerSelector in the PHP settings.' );
			}
		}
		setActiveButton( toggleEl );
		toggleEl.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.pmpro-pricing-toggle-btn' );
			if ( ! btn || btn.dataset.view === state.view ) {
				return;
			}
			state.view = btn.dataset.view;
			setActiveButton( toggleEl );
			renderAll( matchedNodes );
			var live = toggleEl.querySelector( '.pmpro-pricing-toggle-live' );
			live.textContent = state.view === 'monthly'
				? 'Showing monthly equivalent pricing, billed annually.'
				: 'Showing annual pricing.';
		} );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', run );
	} else {
		run();
	}
} )();
JS;

	wp_add_inline_script( 'pmpro-pricing-toggle', $js );

	$css = <<<'CSS'
.pmpro-pricing-toggle {
	display: inline-flex;
	gap: 4px;
	margin-bottom: 1.5em;
	padding: 4px;
	border-radius: 999px;
	background: #f0f0f1;
}
.pmpro-pricing-toggle-btn {
	border: none;
	background: transparent;
	padding: 8px 20px;
	border-radius: 999px;
	cursor: pointer;
	font-weight: 600;
	color: #50575e;
}
.pmpro-pricing-toggle-btn.is-active {
	background: #fff;
	color: #1d2327;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}
.pmpro-pricing-toggle-disclosure {
	display: block;
	font-size: 0.8em;
	font-weight: normal;
	opacity: 0.75;
}
.screen-reader-text {
	position: absolute;
	left: -9999px;
	width: 1px;
	height: 1px;
	overflow: hidden;
}
/* TESTING AID: highlights the row(s) the script actually converted.
   Remove this rule (and the matching JS classList.add) once you're
   done testing and ready to go live. */
.pmpro-pricing-toggle-highlight-row {
	outline: 2px dashed #2271b1;
	background: rgba(34, 113, 177, 0.08);
}
CSS;

	wp_add_inline_style( 'pmpro-pricing-toggle', $css );
}