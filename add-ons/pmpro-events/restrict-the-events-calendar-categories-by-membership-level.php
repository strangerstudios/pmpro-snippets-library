<?php
/**
 * Restrict The Events Calendar event categories by membership level.
 * Adds the "Require Membership" level checkboxes to Events > Event Categories. Every event in a restricted category requires one of the checked levels.
 * Requires Paid Memberships Pro v3.8.1 or higher.
 *
 * title: Restrict The Events Calendar Event Categories by Membership Level
 * layout: snippet
 * collection: add-ons
 * category: events, the-events-calendar, restriction, terms, taxonomies
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_restrict_tribe_event_categories( $taxonomies ) {
	$taxonomies[] = 'tribe_events_cat';

	return $taxonomies;
}
add_filter( 'pmpro_restrictable_taxonomies', 'my_pmpro_restrict_tribe_event_categories' );
