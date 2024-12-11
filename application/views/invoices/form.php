<?php $this->load->view("partial/header"); ?>
<style type="text/css">
	.tooltip-container {
		position: relative;
		display: inline-block;
	}

	.tooltip-text {
	visibility: hidden;
	background-color: #000;
	color: #fff;
	text-align: center;
	padding: 5px;
	border-radius: 5px;
	position: absolute;
	z-index: 1;
	bottom: 100%;
	left: 50%;
	transform: translateX(-50%);
	white-space: nowrap;
	}

	.tooltip-container:hover .tooltip-text {
		visibility: visible;
	}
</style>
	
<?php
	$total_payments = 0;
	$all_invoice_payments = $this->Invoice->get_payments( $invoice_type, $invoice_id )->result_array(  );
	$have_paymet = empty( $all_invoice_payments ) ? false : true;
	if ( $this->session->flashdata('success') ):	?>
		<div class="alert alert-success" role="alert">
			<?php echo $this->session->flashdata('success'); ?>
		</div>
	<?php	elseif( $this->session->flashdata('error') ):	?>
		<div class="alert alert-success" role="alert">
			<?php echo $this->session->flashdata('error'); ?>
		</div>
	<?php	elseif( $this->session->flashdata('total_validate_not') ):	?>
		<div class="alert" style="color: #a94442;background-color:#f2dede;border-color:#d6e9c6;" role="alert">
			<?php echo lang( 'invoices_totals_not_equals' ); ?>
		</div>
	<?php	endif;
	
	$main_total = $invoice_info->main_total;
	$variable_total = $invoice_info->total;
	$plus = false;
	$minus = false;
	$equal = false;
	if( $main_total === $variable_total ) {
		$equal = true;
	}elseif( $main_total > $variable_total ) {
		$minus = true;
	}elseif( $main_total < $variable_total ) {
		$plus = true;
	}

	if( $have_paymet ){
		foreach($all_invoice_payments as $payment) {
			$total_payments += $payment['payment_amount'];
		}
	}
	$new_balance = $main_total - $total_payments;
	
	$is_complete = $invoice_info->invoice_id !== NULL && $invoice_info->invoice_id !== '';
	$styles_disable_action = "pointer-events: none;cursor: default;background: gray;";
	$disable_action = "style='$styles_disable_action'";

	$can_edit_due_date_invoce = ($this->Employee->has_module_action_permission('invoices', 'show_can_edit_due_date_invoce', $this->Employee->get_logged_in_employee_info()->person_id));
	$can_add_credit_notes = ($this->Employee->has_module_action_permission('invoices', 'show_can_create_credit_note_invoce', $this->Employee->get_logged_in_employee_info()->person_id));
	$can_pay_invoce = ($this->Employee->has_module_action_permission('invoices', 'show_can_pay_invoce', $this->Employee->get_logged_in_employee_info()->person_id));	
