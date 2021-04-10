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
$commerce_code = isset($_GET['commerce_code']) ? $_GET['commerce_code'] : NULL;
$buy_order = isset($_GET['buy_order']) ? $_GET['buy_order'] : NULL;
$authorization_code = isset($_GET['authorization_code']) ? $_GET['authorization_code'] : NULL;
$capture_amount = isset($_GET['capture_amount']) ? $_GET['capture_amount'] : NULL;

/** Insertamos los datos de configuración. Debes modificar los valores según tu comercio. */
include ('configuracion.php');

/** Generamos la URL completa para consumir el servicio */
$url_transaccion = $webpayplus_url.$token_ws.'/capture';

/** Generamos el arreglo con los datos a enviar */
$data_a_enviar = json_encode(
	array(
		'commerce_code'			=> $commerce_code,
		'buy_order'				=> $buy_order,
		'authorization_code'	=> $authorization_code,
		'capture_amount'		=> $capture_amount
	)
);

/** Generamos el header con las credenciales obtenidas desde el archivo "webpay/configuracion.php" */
$header = array();
$header[] = 'Tbk-Api-Key-Id: '.$webpayplus_codigo_de_comercio;
$header[] = 'Tbk-Api-Key-Secret: '.$webpayplus_api_key;
$header[] = 'Content-Type: application/json';

/** Consumimos el servicio. */
$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_a_enviar);
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
$token				= $resp['token'];
$authorization_code	= $resp['authorization_code'];
$authorization_date	= $resp['authorization_date'];
$captured_amount	= $resp['captured_amount'];
$response_code		= $resp['response_code'];

if($response_code===0){
	$estado = 'CAPTURA EXITOSA';
} else {
	$estado = 'CAPTURA RECHAZADA';
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
		<legend>CAPTURA DE TRANSACCIÓN</legend>
		<div class="campos">
			<p>
				<span class="campoNombre">RESULTADO:</span><span class="campoValor"><?= $estado ?></span>
			</p>
			<p>
				<span class="campoNombre">RESPONSE:</span><span class="campoValor"><? echo '<pre>'; print_r($resp); echo '</pre>'; ?></span>
			</p>
			<p>
				<span class="campoNombre">VER:</span><span class="campoValor"><a href="ver-estado.php?token_ws=<?= $token_ws ?>" class="rojo">VER TRANSACCIÓN</a></span>
			</p>
			<p>
				<span class="campoNombre">NUEVA:</span><span class="campoValor"><a href="index.php" class="verde">CREAR NUEVA TRANSACCIÓN DE PRUEBA</a></span>
			</p>
		</div>
	</fieldset>
</body>
</html>