<?php $this->load->view("partial/header"); ?>
<?php $this->load->view("bac/bac_menu"); ?>
<div id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>

<div class="panel panel-piluku">
	<div class="panel-body">
	   <h4 id="title"><?php echo lang('sales_please_swipe_credit_card_on_machine');?></h4>
	</div>
</div>
<script type="text/javascript">
delete $.ajaxSettings.headers["cache-control"];
$(document).ready(function()
{	
	var endpoint = "<?php echo $endpoint; ?>";
	saleFunction(endpoint);

	function saleFunction(endpoint)
	{
		$('#result').html('');
	 	$.ajax({
			url: endpoint,
			type: "POST",
			// headers: {"X-Api-Key": "kcw8kksc08gcoc4w4gsgwowwcc8ks8swok4socw0"},
			contentType: 'application/json',
			processData: false,
			data: JSON.stringify({ 
				"parameters": "transactionType:SALE;terminalId:<?php echo $register->emv_terminal_id; ?>;invoice:<?php echo $invoice; ?>;totalAmount:<?php echo $totalAmount; ?>"
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
				post_submit('<?php echo site_url('sales/finish_cc_processing'); ?>', 'POST', processed_data);
  			},
			error: function(result) 
			{
				$("#title").html("<span class='text-danger'> " + <?php echo json_encode(lang('sales_unable_to_connect_to_credit_card_terminal')); ?> + "</span>");
				$("#status").html("<a class='btn btn-primary btn-lg m-b-20' href='<?php echo site_url('sales'); ?>'>&laquo; <?php echo lang('sales_register'); ?>");

    			$("#result").html(result.runTransactionResult);
				Object.entries(res).forEach(entry => {
					const [key, value] = entry;
					$("#result").append("<li>" + key + ": " + value + "</li>");
				});
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
<div id="result"></div>

<?php $this->load->view("partial/footer"); ?>