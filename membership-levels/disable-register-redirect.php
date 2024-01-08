<?php
/**
 * Disable the PMPro redirect from default WordPress register page to PMPro levels page.
 *
 * title: Disable PMPro redirect to PMPro levels page.
 * layout: snippet
 * collection: membership-levels
 * category: redirect, filter
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
add_filter('pmpro_register_redirect', '__return_false');
