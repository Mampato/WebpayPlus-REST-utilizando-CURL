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

/** Estado actual de la pasarela. Los valores permitidos son "INTEGRACION" o "PRODUCCION". */
/** Recuerda que antes de pasar a "PRODUCCION" debes ingresar las credenciales de producción. */
$wpp_estado = 'INTEGRACION';

/** Ingresa aquí las credenciales del comercio en producción */
$wpp_codigo_comercio    = 597000000000; // Debe ser un INT. Formato: 5970XXXXXXXX
$wpp_api_key            = 'API-KEY-DEL-COMERCIO'; // La API KEY secreta asignada al comercio.

/** Lo más probable es que no necesites modificar nada más desde aquí. */
$wpp_ruta_servicio = '/rswebpaytransaction/api/webpay/v1.0/transactions/';
switch ($wpp_estado) {
    case 'INTEGRACION':
    $webpayplus_url = 'https://webpay3gint.transbank.cl'.$wpp_ruta_servicio;
    $webpayplus_codigo_de_comercio = 597055555532;
    $webpayplus_api_key = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
    break;
    case 'PRODUCCION':
    $webpayplus_url = 'https://webpay3g.transbank.cl'.$wpp_ruta_servicio;
    $webpayplus_codigo_de_comercio = $wpp_codigo_comercio;
    $webpayplus_api_key = $wpp_api_key;
    break;
    default:
    $webpayplus_url = 'https://webpay3gint.transbank.cl'.$wpp_ruta_servicio;
    $webpayplus_codigo_de_comercio = 597055555532;
    $webpayplus_api_key = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
    break;
}
?>