<?php

if (isset($standalone) && $standalone) {
	$this->load->view("partial/header_standalone");
} else {
	$this->load->view("partial/header");
}

?>

<?php
$this->load->helper('sale');

$is_on_device_tip_processor = $this->Location->get_info_for_key('credit_card_processor', isset($override_location_id) ? $override_location_id : FALSE) == 'card_connect' || $this->Location->get_info_for_key('credit_card_processor', isset($override_location_id) ? $override_location_id : FALSE) == 'coreclear2';


$return_policy = ($loc_return_policy = $this->Location->get_info_for_key('return_policy', isset($override_location_id) ? $override_location_id : FALSE)) ? $loc_return_policy : $this->config->item('return_policy');
$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
$website = ($website = $this->Location->get_info_for_key('website', isset($override_location_id) ? $override_location_id : FALSE)) ? $website : $this->config->item('website');
$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');


if (isset($error_message)) {
	echo '<h1 style="text-align: center;">' . $error_message . '</h1>';
	exit;
}
?>

<!-- Css Loader  -->
<div class="spinner hidden" id="ajax-loader" style="width:100vw;  height:100vh;">
	<div class="rect1"></div>
	<div class="rect2"></div>
	<div class="rect3"></div>
</div>

<?php
if (!(isset($standalone) && $standalone)) {
?>
	<div class="manage_buttons hidden-print">

		<div class="row">
			<div class="col-md-6">
				<div class="hidden-print search no-left-border">
					<ul class="list-inline print-buttons">
						<li></li>

						<?php 
							//if ($sale_id_raw != lang('sales_test_mode_transaction', '', array(), TRUE)) { 
							if( FALSE ) {
						?>
							<li>
								<?php echo anchor('sales/download_receipt/' . $sale_id_raw, '<span class="ion-arrow-down-a"></span>', array('id' => 'download_pdf', 'class' => 'btn btn-primary btn-lg hidden-print')); ?>
							</li>
	
						<?php } ?>
							<li>
								<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="print_receipt()"> <?php echo lang('common_print', '', array(), TRUE); ?> </button>
							</li>
					</ul>
				</div>
			</div>
			<div class="col-md-6">
				<div class="buttons-list">
					<div class="pull-right-btn">
						<ul class="list-inline print-buttons">
							
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } else {
?>
	<div class="col-md-12 text-center hidden-print">
		<div class="row">
			<button class="btn btn-primary btn-lg" id="print_button" onclick="print_receipt()"> <?php echo lang('common_print', '', array(), TRUE); ?> </button>
		</div>
		<br />
	</div>
<?php
} ?>
<div <?php echo $this->config->item('uppercase_receipts') ? 'style="text-transform: uppercase !important"' : ''; ?>class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small'; ?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<div class="panel-body panel-pad">
				<div class="row">
					<!-- from address-->
					<div class="col-md-4 col-sm-4 col-xs-12">
						<ul class="list-unstyled invoice-address" style="margin-bottom:2px;">
							<?php if ($company_logo) { ?>

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
					</div>
					<!--  sales-->
					<div class="col-md-4 col-sm-4 col-xs-12">
						<ul class="list-unstyled invoice-detail" style="margin-bottom:2px;">
							<li>
								<?php if ($receipt_title && (!isset($sale_type) || $sale_type != lang('common_estimate'))) { ?>
									<?php echo H($receipt_title); ?><?php echo ($total) < 0 ? ' (' . lang('sales_return', '', array(), TRUE) . ')' : ''; ?>
									<br>
								<?php } ?>
								<strong><?php echo $transaction_time ?></strong>
							</li>



							<?php
							if (version_compare(PHP_VERSION, '7.2', '>=')  && function_exists('bcadd')) {
								require_once(APPPATH . "libraries/hashids/vendor/autoload.php");

								$hashids = new Hashids\Hashids(base_url());
								$sms_id = $hashids->encode($sale_id_raw);
								$signature = $this->Sale->get_receipt_signature($sale_id_raw);

							?>
								<li class="remove_when_mobile"><span><?php echo lang('common_recp', '', array(), TRUE) . ":"; ?></span><?php echo anchor(site_url('r/' . $sms_id . '?signature=' . $signature), 'No. '.$correlative); ?>
								<li class="keep_when_mobile" style="display: none"><span><?php echo lang('common_recp', '', array(), TRUE) . ":"; ?></span><?php echo 'No. '.H($correlative); ?>

								<?php
							} else {
								?>
								<li><span><?php echo lang('common_recp', '', array(), TRUE) . ":"; ?></span><?php echo H($correlative); ?>
								<?php
							}
								?>


								</li>
								<?php if (isset($deleted) && $deleted) { ?>
									<li><span class="text-danger" style="color: #df6c6e;"><strong><?php echo lang('sales_deleted_voided', '', array(), TRUE); ?></strong></span></li>
								<?php } ?>
								<?php if (isset($sale_type)) { ?>
									<li><?php echo H($sale_type); ?></li>
								<?php } ?>

								<?php if ($is_ecommerce) { ?>
									<li><?php echo lang('common_ecommerce', '', array(), TRUE); ?></li>
								<?php } ?>

								<?php
								if ($this->Register->count_all(isset($override_location_id) ? $override_location_id : FALSE) > 1 && $register_name) {
								?>
									<li><span><?php echo lang('sales_cash_name', '', array(), TRUE) . ':'; ?></span><?php echo H($register_name); ?></li>
								<?php
								}
								?>

								<?php
								if ($tier && !$this->config->item('hide_tier_on_receipt')) {
								?>
									<li><span><?php echo $this->config->item('override_tier_name') ? $this->config->item('override_tier_name') : lang('common_tier_name', '', array(), TRUE) . ':'; ?></span><?php echo H($tier); ?></li>
								<?php
								}
								?>

								<?php
								if (!$this->config->item('remove_employee_from_receipt')) { ?>
									<li><span><?php echo lang('sales_cash_employee', '', array(), TRUE) . ":"; ?></span><?php echo H($this->config->item('remove_employee_lastname_from_receipt') ? $employee_firstname : $employee); ?></li>
									<?php
			
								}
								?>
						</ul>
					</div>
					

				

				</div>
				<?php
				$x_col = 6;
				$xs_col = 4;
				if ($discount_exists) {
					$x_col = 4;
					$xs_col = 3;

					if ($this->config->item('wide_printer_receipt_format')) {
						$x_col = 4;
						$xs_col = 2;
					}
				} else {
					if ($this->config->item('wide_printer_receipt_format')) {
						$x_col = 4;
						$xs_col = 2;
					}
				}
				?>

				<table style="width:100%;" id='receipt-draggable'>
					<thead>
						<tr>
							<!-- invoice heading-->
							<th class="invoice-table">
								<div class="row">
									
									<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
										<div class="invoice-head text-right item-price">	
											<?php echo lang('sales_transaction_type', '', array(), TRUE); ?>
										</div>
									</div>

									<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
										<div class="invoice-head text-right item-price">
											<?php echo lang('common_concept', '', array(), TRUE); ?>
										</div>
									</div>

									<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
										<div class="invoice-head text-right item-qty">
											<?php if($amount > 0){ echo lang('common_quantity', '', array(), TRUE).' '.lang('sale_added', '', array(), TRUE);} else if($amount < 0) { echo lang('common_quantity', '', array(), TRUE).' '.lang('sale_subtracted', '', array(), TRUE);}?>
										</div>
									</div>

								</div>
							</th>
						</tr>
					</thead>
					
						<tbody>
							<tr class="invoice-item-details">
								<!-- invoice items-->
								<td>
									<div class="row">
									
										<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
										<div class="invoice-head text-right item-price">
												<?php 
													if($payment_type == 'common_cash')
													{
														echo lang('common_cash');
													}	
													else if ($payment_type == 'common_check')
													{
														echo lang('common_check');
													}
													else if ($payment_type == 'common_giftcard')
													{
														echo lang('common_giftcard');
													}
													else if ($payment_type == 'common_debit')
													{
														echo lang('common_debit');
													}
													else if ($payment_type == 'common_credit')
													{
														echo lang('common_credit');
													}
												 ?>
											</div>
										</div>

										<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
											<div class="invoice-head text-right item-price">
												<?php echo $note ?>
											</div>
										</div>

										<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
											<div class="invoice-head text-right item-qty">
												<?php echo number_format($amount, 2, '.', '') ?>
											</div>
										</div>

									</div>
								</td>
							</tr>

						</tbody>
					<?php
					
					?>
				</table>

				<div class="invoice-footer gift_receipt_element">
					<div class="row">
						<div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
							<div class="invoice-footer-heading"><?php echo lang('common_sub_total', '', array(), TRUE); ?></div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4">
							<div class="invoice-footer-value">
								<?php
									echo to_currency($amount);
								?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
							<div class="invoice-footer-heading"><?php echo lang('common_total', '', array(), TRUE); ?></div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4">
							<div class="invoice-footer-value invoice-total" style="font-size: 150%;font-weight: bold;;">

									<?php echo to_currency($total_invoice_amount = $amount) ?>

							</div>
						</div>
					</div>
				</div>
				<!-- invoice footer-->

				<br />

				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="invoice-policy" id="invoice-policy-return">
							<?php echo nl2br(H('F_________________________')); ?>
						</div>
						<div class="invoice-policy" id="invoice-policy-return">
							<?php echo nl2br(H(lang('sales_cash_signature').': '.$employee)); ?>
						</div>

						<br />
						<br />
						<br />

				</div>
			</div>
			<!--container-->
		</div>
	</div>
</div>
</div>


<div id="duplicate_receipt_holder" style="display: none;">

</div>

<?php
if ($this->config->item('allow_reorder_sales_receipt')) 
{
?>
<style>
	#receipt-draggable tbody {
		cursor: move;
		width: 100%;
	}

	.invoice-head {
		cursor: pointer;
	}
</style>
<?php
}
?>

