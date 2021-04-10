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

$token_ws = isset($_GET['token_ws']) ? $_GET['token_ws'] : NULL;

/** Insertamos los datos de configuración. Debes modificar los valores según tu comercio. */
include ('configuracion.php');

/** Generamos la URL completa para consumir el servicio */
$url_transaccion = $webpayplus_url.$token_ws;

/** Generamos el header con las credenciales obtenidas desde el archivo "webpay/configuracion.php" */
$header = array();
$header[] = 'Tbk-Api-Key-Id: '.$webpayplus_codigo_de_comercio;
$header[] = 'Tbk-Api-Key-Secret: '.$webpayplus_api_key;
$header[] = 'Content-Type: application/json';

/** Consumimos el servicio. */
$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_URL, $url_transaccion);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($curl);

if(curl_errno($curl)){
	throw new Exception(curl_error($curl));
}

$resp = json_decode($result,true);

curl_close($curl);

/** Obtenemos los datos desde la respuesta. */
$status				= $resp['status'];
$buy_order			= $resp['buy_order'];
$amount				= $resp['amount'];
$vci				= $resp['vci'];
$sessionId			= $resp['session_id'];
$authorizationCode	= $resp['authorization_code'];
$paymentTypeCode	= $resp['payment_type_code'];
$responseCode		= $resp['response_code'];
$installmentsNumber	= $resp['installments_number'];
$cardNumber			= $resp['card_detail']['card_number'];
$accountingDate		= $resp['accounting_date'];
$transactionDate	= $resp['transaction_date'];

/** Generamos valores para este ejemplo. */
if($status==='AUTHORIZED'){
	$estado = 'APROBADA';
} else {
	$estado = 'RECHAZADA';
}
?>
<html lang="es">
<head>
	<title>Ejemplo Webpay Plus REST</title>
	<link rel="stylesheet" href="estilo.css">
</head>
<body>
	<fieldset style="width: 50%">
		<legend>ESTADO DE LA TRANSACCIÓN</legend>
		<div class="campos">
			<p>
				<span class="campoNombre">ESTADO:</span><span class="campoValor"><?= $estado ?></span>
			</p>
			<p>
				<span class="campoNombre">RESPONSE:</span><span class="campoValor"><? echo '<pre>'; print_r($resp); echo '</pre>'; ?></span>
			</p>
			<? if($status==='AUTHORIZED'){ ?>
				<p>
					<span class="campoNombre">REVERSA:</span><span class="campoValor"><a href="reversar.php?token_ws=<?= $token_ws ?>&amount=<?= $amount ?>" class="rojo">REVERSAR ESTA TRANSACCION</a></span>
				</p>
				<p>
					<span class="campoNombre">CAPTURA:</span><span class="campoValor"><a href="capturar.php?token_ws=<?= $token_ws ?>&commerce_code=<?= $webpayplus_codigo_de_comercio ?>&buy_order=<?= $buy_order ?>&authorization_code=<?= $authorizationCode ?>&capture_amount=<?= $amount ?>" class="rojo">CAPTURAR TRANSACCIÓN</a></span>
				</p>
			<? } ?>
			<p>
				<span class="campoNombre">NUEVA:</span><span class="campoValor"><a href="index.php" class="verde">CREAR NUEVA TRANSACCIÓN DE PRUEBA</a></span>
			</p>
		</div>
	</fieldset>
</body>
</html>