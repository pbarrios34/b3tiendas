<?php $this->load->view("partial/header"); ?>
<?php $this->load->view('partial/categories/expense_category_modal', array('categories' => $categories));?>
<?php
	$is_complete = $expense_info->id !== NULL && $expense_info->id !== '';
	$styles_disable_action = "pointer-events: none;cursor: default;color: gray;";
	$disable_action = "style='$styles_disable_action'";

	$current_register_id = $this->Employee->get_logged_in_employee_current_register_id(  );
	$register_info_by_register_id = $this->Register->get_info( $current_register_id );

	$cash_register_is_open = $this->Register->is_register_log_open(  );
?>
<div class="col-12 font-pt-sans visible-print" style="width:95%;margin:0 auto;">
	<ul class="list-unstyled invoice-address text-center" style="margin-bottom:2px;">
		<?php 
		$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
		$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');
		$tax_id = ($tax_id = $this->Location->get_info_for_key('tax_id', isset($override_location_id) ? $override_location_id : FALSE)) ? $tax_id : $this->config->item('tax_id');
		$website = ($website = $this->Location->get_info_for_key('website', isset($override_location_id) ? $override_location_id : FALSE)) ? $website : $this->config->item('website');
		
		if ($company_logo) { ?>

			<?php
			if (!(isset($standalone) && $standalone)) {
			?>

				<li class="invoice-logo">
					<?php echo img(array('src' => secure_app_file_url($company_logo))); ?>
				</li>
			<?php } ?>
		<?php } ?>

		<?php if ($this->Location->count_all() > 1) { ?>
			<li class="company-title"><?php echo H($company); ?></li>
			<?php if(!$this->config->item('hide_location_name_on_receipt')){ ?>
				<li><?php echo H($this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
			<?php } ?>
		<?php } else {
		?>
			<li class="company-title"><?php echo H($company); ?></li>
		<?php
		}
		?>

		<?php
		if ($tax_id) {
		?>
			<li class="tax-id-title"><?php echo lang('common_tax_id') . ': ' . H($tax_id); ?></li>
		<?php
		}
		?>

		<li class="nl2br"><?php echo H($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
		<li><?php echo H($this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
		<?php if ($website) { ?>
			<li><?php echo H($website); ?></li>
		<?php } ?>
	</ul>
	<section class="expenses__description">
		<div class="__item">
			<p><?php echo lang( 'invoices_invoice_date' ).": "; ?></p>
			<p><?php echo $expense_info->expense_date ? date( get_date_format(), strtotime( $expense_info->expense_date ) ) : date( get_date_format() ); ?></p>
		</div>
		<div class="__item">
			<p><?php echo lang('expenses_recipient_name').": "; ?></p>
			<p><?php echo $this->Employee->get_info( $expense_info->employee_id )->username; ?></p>
		</div>
		<div class="__item">
			<p><?php echo form_label(lang('common_approved_by').': '); ?></p>
			<p><?php echo $this->Employee->get_info( $expense_info->approved_employee_id )->username; ?></p>
		</div>
	</section>
	<h5 style="font-family: 'Helvetica Neue', 'Nunito', sans-serif;text-align:center;"><?php echo lang("expenses_basic_information"); ?></h5>

	<table class="form-to-print">
		<tr>
			<th><?php echo form_label(lang('expenses_amount')); ?></th>
			<td><?php echo $expense_info->expense_amount? to_currency_no_money($expense_info->expense_amount) : ''; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('common_payment').': '); ?></th>
			<td><?php echo $expense_info->expense_payment_type; ?></td>
		</tr>	
		<tr>
			<th><?php echo form_label(lang('common_tax').': '); ?></th>
			<td><?php echo $expense_info->expense_tax? to_currency_no_money($expense_info->expense_tax) : to_currency_no_money(0); ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('expenses_id_order').': '); ?></th>
			<td><?php echo $expense_info->expense_description; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('expenses_supplier_title').': '); ?></th>
			<td><?php echo $expense_info->expense_type; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('common_category').': '); ?></th>
			<td><?php echo $this->Expense_category->get_info( $expense_info->category_id )->name; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('common_reason').': '); ?></th>
			<td><?php echo $expense_info->expense_reason; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('common_expenses_note').': '); ?></th>
			<td><?php echo $expense_info->expense_note; ?></td>
		</tr>
		<tr>
			<th><?php echo form_label(lang('common_remove_cash_from_register').': '); ?></th>
			<td><?php echo ( empty( $this->Register->get_info($expense_info->cash_register_id)->register_id ) ? lang( 'common_none' ) : $this->Register->get_info($expense_info->cash_register_id)->name ); ?></td>
		</tr>
	</table>
	<style>
		.form-to-print {
    		font-family: 'Helvetica Neue', 'Nunito', sans-serif;
			border-collapse: collapse;
			width: 100%;
		}
		.form-to-print td, .form-to-print th {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
		}
		.form-to-print tr td label {
			font-weight: normal;
		}
		.form-to-print tr th label {
			font-weight: bold;
		}
		.expenses__description .__item {
    		font-family: 'Helvetica Neue', 'Nunito', sans-serif;
			display: flex;
		}
		.expenses__description .__item p:first-of-type {
			font-weight: bold;
			margin-right: 15px;
		}
	</style>