<?php if ($this->config->item('print_after_sale') && $this->uri->segment(2) == 'complete') {
?>
	<script type="text/javascript">
		$(window).bind("load", function() {
			<?php
			if ($this->agent->browser() == 'Chrome') {
			?>
				setTimeout(function() {
					print_receipt();
				}, 1500);
			<?php
			} else {
			?>
				print_receipt();
			<?php
			}
			?>
		});
	</script>
<?php }  ?>

<script type="text/javascript">
	<?php
	if ($this->session->userdata('amount_change')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->userdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_change_due') . ': ' . to_currency($this->session->userdata('amount_change'))); ?>, {
			timeOut: 30000
		});
	<?php
	}
	?>


	$(document).ready(function() {

		<?php if (isset($email_sent) && $email_sent) { ?>
			show_feedback('success', <?php echo json_encode(lang('common_receipt_sent', '', array(), TRUE)); ?>, <?php echo json_encode(lang('common_success', '', array(), TRUE)); ?>);
		<?php } ?>
		$("#edit_sale").click(function(e) {
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang('sales_sale_edit_confirm', '', array(), TRUE)); ?>, function(result) {
				if (result) {
					$("#sales_change_form").submit();
				}
			});
		});

		$("#email_receipt,#sms_receipt").click(function() {
			$.get($(this).attr('href'), function() {
				show_feedback('success', <?php echo json_encode(lang('common_receipt_sent', '', array(), TRUE)); ?>, <?php echo json_encode(lang('common_success', '', array(), TRUE)); ?>);

			});

			return false;
		});
		
	<?php
	if ($this->config->item('allow_reorder_sales_receipt')) 
	{
		if (!$this->agent->is_mobile() && !$this->agent->is_tablet()) 
		{
		?>
		$("#receipt-draggable").sortable({
			items: 'tbody',
			cursor: 'move',
			axis: 'y',
			dropOnEmpty: false,
			start: function(e, ui) {
				ui.item.addClass("selected");
				var td_width = [];
				var td_height = [];
				for( let i = 0; i < $("#receipt-draggable tbody").length; i ++){
					if($($("#receipt-draggable tbody")[i]).hasClass('selected') || $($("#receipt-draggable tbody")[i]).hasClass('ui-sortable-placeholder')){
						continue;
					}else{
						td_height = $($("#receipt-draggable tbody")[i]).height();
						for(let j = 0; j<$($("#receipt-draggable tbody")[i]).find(".invoice-item-details td").length; j++){
							td_width.push($($($("#receipt-draggable tbody")[i]).find(".invoice-item-details td")[j]).width());
						}
						break;
					}
				}

				$(".ui-sortable-placeholder").html("<tr><td>&nbsp;</td></tr>");
				$(".ui-sortable-placeholder").height(td_height+'px');

				for(let k=0; k<$($("#register tbody.selected tr")[0]).find('td').length; k++){
					$($($("#register tbody.selected tr")[0]).find('td')[k]).width(td_width[k]+'px');
				}

			},
			stop: function(e, ui) {

				for(let k=0; k<$($("#register tbody.selected tr")[0]).find('td').length; k++){
					$($($("#register tbody.selected tr")[0]).find('td')[k]).attr('style','');
				}
				ui.item.removeClass("selected");

				updateItemOrder();
			},
			sort:function(e){
				$(".ui-sortable-helper").css("width", $("table#register").width()+'px');
				$(".ui-sortable-helper tr").css("width", $("table#register").width()+'px');
			}
		});
		<?php 
		}
		?>
		function updateItemOrder() {
			var length = $("#receipt-draggable tbody").length;
			var item_lines = [];
			for (let i = 0; i < length; i++) {
				let item_id = $($("#receipt-draggable tbody")[i]).data('item-id');
				let sale_id = $($("#receipt-draggable tbody")[i]).data('sale-id');
				let item_class = $($("#receipt-draggable tbody")[i]).data('item-class');
				item_lines.push({
					item_id: item_id,
					sale_id: sale_id,
					item_class: item_class,
					receipt_line_sort_order: (length - i)
				});
			}

			$('#ajax-loader').removeClass('hidden');
			var href = '<?php echo site_url("ecommerce/manual_sync"); ?>';
			clear_order_icon();

			$.ajax({
				type: "POST",
				url: SITE_URL + '/sales/update_sales_item_order',
				data: {
					item_lines: item_lines
				},
				dataType: "json",
				success: function(data) {
					$('#ajax-loader').addClass('hidden');
					console.log("update");
				},
				error: function() {
					$('#ajax-loader').addClass('hidden');
					console.log("update");
				}
			});
		}

		function invoice_receipt_item_sort(obj, item_type, order_type) {
			var length = $("#receipt-draggable tbody").length;
			var item_lines = [];
			for (let i = 0; i < length; i++) {
				let item_id = $($("#receipt-draggable tbody")[i]).data('item-id');
				let sale_id = $($("#receipt-draggable tbody")[i]).data('sale-id');
				let item_name = $($("#receipt-draggable tbody")[i]).data('item-name');
				let item_price = $($("#receipt-draggable tbody")[i]).data('item-price');
				let item_qty = $($("#receipt-draggable tbody")[i]).data('item-qty');
				let item_total = $($("#receipt-draggable tbody")[i]).data('item-total');
				let item_class = $($("#receipt-draggable tbody")[i]).data('item-class');

				item_lines.push({
					item_id: item_id,
					sale_id: sale_id,
					item_class: item_class,
					item_name: item_name,
					item_price: item_price,
					item_qty: item_qty,
					item_total: item_total,
					line: (length - i)
				});
			}

			if (item_type == 'price') {
				if (order_type == 'down')
					item_lines.sort(function(a, b) {
						return b.item_price - a.item_price
					});
				else
					item_lines.sort(function(a, b) {
						return a.item_price - b.item_price
					});
			} else if (item_type == 'qty') {
				if (order_type == 'down')
					item_lines.sort(function(a, b) {
						return b.item_qty - a.item_qty
					});
				else
					item_lines.sort(function(a, b) {
						return a.item_qty - b.item_qty
					});
			} else if (item_type == 'total') {
				if (order_type == 'down')
					item_lines.sort(function(a, b) {
						return b.item_total - a.item_total
					});
				else
					item_lines.sort(function(a, b) {
						return a.item_total - b.item_total
					});
			} else if (item_type == 'name') {
				if (order_type == 'down')
					item_lines.sort(function(a, b) {
						if (a.item_name > b.item_name) {
							return -1;
						}
						if (b.item_name > a.item_name) {
							return 1;
						}
						return 0;
					});
				else
					item_lines.sort(function(a, b) {
						if (b.item_name > a.item_name) {
							return -1;
						}
						if (b.item_name > a.item_name) {
							return 1;
						}
						return 0;
					});
			}

			sort_items(item_lines);
			if (order_type == 'up') {
				$(obj).removeClass('ion-arrow-down-b');
				$(obj).addClass('ion-arrow-up-b');
			} else {
				$(obj).removeClass('ion-arrow-up-b');
				$(obj).addClass('ion-arrow-down-b');
			}
		}

		function sort_items(item_lines) {
			for (let i = 0; i < item_lines.length; i++) {
				var obj_origin = $("#receipt-draggable tbody[data-item-id='" + item_lines[i]['item_id'] + "']");
				var obj_new = obj_origin.clone();
				$("#receipt-draggable").append(obj_new);
				obj_origin.remove();
			}
			updateItemOrder();
		}

		function clear_order_icon() {
			$(".invoice-head.item-name, .invoice-head.item-price, .invoice-head.item-qty, .invoice-head.item-total").removeClass('ion-arrow-up-b');
			$(".invoice-head.item-name, .invoice-head.item-price, .invoice-head.item-qty, .invoice-head.item-total").removeClass('ion-arrow-down-b');
		}

		$(".invoice-head.item-name, .invoice-head.item-price, .invoice-head.item-qty, .invoice-head.item-total").on('click', function() {
			var type = "price";
			if ($(this).hasClass('item-name')) {
				type = 'name';
			} else if ($(this).hasClass('item-qty')) {
				type = 'qty';
			} else if ($(this).hasClass('item-total')) {
				type = 'total';
			} else if ($(this).hasClass('item-price')) {
				type = 'price';
			}

			if ($(this).hasClass('ion-arrow-down-b')) {
				invoice_receipt_item_sort(this, type, 'up');
			} else if ($(this).hasClass('ion-arrow-up-b')) {
				invoice_receipt_item_sort(this, type, 'down');
			} else {
				invoice_receipt_item_sort(this, type, 'down');
			}
		});
	<?php 
	}
	?>
	});

	$('#print_duplicate_receipt').click(function() {
		if ($('#print_duplicate_receipt').prop('checked')) {
			var receipt = $('#receipt_wrapper').clone();
			$('#duplicate_receipt_holder').html(receipt);
			$("#duplicate_receipt_holder").addClass('visible-print-block');
			$("#duplicate_receipt_holder #signature_holder").addClass('hidden');
			$("#duplicate_receipt_holder .receipt_type_label").text(<?php echo json_encode(lang('sales_duplicate_receipt', '', array(), TRUE)); ?>);
			$(".receipt_type_label").show();
			$(".receipt_type_label").addClass('show_receipt_labels');
		} else {
			$("#duplicate_receipt_holder").empty();
			$("#duplicate_receipt_holder").removeClass('visible-print-block');
			$("#duplicate_receipt_holder #signature_holder").removeClass('hidden');
			$(".receipt_type_label").hide();
			$(".receipt_type_label").removeClass('show_receipt_labels');
		}
	});

	<?php
	$this->load->helper('sale');
	if ($this->config->item('always_print_duplicate_receipt_all') || ($this->config->item('automatically_print_duplicate_receipt_for_cc_transactions') && $is_credit_card_sale)) {
	?>
		$("#print_duplicate_receipt").trigger('click');
	<?php
	}
	?>

	<?php
	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt')) {
	?>
		window.onafterprint = function() {
			window.location = '<?php echo site_url('sales'); ?>';
		}
	<?php
	}
	?>

	function print_receipt() {
		window.print();
	}

	function toggle_gift_receipt() {
		var gift_receipt_text = <?php echo json_encode(lang('sales_gift_receipt', '', array(), TRUE)); ?>;
		var regular_receipt_text = <?php echo json_encode(lang('sales_regular_receipt', '', array(), TRUE)); ?>;

		if ($("#gift_receipt_button").hasClass('regular_receipt')) {
			$('#gift_receipt_button').addClass('gift_receipt');
			$('#gift_receipt_button').removeClass('regular_receipt');
			$("#gift_receipt_button").text(gift_receipt_text);
			$('.gift_receipt_element').show();
		} else {
			$('#gift_receipt_button').removeClass('gift_receipt');
			$('#gift_receipt_button').addClass('regular_receipt');
			$("#gift_receipt_button").text(regular_receipt_text);
			$('.gift_receipt_element').hide();
		}

	}

	//timer for sig refresh
	var refresh_timer;
	var sig_canvas = document.getElementById('sig_cnv');

	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile()) {
	?>
		var signaturePad = new SignaturePad(sig_canvas);
	<?php
	}
	?>
	$("#capture_digital_sig_button").click(function() {
		<?php
		//Only use Sig touch on mobile
		if ($this->agent->is_mobile()) {
		?>
			signaturePad.clear();
		<?php
		} else {
		?>
			try {
				if (TabletConnectQuery() == 0) {
					bootbox.alert(<?php echo json_encode(lang('common_unable_to_connect_to_signature_pad', '', array(), TRUE)); ?>);
					return;
				}
			} catch (exception) {
				bootbox.alert(<?php echo json_encode(lang('common_unable_to_connect_to_signature_pad', '', array(), TRUE)); ?>);
				return;
			}

			var ctx = document.getElementById('sig_cnv').getContext('2d');
			SigWebSetDisplayTarget(ctx);
			SetDisplayXSize(500);
			SetDisplayYSize(100);
			SetJustifyMode(0);
			refresh_timer = SetTabletState(1, ctx, 50);
			KeyPadClearHotSpotList();
			ClearSigWindow(1);
			ClearTablet();
		<?php
		}
		?>

		$("#capture_digital_sig_button").hide();
		$("#digital_sig_holder").show();
	});

	$("#capture_digital_sig_clear_button").click(function() {
		<?php
		//Only use Sig touch on mobile
		if ($this->agent->is_mobile()) {
		?>
			signaturePad.clear();
		<?php
		} else {
		?>
			ClearTablet();
		<?php
		}
		?>
	});

	$("#capture_digital_sig_done_button").click(function() {
		<?php
		//Only use Sig touch on mobile
		if ($this->agent->is_mobile()) {
		?>
			if (signaturePad.isEmpty()) {
				bootbox.alert(<?php echo json_encode(lang('common_no_sig_captured', '', array(), TRUE)); ?>);
			} else {
				SigImageCallback(signaturePad.toDataURL().split(",")[1]);
				$("#capture_digital_sig_button").show();
			}
		<?php
		} else {
		?>
			if (NumberOfTabletPoints() == 0) {
				bootbox.alert(<?php echo json_encode(lang('common_no_sig_captured', '', array(), TRUE)); ?>);
			} else {
				SetTabletState(0, refresh_timer);
				//RETURN TOPAZ-FORMAT SIGSTRING
				SetSigCompressionMode(1);
				var sig = GetSigString();

				//RETURN BMP BYTE ARRAY CONVERTED TO BASE64 STRING
				SetImageXSize(500);
				SetImageYSize(100);
				SetImagePenWidth(5);
				GetSigImageB64(SigImageCallback);
				$("#capture_digital_sig_button").show();
			}
		<?php
		}
		?>
	});

	function SigImageCallback(str) {
		$("#digital_sig_holder").hide();
		$.post('<?php echo site_url('sales/sig_save'); ?>', {
			sale_id: <?php echo json_encode($sale_id_raw); ?>,
			image: str
		}, function(response) {
			$("#signature_holder").empty();
			$("#signature_holder").append('<img src="' + SITE_URL + '/app_files/view_cacheable/' + response.file_id + '?timestamp=' + response.file_timestamp + '" width="250" />');
		}, 'json');

	}

	<?php
	//EMV Usb Reset
	if (isset($reset_params)) {
	?>
		var data = {};
		<?php
		foreach ($reset_params['post_data'] as $name => $value) {
			if ($name && $value) {
		?>
				data['<?php echo $name; ?>'] = '<?php echo $value; ?>';
		<?php
			}
		}
		?>

		mercury_emv_pad_reset(<?php echo json_encode($reset_params['post_host']); ?>, <?php echo $this->Location->get_info_for_key('listener_port'); ?>, data);
	<?php
	}
	if (isset($trans_cloud_reset) && $trans_cloud_reset) {
	?>
		$.get(<?php echo json_encode(site_url('sales/reset_pin_pad')); ?>);
	<?php
	}
	?>

	<?php
	if (isset($prompt_for_customer_info) && $prompt_for_customer_info)
	{
	?>
		$.get(<?php echo json_encode(site_url('sales/prompt_for_customer_info/'.$sale_id_raw)); ?>);
	<?php
	}
	?>

	<?php if ($this->config->item('auto_capture_signature')) { ?>
		$("#capture_digital_sig_button").click();
	<?php } ?>
