<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title">Confirmación de articulos por expirar</h4>
		</div>
		<div class="modal-body ">
			<div class="row" id="form">
				
				<div class="spinner" id="grid-loader" style="display:none">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				</div>
				<div class="col-md-12">
					<?php
						$expire_date = $this->input->get('expire_date');
						$item_id = $this->input->get('item_id');
						$item_name = $this->Item->get_info( $item_id )->name;
						$employee_id = $this->input->get('employee_id');

						$get_all_items_expired = $this->Receiving->get_all_items_expired_at_date( $item_id, $expire_date );
						$filter_by_location = $this->Receiving->filter_item_expired_by_location( $get_all_items_expired, $this->Employee->get_logged_in_employee_current_location_id() );
					?>
					<?php $person_id = $person_info->person_id ? $person_info->person_id : '';?>
					<table class="table">
						<h4 class="text-center">Descripción del Producto/Item <b><?php echo $item_name; ?></h4>
						<thead>
							<tr>
								<th class="colsho ">No. Recibo</th>
								<th class="colsho ">Fecha de expiración</th>
								<th class="colsho ">Cantidad a expirar</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$items_quantity = 0;
								foreach( $filter_by_location as $item_expired ){	?>
									<tr>
										<td class="colsho "><?php echo $item_expired['receiving_id']; ?></td>
										<td class="colsho "><?php echo $item_expired['expire_date']; ?></td>
										<td class="colsho "><?php echo to_currency_no_money( $item_expired['quantity_purchased'] ); ?></td>
									</tr>
									<?php	
									$items_quantity = $items_quantity+(int)$item_expired['quantity_purchased'];
								}
							?>
							<tr>
								<td class="colsho "></td>
								<td class="colsho "><b>Total</b></td>
								<td class="colsho "><?php echo to_currency_no_money( $items_quantity ); ?></td>
							</tr>
						</tbody>
					</table>
					<span style="text-align: center; width: 100%; display: block; font-size: 10px;">*La cantidad de items por expirar que confirme sera en base a todos los articulos (<?php echo to_currency_no_money( $items_quantity ) ?>)</span>
					<br>
					<?php echo form_open('/Receivings/save_items_quantity_expiration' ,array('id'=>$controller_name.'_form','class'=>'form-horizontal')); ?>

					<div class="form-group">
						<?php echo form_label(lang('items_quantity_expiration').':', 'item_quantity_expiration',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'item_quantity_expiration_name',
							'type'	=>	'number',
							'id'	=>	'item_quantity_expiration_id',
							'value'	=>	$person_info->account_number,
							'min' => 1,
							'max' => $items_quantity),
							);?>
						</div>
					</div>
					<hr>
					<?php echo form_hidden('items', json_encode( $filter_by_location )); ?>
					<?php echo form_hidden('quantity', $items_quantity); ?>
					<?php echo form_hidden('item_id', $item_id); ?>
					<?php echo form_hidden('employee_id', $employee_id); ?>
					<div class="modal-footer" style="padding:0px;">
						<div class="form-acions">
							<?php
							echo form_submit(array(
								'name'	=>	'submit',
								'id'	=>	'submit',
								'value'	=>	lang('common_save'),
								'class'	=>'	submit_button btn btn-success')
							);
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
echo form_close();
?>

<script type='text/javascript'>
					
