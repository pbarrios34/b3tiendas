<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
function formated_number( number, decimal = false ) {

	let whitoutCero = parseInt(number, 10).toString();
	if( decimal ) {
		let langh = whitoutCero.length;
		let intNumber = whitoutCero.slice(0, langh - 2);
		let decimals = whitoutCero.slice(langh - 2);
		let finalNumber = intNumber + "." + decimals;
		return finalNumber;
	} else {
		return whitoutCero;
	}
}
function formated_date( date ) {
	if (typeof date !== "string") {
		date = date.toString();
	}
	
	let year = parseInt(date.substring(4));
	let month = parseInt(date.substring(0, 2));
	let day = parseInt(date.substring(2, 4));

	let dateObjet = new Date(year, month - 1, day);
	let formatedDate = dateObjet.toLocaleDateString('en-US');

	return formatedDate;
}
function formated_time( time ) {
	if (typeof time !== "string") {
		time = time.toString();
	}
	
	let hh = time.substring(0, 2);
	let mm = time.substring(2, 4);
	let ss = time.substring(4);

	let formatedTime = hh + ':' + mm + ':' + ss;

	return formatedTime;
}
$(document).ready(function()
{	
	var endpoint = "<?php echo $endpoint; ?>";
	$('#generate-batch-inquiry').click(function() {
		batchFunction('BATCH_INQUIRY');
	});

	$('#generate-batch-settlement').click(function() {
		batchFunction('BATCH_SETTLEMENT');
	});

	function batchFunction(transactionType)
	{
		$('#result').html('');
		var url = "<?php echo $endpoint; ?>";
		if ("<?php echo $dev; ?>" == "dev")
		{
			url = url + "?transactionType=" + transactionType;
		}
	 	$.ajax({
			url: url,
			type: "POST",
			// headers: {"X-Api-Key": "kcw8kksc08gcoc4w4gsgwowwcc8ks8swok4socw0"},
			contentType: 'application/json',
			processData: false,
			data: JSON.stringify({ 
				"parameters": "transactionType:" + transactionType + ";terminalId:<?php echo $register->emv_terminal_id; ?>"
			}),
			success: function(result) 
			{
/* 				var res = JSON.parse(result.runTransactionResult);
				Object.entries(res).forEach(entry => {
					const [key, value] = entry;
					$("#result").append("<li><b>" + key + "</b>: " + value + "</li>");
				}); */
				var res = JSON.parse(result.runTransactionResult);
				let mapResponse = [];
				if( Object.entries(res).length >10 ) {
					Object.entries(res).forEach(entry => {
						switch( entry[0] ) {
							case 'acqNumber':
								mapResponse['Número ACQ'] = formated_number( entry[1], false );
								break;
							case 'authorizationNumber':
								mapResponse['Número de autorización'] = formated_number( entry[1], false );
								break;
							case 'cardBrand':
								mapResponse['Linea de marca'] = entry[1];
								break;
							case 'hostDate':
								mapResponse['Fecha de emisión'] = formated_date(entry[1]);
								break;
							case 'hostTime':
								mapResponse['Hora de emisión'] = formated_time(entry[1]);
								break;
							case 'refundsAmount':
								mapResponse['Cantidad devuelta'] = formated_number( entry[1], true );
								break;
							case 'refundsTransactions':
								mapResponse['Devoluciones en transacción'] = formated_number( entry[1], true );
								break;
							case 'responseCode':
								mapResponse['Código de respuesta'] = formated_number( entry[1] );
								break;
							case 'salesAmount':
								mapResponse['Monto de venta'] = formated_number( entry[1], true );
								break;
							case 'salesTax':
								mapResponse['Monto de impuestos'] = formated_number( entry[1], true );
								break;
							case 'salesTip':
								mapResponse['Propinas'] = formated_number( entry[1], true );
								break;
							case 'salesTransactions':
								mapResponse['Monto en transacciones'] = formated_number( entry[1], true );
								break;
							case 'currencyVoucher':
								mapResponse['Voucher'] = entry[1];
								break;
							case 'TerminalDisplayLine1Voucher':
								mapResponse['Line 1'] = entry[1];
								break;
							case 'TerminalDisplayLine2Voucher':
								mapResponse['Line 2'] = entry[1];
								break;
							case 'TerminalDisplayLine3Voucher':
								mapResponse['Dirección de emisión'] = entry[1];
								break;
							case 'signature':
								mapResponse['Firma'] = entry[1];
								break;
							case 'trnTotalTime':
								mapResponse['Tiempo de emisión'] =  entry[1]/1000 + ' Seg';
								break;
						}
					});
					Object.entries(mapResponse).forEach( item => {
						$("#result").append("<li><b>" + item[0] + "</b>: " + item[1] + "</li>");
					} );
				} else {
					Object.entries(res).forEach(entry => {
						switch( entry[0] ) {
							case 'acqNumber':
								mapResponse['Número ACQ'] = formated_number( entry[1] );
								break;
							case 'responseCode':
								mapResponse['Código de respuesta'] = entry[1];
								break;
							case 'responseCodeDescription':
								mapResponse['Descripción de la respuesta'] = entry[1];
								break;
							case 'currencyVoucher':
								mapResponse['Moneda'] = entry[1];
								break;
							case 'TerminalDisplayLine1Voucher':
								mapResponse['Linea 1'] = entry[1];
								break;
							case 'TerminalDisplayLine2Voucher':
								mapResponse['Linea 2'] = entry[1];
								break;
							case 'TerminalDisplayLine3Voucher':
								mapResponse['Dirección de emisión'] = entry[1];
								break;
							case 'signature':
								mapResponse['Firma'] = entry[1];
								break;
							case 'trnTotalTime':
								mapResponse['Tiempo de emisión'] = entry[1]/1000 + ' Seg';
								break;
						}
					});
					Object.entries(mapResponse).forEach( item => {
						$("#result").append("<li><b>" + item[0] + "</b>: " + item[1] + "</li>");
					} );
				}

				logResponse(res);
  			},
			error: function(result) 
			{
    			$("#result").html(result.runTransactionResult);
				Object.entries(res).forEach(entry => {
					const [key, value] = entry;
					$("#result").append("<li>" + key + ": " + value + "</li>");
				});
  			}
		});
	}
	function logResponse(response) {
		const csvData = `${response.acqNumber},${response.authorizationNumber},${response.cardBrand},${response.hostDate},${response.hostTime},${response.refundsAmount},${response.refundsTransactions},${response.responseCode},${response.salesAmount},${response.salesTax},${response.salesTip},${response.salesTransactions},${response.currencyVoucher},${response.TerminalDisplayLine1Voucher},${response.TerminalDisplayLine2Voucher},${response.TerminalDisplayLine3Voucher},${response.signature},${response.trnTotalTime}\n`;

		$("#csvRecords").append(csvData);
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

</div>

<div style="background: white; padding: 20px 30px;">
	
<?php $this->load->view("bac/bac_menu"); ?>

<h2>Reporte de Cierre</h2>

<a id="generate-batch-inquiry" class="btn btn-success">Generar Reporte</a>
<a id="generate-batch-settlement" class="btn btn-warning">Hacer Cierre</a>
<ul id="result"></ul>
<p>&nbsp;</p>

</div>
<div id="csvRecords">
	<?php
		if (isset($_POST['csvData'])) {
			$csvData = $_POST['csvData'];
			$csvFilePath = 'application/views/bac/card_payment_logs.csv';
			$file = fopen($csvFilePath, 'a');
		
			fwrite($file, $csvData);
			fclose($file);
			echo 'Datos CSV guardados correctamente.';
		}
	?>
</div>
<?php $this->load->view("partial/footer"); ?>