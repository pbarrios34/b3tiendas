<?php $this->load->view("partial/header"); ?>
<div id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>

<div class="panel panel-piluku">
	<div class="panel-body">
	   <h4 id="title"><?php echo lang('sales_please_swipe_credit_card_on_machine');?></h4>
	</div>
</div>
<!-- This form is not being used at all, everything is Javascript -->
<form id="formCheckout" method="post" action="http://localhost:8000/CSPIntegrationServices/Interop/rest/runTransaction">
	<?php foreach($post_data as $key=>$value) { ?>
		<?php echo form_hidden($key, $value);?>
	<?php } ?>
</form>
<!-- End from not being used -->
<?php $this->load->view("partial/footer"); ?>

<script>
delete $.ajaxSettings.headers["cache-control"];

$(document).ready(function()
{

	body = {
		parameters: "transactionType:SALE;terminalId:<?php echo $post_data['TerminalID'] ?>;invoice:<?php echo $post_data['InvoiceNo'] ?>;totalAmount:<?php echo $post_data['Purchase'] ?>"
	}

	$.ajax("http://localhost:8000/CSPIntegrationServices/Interop/rest/runTransaction", {
		type: 'POST',
		data: JSON.stringify(body),
		contentType: 'application/json; charset=utf-8',
		success: function(data, textStatus, jqXHR)
		{

			runTransactionResult = JSON.parse(data.runTransactionResult);
			console.log(runTransactionResult);

			processed_data = [];
			Object.entries(runTransactionResult).forEach(entry => {
				const [key, value] = entry;
				processed_data.push({
					'name': key, 
					'value': value
				});
			});
			console.log(processed_data);
				
			$.ajax(SITE_URL+"/sales/set_sequence_no_emv", {
				type: 'POST',
				data: { sequence_no: runTransactionResult.authorizationNumber },
				success: function(data, textStatus, jqXHR)
				{
					post_submit('<?php echo site_url('sales/finish_cc_processing'); ?>', 'POST', processed_data);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					post_submit('<?php echo site_url('sales/finish_cc_processing'); ?>', 'POST', processed_data);
				}			
			});
			//post_submit('<?php echo site_url('sales/finish_cc_processing'); ?>', 'POST', processed_data);
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(errorThrown);
			$("#title").html("<span class='text-danger'> " + <?php echo json_encode(lang('sales_unable_to_connect_to_credit_card_terminal')); ?> + "</span>");
			$("#status").html("<a class='btn btn-primary btn-lg m-b-20' href='<?php echo site_url('sales'); ?>'>&laquo; <?php echo lang('sales_register'); ?>");
			//post_submit('<?php echo site_url('sales/finish_cc_processing'); ?>', 'POST', processed_data);
		},
		cache: true,
		headers: { 'Invoke-Control': '<?php echo $invoke_control;?>' }			
	});

});
</script>