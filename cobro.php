<?php
/**
 * Mi Tienda Pagadito
 *
 * Es un ejemplo de plataforma de e-commerce, que realiza venta de productos
 * electrónicos, y efectúa los cobros utilizando Pagadito, a través del WSPG.
 *
 * cobro.php
 *
 * Este script procesa la transacción a petición del script index.php. Se
 * comunica con el WSPG para conectarse y registrar la transacción.
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

if(isset($_POST["cantidad1"]) && is_numeric($_POST["cantidad1"]) && isset($_POST["cantidad2"]) && is_numeric($_POST["cantidad2"]) && isset($_POST["cantidad3"]) && is_numeric($_POST["cantidad3"]))
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
                     * procedemos a definir los detalles de la venta, para ello
                     * definimos el siguiente arreglo.
                     */
                    $token = $data_response->value;
                    $details = array();
                    if($_POST["cantidad1"]>0)
                    {
                        $details[] =
                            array(
                                "quantity"      => $_POST["cantidad1"],
                                "description"   => $_POST["descripcion1"],
                                "price"         => $_POST["precio1"],
                                "url_product"   => $_POST["url1"]
                            );
                    }
                    if($_POST["cantidad2"]>0)
                    {
                        $details[] =
                            array(
                                "quantity"      => $_POST["cantidad2"],
                                "description"   => $_POST["descripcion2"],
                                "price"         => $_POST["precio2"],
                                "url_product"   => $_POST["url2"]
                            );
                    }
                    if($_POST["cantidad3"]>0)
                    {
                        $details[] =
                            array(
                                "quantity"      => $_POST["cantidad3"],
                                "description"   => $_POST["descripcion3"],
                                "price"         => $_POST["precio3"],
                                "url_product"   => $_POST["url3"]
                            );
                    }

                    /*
                     * A continuación, procedemos a consumir la operación
                     * exec_trans, para solicitar al WSPG que registre nuestra
                     * transacción. Para ello le enviamos token, ern, amount y
                     * details. Alternativamente, le enviamos el formato en el
                     * que queremos que nos responda el WSPG, en este ejemplo
                     * solicitamos el formato PHP.
                     *
                     * A manera de ejemplo el ern es generado como un número
                     * aleatorio entre 1000 y 2000. Lo ideal es que sea una
                     * referencia almacenada por el Pagadito Comercio.
                     */
                    $params = array(
                        "token"         => $token,
                        "ern"           => rand(1000,2000),
                        "amount"        => $_POST["cantidad1"] * $_POST["precio1"] + $_POST["cantidad2"] * $_POST["precio2"] + $_POST["cantidad3"] * $_POST["precio3"],
                        "details"       => json_encode($details),
                        "format_return" => "php"
                    );
                    $response = $oSoap->call('exec_trans', $params);
                    $data_response = unserialize($response);

                    /*
                     * Debido a que el WSPG nos puede devolver diversos mensajes
                     * de respuesta, validamos el tipo de mensaje que nos
                     * devuelve.
                     */
                    switch($data_response->code)
                    {
                        case "PG1002":
                            /*
                             * En caso de haberse registrado la transacción
                             * exitosamente, redireccionamos al usuario a la
                             * URL devuelta por el WSPG.
                             */
                            header("Location: $data_response->value");
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
                        case "PG3004":
                            /*
                             * Tratamiento para monto desigual
                             */
                        case "PG3006":
                            /*
                             * Tratamiento para monto excedido.
                             */
                        case "PG3007":
                            /*
                             * Tratamiento para acceso denegado.
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
            alert('No ha llenado los campos adecuadamente.');
            location.href = 'index.php';
        </script>
    ";
}

?>
