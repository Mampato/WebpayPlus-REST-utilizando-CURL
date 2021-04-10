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

$token_ws = isset($_REQUEST['token_ws']) ? $_REQUEST['token_ws'] : NULL;

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
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
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
$status			= $resp['status'];
$buy_order		= $resp['buy_order'];
$amount			= $resp['amount'];
$vci			= $resp['vci'];
$sessionId		= $resp['session_id'];
$authorizationCode	= $resp['authorization_code'];
$paymentTypeCode	= $resp['payment_type_code'];
$responseCode		= $resp['response_code'];
$installmentsNumber	= $resp['installments_number'];
$cardNumber		= $resp['card_detail']['card_number'];
$accountingDate		= $resp['accounting_date'];
$transactionDate	= $resp['transaction_date'];

/** Es EXTREMADAMENTE RECOMENDABLE que aquí guardes en tu DB los datos obtenidos. */
/** Puedes utilizar el $buy_order (ID Compra) para actualizar el registro. */

if($status==='AUTHORIZED'){ // El valor "AUTHORIZED" es lo mínimo para validar un pago, es RECOMENDABLE que además utilices otras validaciones como, por ejemplo, el $amount acá recibido contra el $monto que guardaste al crear la transacción. 

	/** TRANSACCIÓN APROBADA */
	$estado = 'APROBADA';

} else {

	/** TRANSACCIÓN RECHAZADA */
	$estado = 'RECHAZADA';
}

/** Para el ejemplo de captura utilizamos el $amount restándole 500 */
$capture_amount = $amount-500;
?>
<html lang="es">
<head>
	<title>Ejemplo Webpay Plus REST</title>
	<link rel="stylesheet" href="estilo.css">
</head>
<body>
	<fieldset style="width: 50%">
		<legend>PÁGINA DE RETORNO</legend>
		<div class="campos">
			<p>
				<span class="campoNombre">RESULTADO:</span><span class="campoValor"><?= $estado ?></span>
			</p>
			<p>
				<span class="campoNombre">VER:</span><span class="campoValor"><a href="ver-estado.php?token_ws=<?= $token_ws ?>" class="rojo">VER TRANSACCIÓN</a></span>
			</p>
			<p>
				<span class="campoNombre">CAPTURA:</span><span class="campoValor"><a href="capturar.php?token_ws=<?= $token_ws ?>&commerce_code=<?= $webpayplus_codigo_de_comercio ?>&buy_order=<?= $buy_order ?>&authorization_code=<?= $authorizationCode ?>&capture_amount=<?= $capture_amount ?>" class="rojo">CAPTURAR TRANSACCIÓN</a></span>
			</p>
			<p>
				<span class="campoNombre">NUEVA:</span><span class="campoValor"><a href="index.php" class="verde">CREAR NUEVA TRANSACCIÓN DE PRUEBA</a></span>
			</p>
		</div>
	</fieldset>
</body>
</html>