</script>

<?php if (($is_integrated_credit_sale || $is_sale_integrated_ebt_sale) && $is_sale) { ?>
	<script type="text/javascript">
		show_feedback('success', <?php echo json_encode(lang('sales_credit_card_processing_success', '', array(), TRUE)); ?>, <?php echo json_encode(lang('common_success', '', array(), TRUE)); ?>);
	</script>
<?php } ?>

<script>
	html2canvas(document.querySelector("#receipt_wrapper"), {
		height: $("#receipt_wrapper").height(),
		windowWidth: 280,
		onclone: function(doc) {
			doc.querySelector('#invoice-policy-return').style.display = 'none';
			doc.querySelector('#invoice-policy-return-mobile').style.display = 'block';

			doc.querySelector('#announcement').style.display = 'none';
			doc.querySelector('#announcement-mobile').style.display = 'block';

			doc.querySelector('.remove_when_mobile').style.display = 'none';
			doc.querySelector('.keep_when_mobile').style.display = 'block';



			doc.querySelectorAll('.invoice-table-content').forEach(function(item) {
				item.style.borderBottom = 'none';
			});


			doc.querySelectorAll('.receipt-row-item-holder').forEach(function(item) {
				item.style.clear = 'both';
			});

			if ($("#capture_digital_sig_button").length) {
				doc.querySelector('#capture_digital_sig_button').style.display = 'none';
			}

		}
	}).then(canvas => {
		document.getElementById("print_image_output").innerHTML = canvas.toDataURL();
	});
