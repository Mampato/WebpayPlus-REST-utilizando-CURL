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
$amount = isset($_GET['amount']) ? $_GET['amount'] : NULL;

/** Insertamos los datos de configuración. Debes modificar los valores según tu comercio. */
include ('configuracion.php');

/** Generamos la URL completa para consumir el servicio */
$url_transaccion = $webpayplus_url.$token_ws.'/refunds';

/** Generamos el arreglo con los datos a enviar */
$data_a_enviar = json_encode(
	array(
		'amount'		=> $amount
	)
);

/** Generamos el header con las credenciales obtenidas desde el archivo "webpay/configuracion.php" */
$header = array();
$header[] = 'Tbk-Api-Key-Id: '.$webpayplus_codigo_de_comercio;
$header[] = 'Tbk-Api-Key-Secret: '.$webpayplus_api_key;
$header[] = 'Content-Type: application/json';

/** Consumimos el servicio. */
$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_a_enviar);
curl_setopt($curl, CURLOPT_URL, $url_transaccion);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($curl);

if(curl_errno($curl)){
	throw new Exception(curl_error($curl));
}

$resp = json_decode($result,true);

curl_close($curl);

/** IMPORTANTE: Estos datos son devueltos sólo si el "type" es "NULLIFIED" */
$type				= $resp['type'];
$authorization_code	= $resp['authorization_code'];
$authorization_date	= $resp['authorization_date'];
$nullified_amount	= $resp['nullified_amount'];
$balance			= $resp['balance'];
$response_code		= $resp['response_code'];

/** Generamos valores para este ejemplo. */
if($type==='NULLIFIED' OR $type==='REVERSED'){
	$estado = 'REVERSADA';
} else {
	$estado = 'SIN REVERSA';
}

/** Mensaje de error */
$error_message		= $resp['error_message'];
if(!empty($error_message)){
	$estado = $error_message;
}
?>
<html lang="es">
<head>
	<title>Ejemplo Webpay Plus REST</title>
	<link rel="stylesheet" href="estilo.css">
</head>
<body>
	<fieldset style="width: 50%">
		<legend>REVERSA DE TRANSACCIÓN</legend>
		<div class="campos">
			<p>
				<span class="campoNombre">RESULTADO:</span><span class="campoValor"><?= $estado ?></span>
			</p>
			<p>
				<span class="campoNombre">RESPONSE:</span><span class="campoValor"><? echo '<pre>'; print_r($resp); echo '</pre>'; ?></span>
			</p>
			<p>
				<span class="campoNombre">NUEVA:</span><span class="campoValor"><a href="index.php" class="verde">CREAR NUEVA TRANSACCIÓN DE PRUEBA</a></span>
			</p>
		</div>
	</fieldset>
</body>
</html>