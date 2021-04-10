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

/** Insertamos los datos de configuración. Debes modificar los valores según tu comercio. */
include ('configuracion.php');

/** 
 * Para este ejemplo utilizamos los valores enviados desde el formulario del inicio.
 * Al integrar en tu tienda debes utilizar los valores generados en el checkout.
 */

$id_compra = isset($_POST['id_compra']) ? $_POST['id_compra'] : NULL; // Orden de compra de la tienda. Este número debe ser único para cada transacción. Largo máximo: 26. La orden de compra puede tener: Números, letras, mayúsculas y minúsculas, y los signos |_=&%.,~:/?[+!@()>-

$valor_uso_interno = isset($_POST['valor_uso_interno']) ? $_POST['valor_uso_interno'] : NULL; // Identificador de sesión, uso interno de comercio, este valor es devuelto al final de la transacción. Largo máximo: 61

$monto = isset($_POST['monto']) ? $_POST['monto'] : NULL; // Monto de la transacción. Máximo 2 decimales para USD. Largo máximo: 17

$url_retorno = 'https://bastidas.cl/webpayplus-rest/retorno.php'; // URL del comercio, a la cual Webpay redireccionará posterior al proceso de autorización. Largo máximo: 256

/** Generamos la URL completa para consumir el servicio */
$url_transaccion = $webpayplus_url;

/** Generamos el arreglo con los datos a enviar */
$data_a_enviar = json_encode(
	array(
		'buy_order'		=> $id_compra,
		'session_id'	=> $valor_uso_interno,
		'amount'		=> $monto,
		'return_url'	=> $url_retorno
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

$token 	= $resp['token']; // Token de la transacción. Largo: 64.
$url 	= $resp['url']; // URL de formulario de pago Webpay. Largo máximo: 255.

/** Si recibimos la URL y el TOKEN, iniciamos el flujo de pago. */
if (!empty($url) AND !empty($token)) {
	/** Es EXTREMADAMENTE RECOMENDABLE que aquí guardes el $id_compra, $token y $monto en tu DB */
	/** ya que te serán de mucha ayuda cuando valides y actualices el estado de la transacción. */

	/** Enviamos el TOKEN a la URL recibida. */ 
	?>
	<!DOCTYPE html>
	<html>
	<body>
		<form action="<?= $url ?>" method="post" name="FORM_WEBPAY">
			<input type="hidden" name="token_ws" value="<?= $token ?>">
		</form>
		<script language="JavaScript">
			document.FORM_WEBPAY.submit();
		</script>
	</body>
	</html>
	<?php
	exit;
} else {
	/** Si hubo un error al generar el TOKEN, enviamos al cliente a la página de error. */
	header('Location: https://bastidas.cl/webpayplus-rest/pagina-de-error-en-el-pago.php');
	exit;
}
?>