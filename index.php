<?php
/**
 * Ejemplo de integración de Webpay Plus REST utilizando CURL.
 *
 * IMPORTANTE: Este NO es un desarrollo oficial de Transbank Webpay Plus.
 * Tiene como propósito sólo entregar un ejemplo básico de integración.
 * Se permite todo tipo de modificaciones y personalización de este código.
 * Sólo se pide mantener el crédito del autor original.
 *
 * Requerido:
 * PHP 7.2+
 * CURL
 * OPENSSL
 *
 * @author Patricio Bastidas Bustos <pbastidasbustos@gmail.com>
 *
 ***************************************************************************/

/** 
 * El siguiente formulario nos permite generar transacciones de prueba.
 * Es muy útil para conocer todos los pasos del proceso, pero lo más probable
 * es que en tu integración utilices el archivo "crear-transaccion.php" para
 * iniciar el proceso de pago de tu tienda.
 */


/** Generamos unos valores aleatorios para la prueba. */
$id_compra = random_int(10000, 99999); // Orden de compra de la tienda. Este número debe ser único para cada transacción. Largo máximo: 26. La orden de compra puede tener: Números, letras, mayúsculas y minúsculas, y los signos |_=&%.,~:/?[+!@()>-

$valor_uso_interno = random_int(10000, 99999); // Identificador de sesión, uso interno de comercio, este valor es devuelto al final de la transacción. Largo máximo: 61

$monto = random_int(1000, 10000); // Monto de la transacción. Máximo 2 decimales para USD. Largo máximo: 17
?>
<html lang="es">
<head>
	<title>Ejemplo Webpay Plus REST</title>
	<link rel="stylesheet" href="estilo.css">
</head>
<body>
	<form name="form" method="POST" action="crear-transaccion.php" style="width: 50%" novalidate>
		<fieldset>
			<legend>DATOS DE TRANSACCIÓN DE PRUEBA</legend>
			<div class="campos">
				<p>
					<span class="campoNombre">ID ÚNICO DE COMPRA:</span><input type="text" size="25" name="id_compra" value="<?= $id_compra ?>">
				</p>
				<p>
					<span class="campoNombre">VALOR PARA USO INTERNO:</span><input type="text" size="25" name="valor_uso_interno" value="<?= $valor_uso_interno ?>">
				</p>
				<p>
					<span class="campoNombre">MONTO:</span><input type="text" size="25" name="monto" value="<?= $monto ?>">
				</p>
			</div>
		</fieldset>
		<input type="submit" id="submit" value="ENVIAR"/>
	</form>
</body>
</html>