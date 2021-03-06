<?php

/**
 * Show the forms tab
 *
 * @since      1.2.0
 *
 * @package    simple_contact_forms
 * @subpackage simple_contact_forms/admin/views
 */


// Create the fields table
$completions_table = new scf_Completions_Table();
$completions_table->form_id = 0;
$completions_table->prepare_items();

?>

<style type="text/css">
	.wp-list-table .column-col_completion_time { width: 15%; }
	.wp-list-table .column-col_completion_data { width: 50%; }
	.wp-list-table .column-col_completion_location { width: 25%; }
	.wp-list-table .column-col_completion_delete { width: 10%; }
</style>


<h3>Completions</h3>
<div style="float: left;">
	<a href="#" id="exportCSV">Export to CSV</a>
	<?php $completions_table->display();?>
</div>