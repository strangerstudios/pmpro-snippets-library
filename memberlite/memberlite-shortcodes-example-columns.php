<?php
/**
 * Example of using the Memberlite Row and Columns shortcode.
 * Learn more at https://www.paidmembershipspro.com/documentation/memberlite/memberlite-shortcodes/column-shortcodes/
 *
 * title: Example of the Memberlite Row and Columns Shortcode.
 * layout: snippet
 * collection: memberlite
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/documentation/memberlite/memberlite-shortcodes/column-shortcodes/
 *
 */
?>

[row]
[col medium="4" class="bg_secondary"]

<span class="has-text-color has-white-color">1/3 column</span>

[/col]
[col medium="8" class="bg_primary"]

<span class="has-text-color has-white-color">2/3 column</span>

[/col]
[/row]

[row]
[col medium="6" class="bg_action"]

<span class="has-text-color has-white-color">1/2 column</span>

[/col]
[col medium="6" class="bg_primary"]

<span class="has-text-color has-white-color">1/2 column</span>

[/col]
[/row]

[row]
[col medium="6" medium_offset="3" class="bg_primary"]

<span class="has-text-color has-white-color">1/2 centered column</span>

[/col]
[/row]

[row]
[col medium="6" class="bg_primary"]

<span class="has-text-color has-white-color">1/2 column</span>

[/col]
[col medium="6" class="bg_primary"]

[row_row]
[col_col medium="4" class="bg_secondary"]

<span class="has-text-color has-white-color">Nested column</span>

[/col_col]
[col_col medium="8" class="bg_action"]

<span class="has-text-color has-white-color">Nested column</span>

[/col_col]
[/row_row]

[/col]
[/row]
