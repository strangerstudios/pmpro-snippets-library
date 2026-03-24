<?php
/**
 * Log all WordPress emails in the PMPro email log, not just PMPro emails.
 * By default, PMPro only logs emails it sends itself. This snippet uses
 * the pmpro_should_log_email filter to capture every outbound email
 * sent through WordPress.
 *
 * Note: Logging all emails increases database size. Monitor your
 * wp_pmpro_email_logs table over time on high-traffic sites.
 *
 * title: Log All WordPress Emails in the PMPro Email Log
 * layout: snippet
 * collection: email
 * category: email-log
 * link: [add post URL after publish]
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
add_filter( 'pmpro_should_log_email', '__return_true' );