</script>
<script type="text/print-image" id="print_image_output"></script>
<!-- This is used for mobile apps to print receipt-->
<script type="text/print" id="print_output"><?php echo $company; ?>

<?php echo H($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?>

<?php echo H($this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE)); ?>

<?php if ($website) { ?>
<?php echo H($website); ?>
	
<?php } ?>

<?php echo H($receipt_title); ?>

<?php echo H($transaction_time); ?>

<?php if (isset($customer)) { ?>
	<?php echo lang('common_customer', '', array(), TRUE) . ": " . H($customer); ?>
	<?php if (!$this->config->item('remove_customer_contact_info_from_receipt')) { ?>
	
	<?php if (!empty($customer_address_1)) { ?><?php echo lang('common_address', '', array(), TRUE); ?>: <?php echo H($customer_address_1 . ' ' . $customer_address_2); ?><?php } ?>
	<?php if (!empty($customer_city)) { echo H($customer_city . ' ' . $customer_state . ', ' . $customer_zip); ?><?php } ?>
	<?php if (!empty($customer_country)) { echo H($customer_country); ?> <?php } ?>
	<?php if (!empty($customer_phone)) { ?><?php echo lang('common_phone_number', '', array(), TRUE); ?> : <?php echo H($customer_phone); ?> <?php } ?>

	<?php if (!empty($customer_email)) { ?><?php echo lang('common_email', '', array(), TRUE); ?> : <?php echo H($customer_email); ?><?php } ?>

<?php } else { ?>
	
<?php
	}
}
?>
<?php echo lang('common_sale_id', '', array(), TRUE) . ": " . $sale_id; ?>
<?php if (isset($sale_type)) { ?>
<?php echo $sale_type; ?>
<?php } ?>
	
