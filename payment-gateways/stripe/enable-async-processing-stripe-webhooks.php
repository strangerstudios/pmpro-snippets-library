<?php
/**
 * Enable async processing of Stripe webhooks via Action Scheduler
 *
 * title: Enable Async Processing for Stripe Webhooks in Paid Memberships Pro
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: action-scheduler
 * link: tbd
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_filter( 'pmpro_stripe_webhook_enable_async_processing', '__return_true' );
