<?php
/**
 * Use Paid Memberships Pro filter to serve HTTPS on all pages
 *
 * title: Use PMPro filter to force HTTPS on all pages
 * layout: snippet
 * collection: admin-pages
 * category: admin, security
 * link: https://www.paidmembershipspro.com/debugging-https-ssl-issues/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
add_filter("pmpro_besecure", "__return_true");
