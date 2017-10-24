<?php
/**
 * Mi Tienda Pagadito
 *
 * Es un ejemplo de plataforma de e-commerce, que realiza venta de productos
 * electrónicos, y efectúa los cobros utilizando Pagadito, a través del WSPG.
 *
 * payback.php
 * 
 * Este script recibe la redirección desde Pagadito una vez la transacción ha
 * sido finalizada. Se conecta al WSPG y consulta el estado de la transacción.
 *
 * LICENCIA: Éste código fuente es de uso libre. Su comercialización no está
 * permitida. Toda publicación o mención del mismo, debe ser referenciada a
 * su autor original Pagadito.com.
 *
 * @author      Pagadito.com <soporte@pagadito.com>
 * @copyright   Copyright (c) 2011, Pagadito.com
 * @version     1.0
 * @link        https://dev.pagadito.com/index.php?mod=docs&hac=wspg
 */

/**
 * Se incluye el script config.php que contiene las constantes de conexión.
 * También se incluye la librería nusoap.php para realizar la conexión y consumo
 * del WSPG.
 */
require_once('config.php');
require_once('lib/nusoap.php');

if(isset($_GET["token"]) && $_GET["token"] != "")
{
    /*
     * Lo primero es crear el objeto nusoap_client, al que se le pasa como
     * parámetro la URL de Conexión definida en la constante WSPG
     */
    $oSoap = new nusoap_client(WSPG);
    
    $err = $oSoap->getError();
    if (!$err)
    {
        /*
         * Lo siguiente será consumir la operación 'connect', a la cual le
         * pasaremos el UID y WSK para solicitarle un token de conexión al WSPG.
         * Alternativamente le enviamos el formato en el que queremos que nos
         * responda el WSPG, en este ejemplo solicitamos el formato PHP.
         */
        $params = array(
            "uid"           => UID,
            "wsk"           => WSK,
            "format_return" => "php"
        );
        $response = $oSoap->call('connect', $params);
        $data_response = unserialize($response);

        if (!$oSoap->fault)
        {
            /*
             * Debido a que el WSPG nos puede devolver diversos mensajes de
             * respuesta, validamos el tipo de mensaje que nos devuelve.
             */
            switch($data_response->code)
            {
                case "PG1001":
                    /*
                     * En caso de haber recibido un token exitosamente,
                     * procedemos a consumir la operación 'get_status'
                     * enviándole al WSPG el token de conexión y el token
                     * recibido por GET, que es el que consultaremos.
                     */
                    $token = $data_response->value;

                    $params = array(
                        "token"         => $token,
                        "token_trans"   => $_GET["token"],
                        "format_return" => "php"
                    );
                    $response = $oSoap->call('get_status', $params);
                    $data_response = unserialize($response);

                    /*
                     * Debido a que el WSPG nos puede devolver diversos mensajes
                     * de respuesta, validamos el tipo de mensaje que nos
                     * devuelve.
                     */
                    switch($data_response->code)
                    {
                        case "PG1003":
                            /*
                             * En caso de haberse obtenido el estado de la
                             * transacción exitosamente, validamos el estado
                             * devuelto.
                             */
                            switch ($data_response->value["status"])
                            {
                                case "COMPLETED":
                                    /*
                                     * Tratamiento para una transacción exitosa.
                                     */
                                    $msg = "Gracias por comprar en Mi Tienda Pagadito.<br /><br />Referencia: ".$data_response->value["reference"]."<br />Fecha: ".$data_response->value["date_trans"];
                                    break;
                                case "REGISTERED":
                                    /*
                                     * Tratamiento para una transacción aún en
                                     * proceso.
                                     */
                                    $msg = "La transacci&oacute;n a&uacute;n est&aacute; en proceso.";
                                    break;
                                case "FAILED":
                                    /*
                                     * Tratamiento para una transacción fallida.
                                     */
                                default:
                                    /*
                                     * Por ser un ejemplo, se muestra un mensaje
                                     * de error fijo.
                                     */
                                    $msg = "Lo sentimos, la compra no pudo realizarse.";
                                    break;
                            }
                            break;
                        case "PG2001":
                            /*
                             * Tratamiento para datos incompletos.
                             */
                        case "PG3002":
                            /*
                             * Tratamiento para error.
                             */
                        case "PG3003":
                            /*
                             * Tratamiento para transacción no registrada.
                             */
                        case "PG3007":
                            /*
                             * Tratamiento para acceso denegado.
                             */
                        default:
                            /*
                             * Por ser un ejemplo, se muestra un mensaje
                             * de error fijo.
                             */
                            $msg = "Lo sentimos, ha ocurrido un problema :/";
                            break;
                    }
                    break;
                case "PG2001":
                    /*
                     * Tratamiento para datos incompletos.
                     */
                case "PG3001":
                    /*
                     * Tratamiento para conexión dengada.
                     */
                case "PG3002":
                    /*
                     * Tratamiento para error.
                     */
                case "PG3005":
                    /*
                     * Tratamiento para conexión deshabilitada.
                     */
                default:
                    /*
                     * Por ser un ejemplo, se muestra en una ventana
                     * emergente el código y mensaje de la respuesta
                     * del WSPG
                     */
                    echo "
                        <SCRIPT>
                            alert(\"$data_response->code: $data_response->message\");
                            location.href = 'index.php';
                        </SCRIPT>
                    ";
                    break;
            }
        }
        else
        {
            /*
             * Por ser un ejemplo, se muestra en una ventana emergente el
             * mensaje de error devuelto por el objeto oSoap.
             */
            echo "
                <SCRIPT>
                    alert('".$oSoap->getError()."');
                    location.href = 'index.php';
                </SCRIPT>
            ";
        }
    }
    else
    {
        /*
         * Por ser un ejemplo, se muestra en una ventana emergente el mensaje de
         * error devuelto por el objeto oSoap.
         */
        echo "
            <SCRIPT>
                alert('".$err."');
                location.href = 'index.php';
            </SCRIPT>
        ";
    }
}
else
{
    echo "
        <script>
            alert('No se han recibido los datos correctamente.');
            location.href = 'index.php';
        </script>
    ";
}

?>
<html>
    <head>
        <title>Mi Tienda Pagadito</title>
        <link type="text/css" rel="stylesheet" href="css/default.css" media="screen" />
    </head>
    <body>
        <div id="topBar">
            <div id="center">
                <div id="site"></div>
                <div class="clear"></div>
            </div>
        </div>
        <div id="wrapper">
            <div id="header">
                <img id="logo" src="imagenes/logo.png" title="Mi Tienda Pagadito" alt="Mi Tienda Pagadito" />
                <p>
                    Es un <u>ejemplo</u> de plataforma de e-commerce, que
                    realiza venta de productos electr&oacute;nicos, y efect&uacute;a los cobros
                    utilizando Pagadito, a trav&eacute;s del WSPG.
                </p>
            </div>
            <div id="content">
                <br />
                <?=$msg?>
                <br />
                <a href="index.php">Volver a comprar</a>
            </div>
        </div>
    </body>
</html>