<?php if (!$this->config->item('remove_employee_from_receipt')) { ?>
<?php echo lang('common_employee', '', array(), TRUE) . ": " . $this->config->item('remove_employee_lastname_from_receipt') ? $employee_firstname : $employee; ?>
<?php } ?>
	
<?php
if ($this->Location->get_info_for_key('enable_credit_card_processing', isset($override_location_id) ? $override_location_id : FALSE)) {
	echo lang('common_merchant_id', '', array(), TRUE) . ': ' . H($this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE));
}
?>

<?php echo lang('common_item', '', array(), TRUE); ?>            <?php echo lang('common_price', '', array(), TRUE); ?> <?php echo lang('common_quantity', '', array(), TRUE); ?><?php if ($discount_exists) {
																																														echo ' ' . lang('common_discount_percent', '', array(), TRUE);
																																													} ?> <?php echo lang('common_total', '', array(), TRUE); ?>

---------------------------------------
<?php
foreach (array_reverse($cart_items, true) as $line => $item) {
?>
<?php echo character_limiter(H($item->name), 14, '...'); ?><?php echo strlen($item->name) < 14 ? str_repeat(' ', 14 - strlen(H($item->name))) : ''; ?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item->unit_price, 10)); ?> <?php echo to_quantity($item->quantity); ?><?php if ($discount_exists) {
																																																																														echo ' ' . $item->discount;
																																																																													} ?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item->unit_price * $item->quantity - $item->unit_price * $item->quantity * $item->discount / 100, 10)); ?>

  <?php echo clean_html($item->description); ?>  <?php echo isset($item->serialnumber) ? H($item->serialnumber) : ''; ?>
	

