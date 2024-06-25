<?php
/**
* Automatically Disable All PMPro Email
* The function below will disable all email sent by Paid Memberships Pro –
* - both for the member and the administrator.
*
* title: Disable All PMPro Email
* layout: snippet
* collection: email
* category: disable
* link: https://www.paidmembershipspro.com/disable-emails-member-user-admin/
*
* You can add this recipe to your site by creating a custom plugin
* or using the Code Snippets plugin available for free in the WordPress repository.
* Read this companion article for step-by-step directions on either method.
* https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
*/


function my_pmpro_disable_all_email( $recipient, $email ) {
    return null;
}
add_filter("pmpro_email_recipient", "my_pmpro_disable_all_email", 10, 2);
