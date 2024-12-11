<h1>Integracion</h1>
<img src="/assets/img/bac/bac_logo.png" width="200">
<p>Bienvenido al area administrativa de la integracion con BAC CREDOMATIC para el procesamiento de tarjetas de credito en Guatemala<p>
<p>Tienda: <b><?php echo $location->name; ?></b></p>
<p>Caja Registradora: <b><?php echo $register->name; ?></b></p>
<p>ID de Terminal: <b><?php echo $register->emv_terminal_id; ?></b></p>
<p>Si la identificacion del a terminal es incorrecta, debe configurarla en la seccion de "Tiendas > Caja" y tener abierta la caja correcta.</p>
<ul>
	<!--<li><?php echo anchor("bac/sale",'<i class="icon ti-credit-card"></i><span class="text">Venta</span>', array('tabindex' => '-1')); ?></li>
	<li><?php echo anchor("bac/refund",'<i class="icon ti-credit-card"></i><span class="text">Devolución</span>', array('tabindex' => '-1')); ?></li>
	<li><?php echo anchor("bac/batch_inquiry",'<i class="icon ti-credit-card"></i><span class="text">Reporte de Cierre</span>', array('tabindex' => '-1')); ?></li>-->
	<li><?php echo anchor("bac/batch",'<i class="icon ti-credit-card"></i><span class="text">Reporte de Cierre y Cierre</span>', array('tabindex' => '-1')); ?></li>
</ul>
<?php 

	
	
?>