<?php
}
?>

<?php echo lang('common_sub_total', '', array(), TRUE); ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($subtotal)); ?>


<?php foreach ($taxes as $name => $value) { ?>
<?php echo $name; ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($value)); ?>

<?php }; ?>

<?php echo lang('common_total', '', array(), TRUE); ?>: <?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($total))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($total)); ?>

<?php echo lang('common_items_sold', '', array(), TRUE); ?>: <?php echo to_quantity($number_of_items_sold); ?>

<?php
foreach ($payments as $payment_id => $payment) { ?>

<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format() . ' ' . get_time_format(), strtotime($payment->payment_date)) : lang('common_payment', '', array(), TRUE); ?>  <?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment($cart) || sale_has_partial_ebt_payment($cart)) && ($payment->payment_type == lang('common_credit', '', array(), TRUE) ||  $payment->payment_type == lang('sales_partial_credit', '', array(), TRUE) || $payment->payment_type == lang('common_ebt', '', array(), TRUE) || $payment->payment_type == lang('common_partial_ebt', '', array(), TRUE) ||  $payment->payment_type == lang('common_ebt_cash', '', array(), TRUE) ||  $payment->payment_type == lang('common_partial_ebt_cash', '', array(), TRUE))) {
																																																			echo $payment->card_issuer . ': ' . $payment->truncated_card; ?> <?php } else { ?><?php $splitpayment = explode(':', $payment->payment_type);
																																																																								echo $splitpayment[0]; ?> <?php } ?><?php echo $this->config->item('round_cash_on_sales') && $payment->payment_type == lang('common_cash', '', array(), TRUE) ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($payment->payment_amount))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($payment->payment_amount)); ?>

