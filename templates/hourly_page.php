<?php
global $wpdb;
$user_id =  get_current_user_id();
$project_key =  get_user_meta( $user_id, 'project_key', true );
$filter_id = get_user_meta( $user_id, 'jira_filter', true );

$jira_first_order_date = jira_user_orders_data($user_id)['start_date'];
$jira_time = jira_return_user_db_values($user_id)['billable_rounded'];
$time_total = jira_return_user_db_values($user_id)['booked'];
$time_left = $time_total - $jira_time;
?>


<?php if($time_total){?>
<div class="woocommerce_account_subscriptions">

	<table class="shop_table shop_table_responsive my_account_orders">

		<div class="loading d-none"></div>
		<tbody>

		<tr class="progress-bar-sundsits" >

			<?php $time_count_total = 6000;
			$bar_width = $time_left / $time_count_total * 100;?>
			<td colspan="5">
				<div class="progressBar">
					<div class="progress-div" style="width: <?php echo $bar_width;?>%;<?php if($bar_width == 0) echo 'display:none;'?>"></div>
				</div>
				<div class="remaining-time"><?php echo $time_left;?> minutes left</div>
			</td>
		</tr>

		</tbody>

	</table>

</div>
	<script>
		var ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
	</script>
	<div class="more-button"><span data-user="<?php echo $user_id;?>">Zeige verbrauchte Zeiten</span></div>
<!-- Button trigger modal -->
<!--button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div-->
<?php } 
add_action('wp_footer', 'frontend_popup_to_footer'); 
function frontend_popup_to_footer() { ?>
     <!-- Modal -->
<div class="modal fade" id="userTimeModal" tabindex="-1" aria-labelledby="userTimeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>
<?php }
