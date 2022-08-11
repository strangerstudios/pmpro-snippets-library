# Snippets Library for Paid Memberships Pro and related Add Ons

We are aiming to collect all of the most useful Gists and Snippets here in one place.

A few of the benefits of this collection will be:

* **Quality Assurance:** Each snippet will get code reviewed for quality and compatibility
* **Access:** Easy to search via GitHub (or locally)
* **Offline Access:** Easy to checkout this repo locally by the team as needed
* **Maintainability:** One place to update as changes are needed going forward for any snippet (not stuck on any one GitHub user account)

A snippet should contain the full functionality — we should avoid having two separate snippets that rely on one another to achieve the desired result.

## Submitting new snippets or changes

1. Just go to the folder (or create a new path) for where you want to add/change a snippet.
2. Create a fork and submit Pull Request with your snippet work. Never use underscores in snippet file names or folder names.
3. The PR will be code reviewed.
4. In the mean time, you can provide customers with your own Gist URL of the snippet.
5. After PR is approved, the snippet will be merged.
6. Now the snippet is ready for inclusion on our site!

## Header Layout for snippets
Please include the following header format when submitting a snippet
```
/**
 * Describe what the snippet does in one sentence. (i.e. Add a checkbox to the checkout page.)
 * 
 * title: Add custom field to checkout
 * layout: snippet-example
 * collection: frontend-pages
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
```

## Choosing the "Collection" and Organizing Folders

The snippet folder should match the primary function or feature of the code. For example, if the snippet modifies a direct behavior of the checkout process or page, place the snippet in the "checkout" folder. If the snippet modifies a setting on the Payment Gateways page in the WordPress admin, while this may affect checkout, it belongs in the admin-pages folder.

Choose the folder/collection that your snippet integrates with directly, and use the category as a backup.

- add-ons: Anything related to an Add On. Create a subfolder for the Add On that exactly matches the Add On's folder name/slug.
- admin-pages: Snippets that modify the core PMPro plugin's admin area under Memberships > Settings.
- blocks-shortcodes: Snippets that adjust core PMPro block functionality or shortcode functionality OR snippets that add new custom block or shortcode features.
- checkout: Snippets that modify the checkout page and checkout process, including registration checks, checkout requirements, or additional functionality that is triggered during checkout.
- discount-codes: Snippets that modify how discount codes function on the frontend and for the user.
- email: Snippets that adjust how emails are sent, the email contents, adding additional emails, etc.
- frontend-pages: Snippets that modify how the frontend pages appear and the contents on the frontend pages.
- import-export: Snippets that adjust default export features of the members list, orders list, and reports. (propose to rename to export only? We don't support import in core PMPro. Should those go in Add Ons?)
- integration-compatibility: Basic snippets that integrate core PMPro with other plugins to resolve issues or bridge functionality.
- localization: Snippets related to translating the core PMPro plugin's terms to other languages or using translation functions to change core plugin language (i.e. "Membership" > "Subscription").
- membership-levels: Functionality that changes how membership levels are assigned to users, cancellation behavior, expiration behavior, etc. This should not include snippets that adjust the Memberships > Settings > Membership Levels admin screens or features.
- misc: This collection is temporary, for all snippets that don't belong into any created collections. The idea is to move the snippets once we plan out our file structure.
- orders: Functionality that hooks into order updates, creation, changes, etc.
- payment-gateways: Anything that modifies or extends default payment gateway functionality. Create a subfolder for each gateway that exactly matches the gateway folder name in core PMPro. (Should this include gateway Add Ons or place those in Add Ons > Gateway Folder?)
- restricting-content: Snippets specifically related to how PMPro handles content restrictions, including behavior like default messages, redirection, extending for CPTs, etc.
- user-fields: Snippets that modify or extend the behavior of core PMPro user fields.