<?php if ($payment->entry_method) { ?>
	
<?php echo lang('sales_entry_method', '', array(), TRUE) . ': ' . H($payment->entry_method); ?>
	
<?php } ?>
<?php if ($payment->tran_type) { ?><?php echo lang('sales_transaction_type', '', array(), TRUE) . ': ' . H($payment->tran_type); ?>
	
<?php } ?>
<?php if ($payment->application_label) { ?><?php echo lang('sales_application_label', '', array(), TRUE) . ': ' . H($payment->application_label); ?>
	
<?php } ?>
<?php if ($payment->ref_no) { ?><?php echo lang('sales_ref_no', '', array(), TRUE) . ': ' . H($payment->ref_no); ?>
	
<?php } ?>
<?php if ($payment->auth_code) { ?><?php echo lang('sales_auth_code', '', array(), TRUE) . ': ' . H($payment->auth_code); ?>
	
<?php } ?>
<?php if ($payment->aid) { ?><?php echo 'AID: ' . H($payment->aid); ?>
	
<?php } ?>
<?php if ($payment->tvr) { ?><?php echo 'TVR: ' . H($payment->tvr); ?>

<?php } ?>
<?php if ($payment->tsi) { ?><?php echo 'TSI: ' . H($payment->tsi); ?>
	
<?php } ?>
<?php if ($payment->arc) { ?><?php echo 'ARC: ' . H($payment->arc); ?>
	
<?php } ?>
<?php if ($payment->cvm) { ?><?php echo 'CVM: ' . H($payment->cvm); ?>
<?php } ?>
<?php
}
?>	
<?php foreach ($payments as $payment) {
	$giftcard_payment_row = explode(':', $payment->payment_type); ?>
<?php if (strpos($payment->payment_type, lang('common_giftcard', '', array(), TRUE)) === 0) { ?><?php echo lang('sales_giftcard_balance', '', array(), TRUE); ?>  <?php echo $payment->payment_type; ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row)))); ?>
	<?php } ?>
