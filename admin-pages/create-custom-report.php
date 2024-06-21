
<?php
/**
 * Create a custom report
 *
 * title: Create a custom report
 * layout: snippet
 * collection: admin-pages
 * category: reports
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add a Custom Report to the Memberships > Reports Screen in Paid Memberships Pro.
 *
 * For each report, add a line like:
 * global $pmpro_reports;
 * $pmpro_reports['slug'] = 'Title';
 *
 * For each report, also write two functions:
 * pmpro_report_{slug}_widget()   to show up on the report homepage.
 * pmpro_report_{slug}_page()     to show up when users click on the report page widget.
 *
 */

 global $pmpro_reports;
 $pmpro_reports['sample'] = __('My Sample Report', 'pmpro-reports-extras');
 
 // Sample Report for Metabox
 function pmpro_report_sample_widget() {	 ?>
     <span id="pmpro_report_sample" class="pmpro_report-holder">
         <p>Hi! I'm a sample report!</p>
         <?php if ( function_exists( 'pmpro_report_sample_page' ) ) { ?>
             <p class="pmpro_report-button">
                 <a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=pmpro-reports&report=sample' ); ?>"><?php _e('Details', 'paid-memberships-pro' );?></a>
             </p>
         <?php } ?>	
     </span>
     <?php
 }
 
 // Sample Report for Individual Report Page
 function pmpro_report_sample_page() { ?>
     <h1><?php _e( 'This is a Sample', 'pmpro-reports-extras' ); ?></h2>
     <p>This report demonstrates how to add a custom report to PMPro. Enjoy!.</p>
     <?php
 }