//validation and submit handling
$(document).ready(function()
{
	setTimeout(function(){$(":input:visible:first","#employee_form").focus();},100);
    setTimeout(function(){$(":input:visible:first","#suppliers_form").focus();},100);
    setTimeout(function(){$(":input:visible:first","#customers_form").focus();},100);

	$("#sales_nit_id").autocomplete({
		source: [],
	});

	$("#sales_nit_id").on("input", function (  ) {

		let valueInputNit = $(this).val();

		if( valueInputNit.length >= 3 ) {
			let felplex_url = "<?php echo $this->config->item('base_url_felplex') ?>";
			let felplex_entity = "<?php echo $this->config->item('entity_id_felplex') ?>";
			let api_key = "<?php echo $this->config->item('api_key_felplex') ?>";
			
			let api_url = felplex_url + "/api/entity/" + felplex_entity + "/find/NIT/" + valueInputNit;
			$.ajax({
				url: "<?php echo site_url('sales/call_felplex_to_find_nit'); ?>",
				method: "POST",
				data: { api_url: api_url, api_key: api_key, get_all: 1 },
				success: function(response) {
					let response_array = JSON.parse(response);
					
					let items_to_add = response_array.map(function (item) {
						return {
							label: item.nit,
							value: item.name
						}
					});
					
					$( "#sales_nit_id" ).autocomplete( {
						source: items_to_add,
						delay: 500,
						autoFocus: false,
						minLength: 0,
						select: function (event, ui) {
							event.preventDefault();
							let itemSelected = ui.item;
							let nitPattern = /(^[0-9]{6,12}$)|(^[0-9]{1,6}([0-9]?)(-?[0-9kK]){1}$)|(^CF$)/;

							if( itemSelected.label == '' || itemSelected.label == 'cf' || itemSelected.label == 'c/f' || itemSelected.label == 'C/F' || itemSelected.label == 'Cf' || itemSelected.label == 'cF' ) {
								itemSelected.label = 'CF';
							}

							if (nitPattern.test(itemSelected.label)) {

								let inputName = document.querySelector('#first_name');
								let inputNit = document.querySelector('#sales_nit_id');

								inputNit.value = itemSelected.label;
								inputName.value = itemSelected.value;
							}
						}
					} ).data("ui-autocomplete")._renderItem = function(ul, item) {
						return $("<li class='customer-badge suggestions'></li>")
							.data("item.autocomplete", item)
							.append('<a class="suggest-item"><div class="avatar">' +
								'<img src="http://localhost/assets/img/user.png" alt="">' +
								'</div>' +
								'<div class="details">' +
								'<div class="name">' +
								item.label +
								'</div>' +
								'<span class="email">' +
								item.value +
								'</span>' +
								'</div></a>')
							.appendTo(ul);
					};
				},
				error: function(xhr, status, error) {
					console.error(xhr.responseText);
				}
			});
		}

	});

	$('#employee_form').validate({
		submitHandler:function(form)
		{

			doEmployeeSubmit(form);
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
			first_name: "required",

			username:
			{
				required:true,
				minlength: 1
			},

			password:
			{
				minlength: 1
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: {
				"required": true
			}
		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
     		last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
     		username:
     		{
     			required: <?php echo json_encode(lang('common_username_required')); ?>,
     			minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
     		},
			password:
			{
				minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
			},
			repeat_password:
			{
				equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
     		},
     		email: <?php echo json_encode(lang('common_email_invalid_format')); ?>
		}
	});

	$('#suppliers_form').validate({
		submitHandler:function(form)
		{
			doEmployeeSubmit(form,'supplier');
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
			company_name: "required",
		},
		messages: 
		{
     		company_name: <?php echo json_encode(lang('suppliers_company_name_required')); ?>
		}
	});

	$('#customers_form').validate({
		submitHandler:function(form) {
			$.post('<?php echo site_url("customers/account_number_exists");?>', {
				account_number:  parseInt($('#sales_nit_id').val())
			}, function(data) {
				<?php if(!$person_info->person_id) { ?>
					if(!data) {
						bootbox.alert(<?php echo json_encode(lang('sales_alert_nit_duplicate'));?>, function(result) {
							if (false) {
								doEmployeeSubmit(form,'customer');
							}
						});
					}
					else {
						doEmployeeSubmit(form,'customer');
					}
				<?php } else { ?>
					doEmployeeSubmit(form,'customer');
				<?php } ?>
			} , "json")
			.error(function() {  });
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
			first_name: "required",
			sales_nit_name: "required"
		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
			sales_nit_name: <?php echo json_encode(lang('common_nit_value_required')); ?>
		}
	});
});

var submitting = false;

function doEmployeeSubmit(form,type = null)
{
	$('#grid-loader').show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
		success:function(response)
		{

			$('#grid-loader').hide();
			window.location.reload(true);
			submitting = false;
			$('#myModalDisableClose').modal('hide');
			if (response.success)
			{
				if (type == 'customer') {
					$.post('<?php echo site_url("sales/select_customer");?>', {customer: response.person_id + '|FORCE_PERSON_ID|'}, function()
					{
						window.location.href = '<?php echo site_url('sales/index/1'); ?>';
					});
				}

				if (type == 'supplier') {
					$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: response.person_id}, function()
					{
						window.location.href = '<?php echo site_url('receivings'); ?>';
					});
				}
				show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>+' #' + response.person_id);

			}
			else
			{
				show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
			}
			
		},
		dataType:'json'
	});
}
</script>