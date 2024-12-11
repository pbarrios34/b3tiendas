<?php $this->load->view("partial/header"); ?>
<?php $this->load->view("bac/bac_menu"); ?>

<div class="panel panel-piluku">
	<div id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>
</div>
<script type="text/javascript">
delete $.ajaxSettings.headers["cache-control"];
$(document).ready(function()
{	
	var endpoint = "<?php echo $endpoint; ?>";
	refundFunction(endpoint);

	function refundFunction(endpoint)
	{
		$('#result').html('');
	 	$.ajax({
			url: endpoint,
			type: "POST",
			// headers: {"X-Api-Key": "kcw8kksc08gcoc4w4gsgwowwcc8ks8swok4socw0"},
			contentType: 'application/json',
			processData: false,
			data: JSON.stringify({ 
				"parameters": "transactionType:VOID;terminalId:<?php echo $register->emv_terminal_id; ?>;systemTraceNum:<?php echo $systemTraceNumber; ?>;referenceNumber:<?php echo $referenceNumber; ?>;authorizationNumb:<?php echo $authorizationCode; ?>"
			}),
			success: function(result) 
			{
				var res = JSON.parse(result.runTransactionResult);
				processed_data = [];
				
				Object.entries(res).forEach(entry => {
					const [key, value] = entry;
					$("#result").append("<li><b>" + key + "</b>: " + value + "</li>");
					processed_data.push({
						'name': key, 
						'value': value
					});
				});
				
				if  (res.responseCode =='00')
				{
					show_feedback('success',result.message,<?php echo json_encode(lang('sales_successfully_deleted')); ?>);
					post_submit('<?php echo site_url('sales/bac_void_receipt/' . 1 . '/' . $sale_id); ?>', 'POST', true);
				}	
				else
				{
					show_feedback('error',result.message,<?php echo json_encode(lang('sales_delete_unsuccessful')); ?>);
					post_submit('<?php echo site_url('sales/bac_void_receipt/' . 0 . '/' . $sale_id); ?>', 'POST', false);
				}
				
			},
			error: function(result) 
			{
				$("#title").html("<span class='text-danger'> " + <?php echo json_encode(lang('sales_unable_to_connect_to_credit_card_terminal')); ?> + "</span>");
				$("#status").html("<a class='btn btn-primary btn-lg m-b-20' href='<?php echo site_url('sales'); ?>'>&laquo; <?php echo lang('sales_register'); ?>");
				$("#rnotification").html('<div class="widget-box"><div class="widget-title"></div><div class="widget-content nopadding"><h1 class="text-danger text-center" style="font-size:100px;"><i class="ion-trash-b"></i></h1><div class="alert alert-danger text-center"><h4><strong><?php echo lang('sales_delete_unsuccessful'); ?></strong></h4><?php echo anchor('sales/receipt/'.$sale_id, lang('sales_receipt'), array('target' =>'_blank')); ?></div></div></div>');
    			$("#result").html(result.runTransactionResult);
				Object.entries(res).forEach(entry => {
					const [key, value] = entry;
					$("#result").append("<li>" + key + ": " + value + "</li>");
				});
				show_feedback('error',result.message,<?php echo json_encode(lang('sales_delete_unsuccessful')); ?>);
  			}
		});
	}
});

</script>
<div class="manage_buttons">
	<!-- Css Loader  -->
	<div class="spinner" id="ajax-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
</div>

<div id="rnotification"></div>
<div id="result"></div>

<?php $this->load->view("partial/footer"); ?>