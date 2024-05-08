<?php
/**
 * Force SSL on every page. Especially helpful at checkout.
 *
 * title: Force SSL on every page
 * layout: snippet
 * collection: misc
 * category: checkout, ssl
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_filter("pmpro_besecure", "__return_true");
