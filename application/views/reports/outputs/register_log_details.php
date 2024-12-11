<style>
	#register_log_details > div ul:last-child .quantity_before_cut{
		display: none;
	}
</style>
<?php
	$registerlog_id = $this->uri->segment(3);
	$cash_register_id = $register_log[0]->register_id;
?>

<div class="row">

	<div class="text-center">
		<button class="btn btn-primary text-white hidden-print" id="print_button" onclick="window.print();"> <?php echo lang('common_print'); ?> </button>
		
		<?php if ($this->Employee->has_module_action_permission('reports', 'view_sales', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
			<a class="btn btn-warning"  href="<?php echo site_url("bacintegration/batch");?>" target="_blank">Hacer Cierre en BAC</a>
		<?php } ?>
		
		<?php if($key) { ?>
			<a href="<?php echo site_url("reports/delete_saved_report/".$key);?>" class="btn btn-primary text-white hidden-print delete_saved_report pull-right"> <?php echo lang('reports_unsave_report'); ?></a>	
		<?php } else { ?>
			<button class="btn btn-primary text-white hidden-print save_report_button pull-right" data-message="<?php echo H(lang('reports_enter_report_name'));?>"> <?php echo lang('reports_save_report'); ?></button>
		<?php } ?>
	</div>
	<br />

	<div class="col-md-12">			
		<?php
		
		if($register_log[0]->shift_end=='0000-00-00 00:00:00')
		{
			$shift_end=lang('reports_register_log_open');	
		}
		else
		{
			$shift_end = date(get_date_format(). ' '.get_time_format(), strtotime($register_log[0]->shift_end));
		}
		?>
		
		<div class="row" id="register_log_details">
			<div class="col-lg-4 col-md-12">
				
			<ul class="list-group">
				<li class="list-group-item hidden-print"><?php echo lang('reports_register_log_id'). ': <strong class="pull-right">'. $register_log[0]->register_log_id; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_register_log_id'). ': <strong class="pull-right">'. $register_log[0]->register_log_id; ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('common_register_name'). ': <strong class="pull-right">'. $register_log[0]->register_name; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('common_register_name'). ': <strong class="pull-right">'. $register_log[0]->register_name; ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('module_location'). ': <strong class="pull-right">'. $register_log[0]->location; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('module_location'). ': <strong class="pull-right">'. $register_log[0]->location; ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('reports_employee_open'). ': <strong class="pull-right">'. $register_log[0]->open_first_name.' '.$register_log[0]->open_last_name; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_employee_open'). ': <strong class="pull-right">'. $register_log[0]->open_first_name.' '.$register_log[0]->open_last_name; ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('reports_close_employee'). ': <strong class="pull-right">'.$register_log[0]->close_first_name.' '.$register_log[0]->close_last_name;  ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_close_employee'). ': <strong class="pull-right">'.$register_log[0]->close_first_name.' '.$register_log[0]->close_last_name;  ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('reports_shift_start'). ': <strong class="pull-right">'. date(get_date_format(). ' '.get_time_format(), strtotime($register_log[0]->shift_start)); ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_shift_start'). ': <strong class="pull-right">'. date(get_date_format(). ' '.get_time_format(), strtotime($register_log[0]->shift_start)); ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('reports_shift_end'). ': <strong class="pull-right">'. $shift_end; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_shift_end'). ': <strong class="pull-right">'. $shift_end; ?></strong></li>
				<li class="list-group-item hidden-print"><?php echo lang('reports_notes'). ': <strong class="pull-right">'. $register_log[0]->notes; ?></strong></li>
				<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_notes'). ': <strong class="pull-right">'. $register_log[0]->notes; ?></strong></li>
			</ul>
			
					<?php foreach ($register_log as $register_log_row) {?>
				<ul class="list-group">

						<li class="list-group-item quantity_before_cut" style="background:#489ee7;color:#fff;border:1px solid #ddd !important;"><strong><?php echo lang('reports_quantity_before_cut').'</strong>: <strong id="previous_register_close_amount__container" class="pull-right">'; ?></strong></li>

						<?php /* if($this->Employee->has_module_action_permission('reports', 'cash_register_limit_view', $this->Employee->get_logged_in_employee_info()->person_id)) { */?>
						<?php if( false ) {?>

						
						
						<?php } else {?>

							<li class="list-group-item hidden-print"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('common_open_amount'). ': <strong class="pull-right">'. to_currency($register_log_row->open_amount); ?></strong></li>
							<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('common_open_amount'). ': <strong class="pull-right">'. to_currency($register_log_row->open_amount); ?></strong></li>
					
						<?php }?>
						
						
						<?php if(true) {?>

						
						
						<?php } else {?>

							<?php if ($register_log_row->payment_type == 'common_cash') {?>
								<?php foreach($this->Register->get_cash_count_details($register_log_row->register_log_id,'open') as $denom=>$count) { ?>
									<li class="list-group-item hidden-print"><?php echo $denom; ?>:  <strong class="pull-right"><?php echo to_quantity($count); ?></strong> </span></li>
									<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo $denom; ?>:  <strong class="pull-right"><?php echo to_quantity($count); ?></strong> </span></li>
								<?php } ?>
							<?php } ?>

						<?php }?>

						
						<li class="list-group-item hidden-print"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('reports_close_amount'). ': <strong class="pull-right">'. to_currency($register_log_row->close_amount); ?></strong></li>
						<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('reports_close_amount'). ': <strong class="pull-right">'. to_currency($register_log_row->close_amount); ?></strong></li>
					  

						<?php if(true) {?>

							
						
						<?php } else {?>

							<?php if ($register_log_row->payment_type == 'common_cash') {?>
								<?php foreach($this->Register->get_cash_count_details($register_log_row->register_log_id,'close') as $denom=>$count) { ?>
									<li class="list-group-item hidden-print"><?php echo $denom; ?>:  <strong class="pull-right"><?php echo to_quantity($count); ?></strong> </span></li>
									<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo $denom; ?>:  <strong class="pull-right"><?php echo to_quantity($count); ?></strong> </span></li>
								<?php } ?>						
							<?php } ?>

						<?php }?>
						


						<?php if($this->Employee->has_module_action_permission('reports', 'cash_register_limit_view', $this->Employee->get_logged_in_employee_info()->person_id)) {?>

						
						<?php } else {?>

							<?php if (false) {?>
								<li class="list-group-item hidden-print"><?php echo lang('sales_amount_of_cash_to_desposit_in_bank'); ?>:  <strong class="pull-right"><?php echo to_currency((float)($register_log_row->close_amount)-(float)($this->config->item('amount_of_cash_to_be_left_in_drawer_at_closing'))); ?></strong> </span></li>
								<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('sales_amount_of_cash_to_desposit_in_bank'); ?>:  <strong class="pull-right"><?php echo to_currency((float)($register_log_row->close_amount)-(float)($this->config->item('amount_of_cash_to_be_left_in_drawer_at_closing'))); ?></strong> </span></li>
							<?php } ?>
							
						<?php }?>	
							
						
							<li class="list-group-item hidden-print"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('common_sales'). ': <strong class="pull-right">'. to_currency($register_log_row->payment_sales_amount); ?></strong></li>
							<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo (strpos($register_log_row->payment_type,'common_') !== FALSE ? lang($register_log_row->payment_type) : $register_log_row->payment_type).' '.lang('common_sales'). ': <strong class="pull-right">'. to_currency($register_log_row->payment_sales_amount); ?></strong></li>
							
						<?php if($this->Employee->has_module_action_permission('reports', 'cash_register_limit_view', $this->Employee->get_logged_in_employee_info()->person_id)) {?>

						
						<?php }?>

							<li class="list-group-item hidden-print"><?php echo lang('reports_difference'). ': <strong class="pull-right">'. to_currency($register_log_row->difference); ?></strong></li>
							<li class="list-group-item visible-print" style="color:#000 !important;border:1px solid #ddd !important;"><?php echo lang('reports_difference'). ': <strong class="pull-right">'. to_currency($register_log_row->difference); ?></strong></li>
						
				</ul>
						<?php } ?>
			</div>

			<div class="col-lg-8  col-md-12">
				<div class="panel panel-piluku">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?php echo lang('reports_adds_and_subs');?>
						</h3>
					</div>
					<div class="panel-body nopadding table_holder  table-responsive" >
						<table class="table  table-hover table-reports table-bordered">
							<thead>
								<tr>
									<th><?php echo lang('reports_date')?></th>
									<th><?php echo lang('reports_employee')?></th>
									<th><?php echo lang('common_payment')?></th>
									<th><?php echo lang('common_amount')?></th>
									<th><?php echo lang('reports_notes')?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if ($register_log_details != FALSE)
								{
									foreach($register_log_details as $row) {?>
									<tr>
										<td><?php echo date(get_date_format(). ' '.get_time_format(), strtotime($row['date']));?></td>
										<td><?php echo $row['employee_name'];?></td>
										<td><?php echo lang($row['payment_type']);?></td>
										<td><?php echo to_currency($row['amount']);?></td>
										<td><?php echo $row['note'];?></td>
									</tr>
									<?php } 	
								}
								?>
							</tbody>
						</table>
					</div>		
				</div>
			</div>
			<!-- Col-md-6 -->

		</div> 
		<!-- row -->

	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		$.post( '<?php echo site_url("reports/get_previous_register_close_amount") ?>', {
			register_id: <?php echo $cash_register_id; ?>,
			register_log_id: <?php echo $registerlog_id; ?>
		}, function( response ) {
			let previous_register_close_amount = JSON.parse( response );
			document.querySelector( '#previous_register_close_amount__container' ).innerHTML = previous_register_close_amount;
		} );
	} );
</script>