?>
<div class="panel panel-piluku invoice_body">
	<div class="panel-heading">
		<?php echo lang("invoices_basic_info"); ?>
		<span class="pull-right">
			<?php echo anchor("invoices/index/$invoice_type",'&lt;- Back To Invoices', array('class'=>'hidden-print')); ?>
		</span>
	</div>

	<div class="spinner" id="grid-loader" style="display:none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	<?php echo form_open("invoices/save/$invoice_type/$invoice_id",array('id'=>'invoice_save_form','class'=>'form-horizontal')); ?>
	
	<div class="panel-body">
		<div class="col-md-12">
			<div id="invoice_date_field" class="form-group">
				<?php echo form_label(lang('invoices_invoice_date').':', 'invoice_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<div class="input-group date" data-date="<?php echo $invoice_info->invoice_date ? date(get_date_format(), strtotime($invoice_info->invoice_date)) : ''; ?>" style="display:none;">
						<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'invoice_date',
							'id'	=>	'invoice_date',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->invoice_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->invoice_date)) : date(get_date_format())
						));?> 
					</div>
					<div class="input-group date">
						<span class="input-group-addon bg" style="pointer-events: none;cursor: default;background: gray"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'',
							'id'	=>	'',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->invoice_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->invoice_date)) : date(get_date_format())
						), $value="", $extra="disabled");?> 
					</div>
				</div>
			</div>
			
			<!-- <div id="invoice_date_field" class="form-group">
				<?php echo form_label(lang('invoices_po_'.$invoice_type).':', 'invoice_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-3">
					<div class="input-group date">
						<?php echo form_input(array(
							'name'	=>	"$invoice_type".'_po',
							'id'	=> 	"$invoice_type".'_po',
							'class'	=>	'form-control col-lg-2',
							'value' => 	$invoice_info->{"$invoice_type".'_po'},
						));?> 
						<span class="input-group-addon bg"><input type="submit" name="submitf" value="Fetch Detail" id="submitf" class="submit_button btn btn-sm btn-primary"></span>
					</div>
				</div>
			</div> -->
			
			
			<div class="form-group">
				<?php echo form_label(lang("invoices_$invoice_type"), '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php
					if ($invoice_id == -1)
					{
						echo form_input(array(
							'name'		=> 	"$invoice_type".'_id',
							'id'		=> 	"$invoice_type".'_id',
							'size'		=>	'10',
							'value' 	=> 	$invoice_info->{"$invoice_type".'_id'}));
					} else {
						echo form_input(array(
							'name'		=> "$invoice_type".'_name',
							'id'		=> 	"",
							'size'		=>	'10',
							'class' 	=> 	'form-control',
							'disabled' 	=> 	'disabled',
							'value' 	=> 	$invoice_info->person));
					?>
					<input type="hidden" name="<?php echo "$invoice_type".'_id';?>" value="<?php echo $invoice_info->{"$invoice_type".'_id'};?>">	
					<?php } ?>	
				</div>
			</div>
			
			<div class="form-group">
				<?php echo form_label(lang("invoices_terms"),'',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php
						echo form_dropdown('term_id', $terms, $invoice_info->term_id, 'class="form-control input_radius" id="term_id" style="display:none;"');
						echo form_dropdown('', $terms, $invoice_info->term_id, 'class="form-control input_radius" id="term_id_dummy" disabled');
					?>	
				</div>
			</div>
			
			<div id="due_date_field" class="form-group">
				<?php echo form_label(lang('invoices_due_date').':', 'due_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<div class="input-group date" data-date="<?php echo $invoice_info->due_date ? date(get_date_format(), strtotime($invoice_info->due_date)) : ''; ?>" style="<?php echo ( !$have_paymet ? ($can_edit_due_date_invoce ? '' : 'display:none') : 'display:none' ) ?>">
						<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'due_date',
							'id'	=>	'due_date',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->due_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->due_date)) : ''
						));?> 
					</div>
					<div class="input-group date" style="<?php echo (!$have_paymet ? (!$can_edit_due_date_invoce ? '' : 'display:none') : ''); ?>">
						<span class="input-group-addon bg" style="pointer-events: none;cursor: default;background: gray;"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'',
							'id'	=>	'due_date_dummy',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->due_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->due_date)) : ''
						), $value="", $extra="disabled" );?> 
					</div>
				</div>
			</div>		
					
			<div class="form-controls form-actions">	
				<ul class="list-inline pull-right">
					<li>
						<?php
							echo form_submit(array(
								'name'	=>	'submitf',
								'id'	=>	'submitf',
								'value'	=>	lang('common_save'),
								'class'	=>	'submit_button btn btn-primary',
								'style' => ($have_paymet ? $styles_disable_action : '')
							));
						?>
					</li>
				</ul>
			</div>
		</div>
		
		<?php
		
		if($invoice_info->invoice_id > 0)
		{
		?>
		<div class="row ">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="col-xs-6 col-sm-3 col-md-2 pull-right">
					<div class="panel panel-success"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_main_total');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($invoice_info->main_total)?></h3> </div> 
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 col-md-2 pull-right <?php echo !$equal ? 'tooltip-container' : '' ?>">
					<div class="panel <?php echo !$equal ? 'panel-danger' : 'panel-success' ?>"> 
						<div class="panel-heading"> 
							<h3 class="panel-title">
								<?php 
									echo lang('common_total');
									if( !$equal ) {
										echo "<span>*</span>";
									}
								?>
							</h3> 
						</div> 
						<div class="panel-body"> <h3>
							<?php 
								echo to_currency($invoice_info->total);
								if( !$equal ) {
									echo "<span>*</span>";
								}
							?>
						</h3> </div> 
						<span class="tooltip-text">
							<?php
								if( $plus ) {
									echo lang( 'invoices_balance_surplus' );
								} elseif( $minus ) {
									echo lang( 'invoices_lack_of_balance' );
								}
							?>
						</span>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 col-md-2 pull-right">
					<div class="panel panel-danger"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_balance');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($new_balance)?></h3> </div> 
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<a id="add_line_item" href="javascript:void(0);" class="btn btn-primary" style="<?php echo $have_paymet ? $styles_disable_action : '' ?>">
				<?php echo lang('invoices_add_invoice_line_item');?>
			</a>
			<?php
				if( $can_add_credit_notes && !$have_paymet ) {	?>
					<a id="add_credit_memo" href="javascript:void(0);" class="btn btn-primary">
						<?php echo lang('invoices_add_credit_memo');?>
					</a>
				<?php	}else {	?>
					<a id="#" href="#" class="btn btn-primary" <?php echo $disable_action; ?>>
						<?php echo lang('invoices_add_credit_memo');?>
					</a>
				<?php	}
			?>
		</div>
		
		<?php } ?>
		<?php
		if(isset($orders) && !empty($orders))
		{
			$type_prefix = $invoice_type == 'customer' ? 'sale' : 'receiving';
		?>
		<Br>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3><strong><?php echo lang('invoices_recent_unpaid_orders');?></strong></h3>
			</div>
			<div class="panel-body" style="padding:0px !important;">
				<div class="" id="invoice_details">
					<table class="table table-bordered">
						<tr class="payment_heading">
							<th><?php echo lang('common_id');?></th>
							<th><?php echo lang('common_time');?></</th>
							<th><?php echo lang('common_amount_due');?></th>
							<th><?php echo lang('common_comment');?></th>
							<th><?php echo lang('invoices_add_to_invoice');?></th>
						</tr>
				
						<?php foreach($orders as $order) { ?>
						<tr>
							<td><?php echo $order[$type_prefix.'_id'];?></td>
							<td><?php echo date(get_date_format().' '.get_time_format(),strtotime($order[$type_prefix.'_time']));?></td>
							<td><?php echo to_currency($order['payment_amount']);?></td>
							<td><?php echo $order['comment'] ? $order['comment'] : lang('common_none');?></td>
							<td>
								<?php if (!$this->Invoice->is_order_in_invoice($invoice_type,$order[$type_prefix.'_id'])) { ?>
									<a href="<?php echo site_url("invoices/add_to_invoice/$invoice_type/$invoice_id/".$order[$type_prefix.'_id']);?>" class="btn btn-primary"><?php echo lang('invoices_add_to_invoice');?></a>
								<?php } else { ?>
								<?php echo lang('invoices_already_invoiced');?>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>

		<?php } ?>
		<br />
		<!-- Load Invoice Details -->
		<?php $this->load->view('partial/invoices/details', array('details' => isset($details) ? $details : NULL,'can_edit' => TRUE,'type_prefix' => $type_prefix, 'have_paymet' => $have_paymet)); ?>
		<!-- Load Invoice Payments -->
		<?php $this->load->view('partial/invoices/payments', array('payments' => $payments, 'have_paymet' => $have_paymet));
		
		if($invoice_id > 0 && (float)$invoice_info->balance > 0)
		{
			if( $can_pay_invoce ) {
				echo anchor("invoices/pay/$invoice_type/$invoice_id", lang('common_pay'),array('class' => 'btn btn-primary pull-left'));
			}else {
				echo anchor("#", lang('common_pay'),array('class' => 'btn btn-primary pull-left', 'style'=>$styles_disable_action));
			}
		}
		?>
		
		
		
	</div> <!-- close pannel body -->
	<?php echo form_close(); ?>

	<div class="panel-body">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3><strong><?php echo lang('common_invoice_docs');?></strong></h3>
			</div>
			<div class="panel-body" style="padding:0px !important;">
				<table class="table table-bordered">
					<tr>
						<td><?php echo lang('common_invoice_cf1');?></td>
						<td>
							<?php 
								echo form_input(
									array(
										'name'=>"proof_invoice",
										'id'=>"proof_invoice",
										'type' => 'file',
										'class'=>"proof_of_invoice form-control",
										'style' => ($have_paymet ? 'pointer-events: none;cursor: default;opacity:.6' : '')
									),
								);
								if( !is_null($invoice_info->proof_of_invoice) && !empty($invoice_info->proof_of_invoice)){
									echo anchor('invoices/download_field_on_view/' . $invoice_info->proof_of_invoice, $this->Appfile->get_file_info($invoice_info->proof_of_invoice)->file_name, array('target' => '_blank'));
									echo form_open('invoices/update_file_on_invoice_view');
										echo form_hidden('invoice_id', $invoice_id);
										echo form_hidden('proof_of_invoice_id', "");
										echo form_hidden('for_delete', true);
										echo form_submit(array('name'=>'submit_cf', 'class'=>'btn btn-primary pull-left', 'style'=>($have_paymet ? 'pointer-events: none;cursor: default;background-color:gray;border-bottom-color: gray;' : '')), "Eliminar documento");
									echo form_close(); 
								}
							?>
						</td>
						<td>
							<?php 
								echo form_open('invoices/update_file_on_invoice_view');
									echo form_hidden('invoice_id', $invoice_id);
									echo form_hidden('proof_of_invoice_id', $invoice_info->proof_of_invoice ? $invoice_info->proof_of_invoice : "");
									echo form_hidden('for_delete', false);
									echo form_submit(array('name'=>'submit_cf', 'class'=>'btn btn-primary pull-left', 'style'=>($have_paymet ? 'pointer-events: none;cursor: default;background-color:gray;border-bottom-color: gray;' : '')), lang('common_invoice_update_cf'));
								echo form_close(); 
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<?php $this->load->view('partial/invoices/invoice_detail_modal', array('modal_id' => 'invoice-modal','action' => "invoices/add_to_invoice_manual/$invoice_type/$invoice_id", 'invoice_type' => $invoice_type,'invoice_id' => $invoice_id));?>
	<?php $this->load->view('partial/invoices/invoice_detail_modal', array('modal_id' => 'invoice-modal-memo','action' => "invoices/add_to_invoice_credit_memo/$invoice_type/$invoice_id", 'invoice_type' => $invoice_type,'invoice_id' => $invoice_id));?>
	
	<script type="text/javascript">
		$('.proof_of_invoice').change(function() {

			var formData = new FormData();
			formData.append('name', $(this).attr('name'));
			formData.append('value', $(this)[0].files[0]);

			$.ajax({
				url: '<?php echo site_url("invoices/save_file_on_invoice_view"); ?>',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					// Actualiza el valor del input con el ID del archivo guardado
					$('input[name="proof_of_invoice_id"]').val(response);
				}
			});
		});
		
		$(".delete-invoice-detail").click(function(e)
		{
			var $that = $(this);
			e.preventDefault();

			bootbox.confirm('Are you you sure you want to delete this invoice item?', function(result)
			{
				if (result)
				{
					window.location = $that.attr('href');
				}
			});
		});
		
		$("#add_line_item").click(function()
		{
			$("#invoice-modal").modal('show');				
		});
		
		$("#add_credit_memo").click(function()
		{
			$("#invoice-modal-memo").modal('show');				
		});
		
		
	    $('.xeditable').editable({
	    	validate: function(value) {
	            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
						return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
	            }
	        },
	    	success: function(response, newValue) {
			}
	    });
		
	    $('.xeditable').on('shown', function(e, editable) {

			$(this).closest('.table-responsive').css('overflow-x','hidden');

	    	editable.input.postrender = function() {
					//Set timeout needed when calling price_to_change.editable('show') (Not sure why)
					setTimeout(function() {
		         editable.input.$input.select();
				}, 200);
		    };
		});
		
		
		date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
		
		
		
		$("#<?php echo $invoice_type;?>_id").select2(
		{
			width : '100%',
			placeholder: <?php echo json_encode(lang('common_search')); ?>,
			ajax: {
				url: <?php echo json_encode(site_url("invoices/suggest_$invoice_type")); ?>,
				dataType: 'json',
			   data: function(term, page) 
				{
			      return {
			          'term': term
			      };
			    },
				results: function(data, page) {
					return {results: data};
				}
			},
			id: function (suggestion) { return suggestion.value },
			formatSelection: function(suggestion) {
				return suggestion.label;
			},
			formatResult: function(suggestion) {
				return suggestion.label;
			}
		});
		
		$("#term_id").change(function(e)
		{
			var url = '<?php echo site_url("invoices/get_term_default_due_date"); ?>'+'/'+$(this).val();
			$.getJSON(url,function(json)
			{	
				var term_default_due_date = json.term_default_due_date;
				$("#due_date").val(term_default_due_date);
				$("#due_date_dummy").val(term_default_due_date);
			
			});	
		});
		
		$("#<?php echo $invoice_type;?>_id").change(function(e)
		{
			var url = '<?php echo site_url("invoices/get_default_terms/".$invoice_type); ?>'+'/'+$(this).val();
			$.getJSON(url,function(json)
			{	
				var default_term_id = json.default_term_id;
				$("#term_id").val(default_term_id);
				$("#term_id").trigger('change');
				$("#term_id_dummy").val(default_term_id);
				$("#term_id_dummy").trigger('change');
			
			});	
		});
		$('#invoice_save_form').ajaxForm({
		success:function(response)
		{
			var response = JSON.parse(response);
			$('#grid-loader').hide();
			submitting = false;
		
			show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);

			if(response.reload==1 && response.success)
			{
				window.location.reload();
			}
			else if(response.redirect==1 && response.success)
			{ 
				window.location.href = '<?php echo site_url('invoices/index/'.$invoice_type); ?>';
			}
			else if(response.redirect==2 && response.success)
			{ 
				window.location.href = '<?php echo site_url('invoices/view/'.$invoice_type.'/'); ?>'+response.invoice_id;
			}

		}});
	</script>
<?php $this->load->view("partial/footer"); ?>