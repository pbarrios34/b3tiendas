<?php $this->load->view("partial/header"); ?>
<?php
	if( !is_null($export_to_excel) && $export_to_excel == true ){
		$rows = array();
		function reformat_array_keys($array) {
			$formatted_array = array();
		
			foreach ($array as $item) {
				$formatted_item = array_values($item);
				$formatted_array[] = $formatted_item;
			}
		
			return $formatted_array;
		}
		$rows[] = $headers;
		$title = 'Informe de ventas por categorías';
		foreach($report_data as $datarow){
			$rows[] = $datarow;
		}
		$sanitized_rows = reformat_array_keys( $rows );
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($sanitized_rows, strip_tags($title) . '.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), true);
		exit;
	}
?>
<style>
	.btn_hiden_columns{
		box-shadow: none;
		background-color: #f2f6f9;
		border: 1px solid #D7DCE5;
		border-radius: 3px;
		font-size: 14px;
		font-weight: 300;
		letter-spacing: 0.6px;
		color: #555;
		position: relative;
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 15px;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
	}
	.settings__container{
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		align-items: flex-start;
	}
	.settings__container>ul{
		list-style: none;
		display: flex;
		height: 0;
	}
	.settings__container>ul.hiden{
		opacity: 0;
	}
	.settings__container>ul.show{
		opacity: 1;
		height: auto;
	}
	.settings__container>ul li{
		min-width: 175px;
	}
	.div_setings{
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
	}
</style>
<?php
	$selected_filter = $this->input->post('items_expired_date_filter');
	$selected_date = $this->input->post('items_expired_date_container');
	$selected_date_init = $this->input->post('items_expired_init_date_name');
	$selected_date_end = $this->input->post('items_expired_end_date_name');
	$selected_customer_date = $this->input->post('items_expired_custom_date_name') ? true : false;
	$selected_supplier = $this->input->post('items_expired_report_supplier');
	$selected_item = $this->input->post('items_expired_report_item');

	$locations_keys = array_keys($array_locations);
	$selected_location = [];
	foreach ($locations_keys as $key) {
		if( $this->input->post('catagory_report_'.$key.'_location') ){
			$selected_location[] = $this->input->post('catagory_report_'.$key.'_location');
		}
	}
?>
<div class="row hidden-print">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<?php echo lang('reports_report_options'); ?>		
			</div>
			<div class="panel-body">
				<?php echo form_open('ExpiredItems/generate/',array('id'=>'items_expired_report_form','class'=>'form-horizontal')); ?>

					<div class="form-group">
						<?php 
							echo form_label(
								lang('reports_date_filter') . ':', 
								'items_expired_date_filter_id', 
								array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')
							); 
						?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php 
								echo form_dropdown(
									'items_expired_date_filter',
									array( '0' => "Fecha de expiración", '1' => "Fecha de confirmación" ), 
									$selected_filter ? $selected_filter : 0, 
									'id="items_expired_date_filter_id" class=""'
								); 
							?>
						</div>
					</div>

					<div class="form-group" <?php echo !$selected_customer_date ? '' : 'style="display:none;"';  ?>>
						<?php echo form_label(lang('reports_date_range') . ':', 'items_expired_date', array('class' => 'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								echo form_dropdown(
								'items_expired_date_container',
								$array_dates,
								$selected_date ? $selected_date : 0,
								'class="form-control" id="items_expired_date_id"'
							);
							?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_category_custom_range_date').':', 'items_expired_custom_date_container', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								echo	form_checkbox(array(
									'name' => 'items_expired_custom_date_name',
									'id' => 'items_expired_custom_date_id',
									'value' => 1,
									'checked' => $selected_customer_date ? true : false,
								));
								echo '<label for="items_expired_custom_date_id"><span></span></label>';
							?>
						</div>
					</div>

					<div class="form-group" <?php echo $selected_customer_date ? '' : 'style="display:none;"';  ?>>
						<?php echo form_label(lang('reports_category_initial_date').':', 'items_expired_init_date_container', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="input-group date">
							<span class="input-group-addon"><i class="ion-calendar"></i></span>
							<?php echo form_input(array(
								'name'=>'items_expired_init_date_name',
								'id'=>'items_expired_init_date_id',
								'class'=>'form-control datepicker',
								'value'=>$selected_date_init ? $selected_date_init : date(get_date_format()))
							);?> 
						</div>  
					</div>

					<div class="form-group" <?php echo $selected_customer_date ? '' : 'style="display:none;"';  ?>>
						<?php echo form_label(lang('reports_category_end_date').':', 'items_expired_end_date_container', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="input-group date">
							<span class="input-group-addon"><i class="ion-calendar"></i></span>
							<?php echo form_input(array(
								'name'=>'items_expired_end_date_name',
								'id'=>'items_expired_end_date_id',
								'class'=>'form-control datepicker',
								'value'=>$selected_date_end ? $selected_date_end : date(get_date_format()))
							);?> 
						</div>  
					</div>

					<div class="form-group">
						<?php 
							echo form_label(
								lang('reports_supplier') . ':', 
								'items_expired_report_supplier_id', 
								array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')
							); 
						?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php 
								echo form_dropdown(
									'items_expired_report_supplier',
									$array_suppliers, 
									$selected_supplier ? $selected_supplier : -1, 
									'id="items_expired_report_supplier_id" class=""'
								); 
							?>
						</div>
					</div>

					<div class="form-group">
						<?php 
							echo form_label(
								lang('reports_expired_items_item') . ':', 
								'items_expired_report_item_id', 
								array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')
							); 
						?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php 
								echo form_dropdown(
									'items_expired_report_item',
									$array_items, 
									$selected_item ? $selected_item : -1, 
									'id="items_expired_report_item_id" class=""'
								); 
							?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_category_export_to_excel') . ':', 'catagory_report_export_to_excel_id', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								echo	form_checkbox(array(
									'name' => 'catagory_report_export_to_excel',
									'id' => 'catagory_report_export_to_excel_id',
									'class' => 'catagory_report_export_to_excel_check',
									'checked' => 0,
									'value' => true,
								));
								echo '<label for="catagory_report_export_to_excel_id"><span></span></label>';
							?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_locations').':', 'catagory_report_all_location', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								echo	form_checkbox(array(
									'name' => 'catagory_report_all_location',
									'id' => 'catagory_report_all_location_id',
									'value' => 1,
									'checked' => $person_info->override_price_adjustments,
								));
								echo '<label for="catagory_report_all_location_id"><span></span><b>Seleccionar todas</b></label>';
							?>
							<?php
							foreach ($array_locations as $key => $value) {
								echo	form_checkbox(array(
									'name' => 'catagory_report_'.$key.'_location',
									'id' => 'catagory_report_'.$key.'_location_id',
									'class' => 'items_expired_location_check',
									'value' => $key,
									'checked' => !empty( $selected_location ) ? ( in_array($key, $selected_location) ? true : false ) : (( $key == $this->Employee->get_logged_in_employee_current_location_id() ) ? true : false),
								));
								echo '<label for="catagory_report_'.$key.'_location_id"><span></span>'.$value.'</label>';
							}
							echo form_checkbox(array(
								'name' => 'catagory_report_array_location',
								'id' => 'catagory_report_array_location_id',
								'class' => 'catagory_report_array_location',
								'value' => '',
							));
							?>
							
						</div>
					</div>
					
					<div class="form-actions pull-right">
						<?php
						
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>"Generar el reporte",
							'class'=>'submit_button floating-button btn btn-lg btn-primary')
						);
						?>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<?php echo lang('reports_report_options'); ?>		
			</div>
			<div class="panel-body">
				<div class="settings__container">
					<div class="div_setings">
						<a href="#" class="btn_hiden_columns"><i class="ion-gear-a"></i></a>
						<div class="expor_to_excel">
						</div>
					</div>
					<ul class="ul_setings hiden">
						<?php
							foreach ($headers as $key => $value) {	
								echo "<li>";
									echo	form_checkbox(array(
										'name' => 'catagory_report_'.$key.'_header_name',
										'id' => 'catagory_report_'.$key.'_header_id',
										'class' => 'items_expired_colums_check',
										'value' => $key,
										'checked' => 1
									));
									echo '<label for="catagory_report_'.$key.'_header_id"><span></span>'.$value.'</label>';
								echo "</li>";
							}
						?>
					</ul>
				</div>
				<table class="category-report__container table table-bordered table-striped table-reports tablesorter stacktable large-only">
					<thead>
						<tr>
							<?php
								foreach ($headers as $key => $value) {
									echo "<th class='colsho ".$key."'>".$value."</th>";
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach ($report_data as $item) {
								echo "<tr>";
								foreach ($item as $key => $value) {
									if( $key == 'quantity_received' ){
										echo "<td class='colsho ".$key."'>".to_currency_no_money( $value )."</td>";
									}else{
										echo "<td class='colsho ".$key."'>".$value."</td>";
									}
								}
								echo "</tr>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="col-12 hidden-print">
	<div class="pull-left-btn">
		<a href="#" id="print_items_expired" class="btn btn-primary btn-lg">Imprimir reporte</a>
	</div>
</div>

<script type='text/javascript'>
	<?php $this->load->view("partial/common_js"); ?>
	$(document).ready(
		function(){
			let items_expired_date_compare = document.querySelector('#items_expired_custom_date_id');
			let items_expired_all_locations = document.querySelector('#catagory_report_all_location_id');
			let show_hiden_report_settings = document.querySelector('.btn_hiden_columns');
			let columns_report_settings = document.querySelectorAll('.items_expired_colums_check');
			let category_without_quantity = document.querySelectorAll('.without_quantity');

			let print_items_expired = document.querySelector('#print_items_expired');

			items_expired_date_compare.addEventListener('change', function(e){
				let custom_date = e.target.checked;
				let range_date = document.querySelector('#items_expired_date_id').parentElement.parentElement;
				let initial_date = document.querySelector('#items_expired_init_date_id').parentElement.parentElement;
				let ending_date = document.querySelector('#items_expired_end_date_id').parentElement.parentElement;

				if( custom_date ){
					range_date.style.display = 'none';
					initial_date.style.display = 'block';
					ending_date.style.display = 'block';
				}else{
					range_date.style.display = 'block';
					initial_date.style.display = 'none';
					ending_date.style.display = 'none';
				}
			});
			items_expired_all_locations.addEventListener('change', function(e){
				let input = e.target;
				let items_expired_all_locations = document.querySelectorAll('.items_expired_location_check');
				if(input.checked){
					items_expired_all_locations.forEach(function(element){
						element.checked = true;
					});
				}else{
					items_expired_all_locations.forEach(function(element){
						element.checked = false;
					});
				}
			});
			print_items_expired.addEventListener('click', function(e){
				e.preventDefault();
				window.print();
			});

			show_hiden_report_settings.addEventListener( 'click', function(e){
				e.preventDefault();
				let ul_settings = document.querySelector('.ul_setings');
				if( ul_settings.classList.contains('show') ){
					ul_settings.classList.remove('show');
					ul_settings.classList.add('hiden');
				}else{
					ul_settings.classList.remove('hiden');
					ul_settings.classList.add('show');
				}
			});

			columns_report_settings.forEach(function(element){
				element.addEventListener('change', function(e){
					let input = e.target;
					let columns = document.querySelectorAll('.category-report__container .'+input.value);
					if(input.checked){
						columns.forEach(function(element){
							element.style.display = 'table-cell';
						});
					}else{
						columns.forEach(function(element){
							element.style.display = 'none';
						});
					}
				});
			});

			date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);

			$("#items_expired_report_supplier_id").select2();
			$("#items_expired_report_item_id").select2();
			$("#items_expired_date_filter_id").select2();
		}
	)
</script>
<?php $this->load->view("partial/footer"); ?>