<?php } ?>
<?php if ($amount_change >= 0) { ?>
<?php echo lang('common_change_due', '', array(), TRUE); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change)); ?>
<?php
} else {
?>
<?php echo lang('common_amount_due', '', array(), TRUE); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change * -1))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change * -1)); ?>
<?php
}
?>
<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt')) { ?>
	
<?php echo lang('common_points', '', array(), TRUE); ?>: <?php echo to_currency_no_money($customer_points); ?>
<?php } ?>

<?php if (isset($customer_balance_for_sale) && (float)$customer_balance_for_sale && !$this->config->item('hide_store_account_balance_on_receipt')) { ?>

<?php echo lang('sales_customer_account_balance', '', array(), TRUE); ?>: <?php echo to_currency($customer_balance_for_sale); ?>
<?php
}
?>
<?php
if ($ref_no) {
?>

<?php echo lang('sales_ref_no', '', array(), TRUE); ?>: <?php echo $ref_no; ?>
<?php
}
if (isset($auth_code) && $auth_code) {
?>

<?php echo lang('sales_auth_code', '', array(), TRUE); ?>: <?php echo H($auth_code); ?>
<?php
}
?>
<?php if ($show_comment_on_receipt == 1) {
	echo H($comment);
} ?>

<?php if (!$this->config->item('hide_signature')) { ?>
<?php if ($signature_needed) { ?>
			
<?php echo lang('sales_signature', '', array(), TRUE); ?>: 
------------------------------------------------



<?php
		if ($is_credit_card_sale) {
			echo $sales_card_statement;
		}
?><?php } ?><?php } ?>
<?php if ($return_policy) {
	echo wordwrap(H($return_policy), 40);
} ?></script>
<?php
if (isset($standalone) && $standalone) {
	$this->load->view("partial/footer_standalone");
	echo '<div style="page-break-after: always">&nbsp;</div>';
} else {
	$this->load->view("partial/footer");
}
?>