</div>
<div class="row hidden-print" id="form">
	
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		 <?php echo form_open('expenses/save/'.$expense_info->id,array('id'=>'expenses_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading hidden-print" style="display:flex;justify-content: space-between;align-items: center;">
				<h3 class="panel-title">
					<i class="ion-edit"></i> <?php if(!$expense_info->id) { echo lang('expenses_new'); } else { echo lang('expenses_update'); } ?>
					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
				<a class="btn btn-primary btn-lg" onClick="window.print()"> <?php echo lang('common_print', '', array(), TRUE); ?> </a>	 
            </div>
			<div class="panel-body">
			<h5><?php echo lang("expenses_basic_information"); ?></h5>
				

				<div class="form-group p-lr-15">
					<?php echo form_label(lang('expenses_date').':', 'expenses_date_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="input-group date">
						<span class="input-group-addon" <?php echo $is_complete ? $disable_action : ""; ?>><i class="ion-calendar"></i></span>
						<?php echo form_input(array(
							'name'=>'expenses_date',
							'id'=>'expenses_date_input',
							'class'=>'form-control form-inps datepicker',
							'value'=>$expense_info->expense_date ? date(get_date_format(), strtotime($expense_info->expense_date)) : date(get_date_format())
						), $value="", $extra=( $is_complete ? $disable_action : "" ) );?> 
					</div>  
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('expenses_amount').':', 'expenses_amount_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'expenses_amount',
							'id'=>( $is_complete ? "" : "expenses_amount_input" ),
							'value'=>$expense_info->expense_amount? to_currency_no_money($expense_info->expense_amount) : ''
						), $value="", $extra=( $is_complete ? $disable_action : "" ) );?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_payment').':', 'expense_payment_type_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php 
							echo form_dropdown('expense_payment_type', $payment_types,$expense_info->expense_payment_type,'class="form-control" '.( $is_complete ? $disable_action : "" ));
						?>
					</div>
				</div>
				
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_tax').':', 'expenses_tax_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>"expenses_tax" ,
							'id'=>"expenses_tax_input",
							'value'=>$expense_info->expense_tax? to_currency_no_money($expense_info->expense_tax) : to_currency_no_money(0)
						), $value="", $extra=( $is_complete ? $disable_action : "" ) );?>
					</div>
				</div>
				
				
				<div class="form-group">
				<?php echo form_label(lang('expenses_id_order').':', 'expenses_description_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
					<?php echo form_input(array(
						'class'=>'form-control form-inps',
						'name'=>"expenses_description",
						'id'=>"expenses_description_input",
						'value'=>$expense_info->expense_description ? $expense_info->expense_description : ''
					), $value="", $extra=( $is_complete ? $disable_action : "" ) );?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('expenses_supplier_title').':', 'expenses_type_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>"expenses_type" ,
							'id'=>"expenses_type_input",
							'value'=>$expense_info->expense_type
						), $value="", $extra=( $is_complete ? $disable_action : "" ) );?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_reason').':', 'expenses_reason_input', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>"expense_reason",
							'id'=>"expenses_reason_input",
							'value'=>$expense_info->expense_reason
						), $value="", $extra=( $is_complete ? $disable_action : "" ) );?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_category').':', 'category_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php 
							if( $is_complete ) {
								echo form_dropdown('category_id', $categories,$expense_info->category_id, 'class="form-control form-inps" id ="category_id" '.( $is_complete ? "disabled" : "" ));
							}else {
								echo form_dropdown('category_id', $categories,$expense_info->category_id, 'class="form-control form-inps" id ="category_id"');
								if ($this->Employee->has_module_action_permission('expenses', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
									<div>
										<a href="javascript:void(0);" id="add_category"><?php echo lang('common_add_category'); ?></a>&nbsp;|&nbsp;<?php echo anchor("expenses/manage_categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
									</div>
								<?php }
							}
						?>	
					</div>
				</div>
			
				<div class="form-group">
					<?php echo form_label(lang('expenses_recipient_name').':', 'employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php 
							echo form_dropdown(
								'employee_id',
								array( 
									$logged_in_employee_id=>$is_complete ? $this->Employee->get_info( $expense_info->employee_id )->username : $this->Employee->get_info( $logged_in_employee_id )->username
								),
								$expense_info->employee_id ? $expense_info->employee_id : $logged_in_employee_id , 
								'id="employee_id" class=""'.$disable_action
							);
						?>
					</div>
				</div>


				<div class="form-group">
					<?php echo form_label(lang('common_approved_by').':', 'approved_employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php 
							echo form_dropdown(
								'approved_employee_id',
								array( 
									$logged_in_employee_id=>$is_complete ? $this->Employee->get_info($expense_info->approved_employee_id)->username : $this->Employee->get_info( $logged_in_employee_id )->username
								),
								$expense_info->employee_id ? $expense_info->approved_employee_id : $logged_in_employee_id , 
								'id="approved_employee_id" class="" '.$disable_action
							);
						?>
					</div>
				</div>
    
				<div class="form-group">
					<?php echo form_label(lang('common_expenses_note').':', 'expenses_note_input', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_textarea(array(
							'class'=>'form-control text-area',
							'name'=>"expenses_note" ,
							'id'=>"expenses_note_input",
							'rows'=>'5',
							'cols'=>'17',
							'value'=>$expense_info->expense_note
						), $value="", $extra=( $is_complete ? $disable_action : "" ));?>
					</div>
				</div>

				<div class="form-group hidden-print">
					<?php echo form_label(lang('common_upload_images').':', 'expense_image_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<ul class="list-unstyled avatar-list">
							<li>
								<input type="file" name="expense_image_id" id="expense_image_id" class="filestyle" accept=".png,.jpg,.jpeg,.gif" <?php echo ( $is_complete ? "disabled" : "" ) ?>>&nbsp;
								<?php
									if( $is_complete ) {	?>
										<style>
											#expenses_form .form-group .avatar-list .group-span-filestyle label.btn-file-upload {
												pointer-events: none;
												cursor: default;
												background: gray;
											}
										</style>
									<?php	}
								?>
							</li>
							<li>
								<?php echo $expense_info->expense_image_id ? '<div id="avatar">'.img(array('style' => 'width: 60%','src' => cacheable_app_file_url($expense_info->expense_image_id),'class'=>'img-polaroid img-polaroid-s')).'</div>' : '<div id="avatar">'.img(array('style' => 'width: 20%','src' => base_url().'assets/img/empty.png','class'=>'img-polaroid','id'=>'image_empty')).'</div>'; ?>
							</li>		
						</ul>
					</div>
				</div>

				<div class="form-group">
				<?php echo form_label(lang('common_remove_cash_from_register').':', 'cash_register_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php 
							$options_cash_register = array( 
								$current_register_id=>
									$cash_register_is_open
									?
									( $is_complete ? ( empty( $this->Register->get_info($expense_info->cash_register_id)->register_id ) ? lang( 'common_none' ) : $this->Register->get_info($expense_info->cash_register_id)->name ) : $register_info_by_register_id->name )
									:
									( $is_complete ? ( empty( $this->Register->get_info($expense_info->cash_register_id)->register_id ) ? lang( 'common_none' ) : $this->Register->get_info($expense_info->cash_register_id)->name ) : lang( 'require_open_cash_register' ) )
							);
							if( $cash_register_is_open && !$is_complete ) {
								$options_cash_register = array( strtolower(lang( 'common_none' )) => lang( 'common_none' ) ) + $options_cash_register;
							}
							echo form_dropdown(
								'cash_register_id', 
								$options_cash_register, 
								$current_register_id,
								'id="cash_register_id" class="" '. ($is_complete ? $disable_action : '') 
							);
						?>
					</div>
				</div>

				<?php if( $expense_info->expense_image_id && !$is_complete) {  ?>

				<div class="form-group">
					<?php echo form_label(lang('common_del_image').':', 'del_image',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_checkbox(array(
						'name'=>'del_image',
						'id'=>'del_image',
						'class'=>'delete-checkbox', 
						'value'=>1
					));
					echo '<label for="del_image"><span></span></label> ';
					?>
					</div>
				</div>

				<?php }  ?>

				<?php
				//Only allow removal from register for NEW expenses
				if ($this->config->item('track_payment_types') && !$expense_info->id)
				{
				?>	
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php if ($this->Employee->has_module_action_permission('sales', 'add_remove_amounts_from_cash_drawer', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
							<?php echo anchor_popup(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
						<?php } ?>
					</div>
				<?php } ?>

				<div class="panel panel-piluku hidden-print">
					<div class="panel-heading">
						<h3 class="panel-title">
							<i class="ion-folder"></i> 
							<?php echo lang("common_files"); ?>
						</h3>
					</div>
		
					<?php if (count($files)) {?>
					<ul class="list-group">
						<?php foreach($files as $file){?>
						<li class="list-group-item permission-action-item">
							<?php echo anchor($controller_name.'/delete_file/'.$file->file_id,'<i class="icon ion-android-cancel text-danger" style="font-size: 120%;'.($is_complete ? 'color:gray !important;' : '' ).'"></i>', array('style' => ($is_complete ? $styles_disable_action : '' )));?>	
							<?php echo anchor($controller_name.'/download/'.$file->file_id,$file->file_name,array('target' => '_blank'));?>
						</li>
						<?php } ?>
					</ul>
					<?php } ?>
					<h4 style="padding: 20px;"><?php echo lang('common_add_files');?></h4>
					<?php for($k=1;$k<=5;$k++) { ?>
						<div class="form-group"  style="padding-left: 10px;">
				    	<?php echo form_label(lang('common_file').' '.$k.':', 'files_'.$k,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ', 'style'=>( $is_complete ? $styles_disable_action : "" ))); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
						      	<div class="file-upload">
								<input type="file" name="files[]" id="files_<?php echo $k; ?>" <?php echo ( $is_complete ? $disable_action : "" ) ?>>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>

			<?php echo form_hidden('redirect', $redirect_code); ?>
			
			<?php
				if( !$is_complete ) {	?>
					<div class="form-actions pull-right">
						<?php
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_save'),
								'class'=>'btn btn-primary btn-lg submit_button floating-button btn-large hidden-print',
								'style'=> !$is_complete ? ($cash_register_is_open ? '' : 'pointer-events: none;cursor: default;background: gray;') : ''
							) );
						?>	 
					</div>
				<?php	}
			?>

		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
</div>

<script type='text/javascript'>
<?php $this->load->view("partial/common_js"); ?>
var submitting = false;
//validation and submit handling
$(document).ready(function()
{
		$('#category_id').selectize({
			create: true,
			render: {
				item: function(item, escape) {
						var item = '<div class="item">'+ escape($('<div>').html(item.text).text()) +'</div>';
						return item;
				},
				option: function(item, escape) {
						var option = '<div class="option">'+ escape($('<div>').html(item.text).text()) +'</div>';
						return option;
				},
				option_create: function(data, escape) {
						var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
					return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
				}
			}
		});
	        	
        $('#expenses_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
			$('#grid-loader').show();
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			error: function(data ) { 
				console.log(data); 
			},
			success:function(response)
			{
				$('#grid-loader').hide();
				submitting = false;
				
				show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
				
				if(response.redirect==1 && response.success)
				{ 
					$.post('<?php echo site_url("expenses");?>', {expense: response.id}, function()
					{
						window.location.href = '<?php echo site_url('expenses'); ?>'
					});					
				}
				if(response.redirect==2 && response.success)
				{ 
					window.location.href = '<?php echo site_url('expenses'); ?>'
				}

			},
			
			<?php if(!$expense_info->id) { ?>
			resetForm: true,
			<?php } ?>
			dataType:'json'
		});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},
		rules: 
		{
        expenses_type: "required",
        expenses_description: "required",
        expenses_date: "required",
		expenses_amount: {
			required:true,
			number:true				
		},
		expenses_tax:"number",
		expenses_description:{
			required:true,
			number:true				
		},
        expenses_recipient_name: "required",
        category_id: "required"
		},
		messages: 
		{
     		expenses_type: <?php echo json_encode(lang('expenses_type_required')); ?>,
     		expenses_description: <?php echo json_encode(lang('expenses_description_required')); ?>,
     		expenses_date: <?php echo json_encode(lang('expenses_date_required')); ?>,
     		expenses_amount: 
			{
				required: <?php echo json_encode(lang('expenses_amount_required')); ?>,
				number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
			},
			expenses_tax: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>,
			expenses_description: 			{
				required: <?php echo json_encode(lang('expenses_amount_required')); ?>,
				number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
			},
     		expenses_recipient_name: <?php echo json_encode(lang('expenses_recipient_name_required')); ?>,
     		category_id: <?php echo json_encode(lang('common_category_required')); ?>
		}
	});
});

date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);

$("#employee_id").select2();
$("#approved_employee_id").select2();
$("#cash_register_id").select2();

// added for expense category

$(document).on('click', "#add_category",function()
{
	$("#categoryModalDialogTitle").html(<?php echo json_encode(lang('common_add_category')); ?>);
	var parent_id = $("#category_id").val();
	
	$parent_id_select = $('#parent_id');
	$parent_id_select[0].selectize.setValue(parent_id, false);
	
	$("#categories_form").attr('action',SITE_URL+'/expenses/save_category');
	
	//Clear form
	$(":file").filestyle('clear');
	$("#categories_form").find('#category_name').val("");

	
	//show
	$("#category-input-data").modal('show');
});

$("#categories_form").submit(function(event)
{
	event.preventDefault();

	$(this).ajaxSubmit({ 
		success: function(response, statusText, xhr, $form){
			show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
			if(response.success)
			{
				$("#category-input-data").modal('hide');
				
				var category_id_selectize = $("#category_id")[0].selectize
				category_id_selectize.clearOptions();
				category_id_selectize.addOption(response.categories);		
				category_id_selectize.addItem(response.selected, true);			
			}		
		},
		dataType:'json',
	});
});

	$('#expense_image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

	$('.delete_file').click(function(e)
	{
		e.preventDefault();
		var $link = $(this);
		bootbox.confirm(<?php echo json_encode(lang('common_confirm_file_delete')); ?>, function(response)
		{
			if (response)
			{
				$.get($link.attr('href'), function()
				{
					$link.parent().fadeOut();
				});
			}
		});
		
	});
</script>
<?php $this->load->view('partial/footer')?>
