<?php
/**
 * Mi Tienda Pagadito
 *
 * Es un ejemplo de plataforma de e-commerce, que realiza venta de productos
 * electrónicos, y efectúa los cobros utilizando Pagadito, a través del WSPG.
 *
 * index.php
 *
 * Es el script de la página inicial de Mi Tienda Pagadito. Aquí se muestran
 * los productos que se encuentran a la venta.
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
                <form action="cobro.php" method="post">
                    <!--
                        Se define 1 campo oculto con el ern de la transacción. Este deberá ser único
                        por cada transacción. A manera de ejemplo se define un hash aleatorio.
                    -->
                    <input type="hidden" value="<?=md5("mitiendapagadito".rand(1000,2000).time())?>" name="ern" />
                    <div class="producto">
                        <!-- Se definen 2 campos ocultos con el precio y la descripción del producto 1 -->
                        <input type="hidden" value="Monitor LCD 17 pulgadas, con resoluciones hasta 1280 x 800 pixeles." name="descripcion1" />
                        <input type="hidden" value="65.00" name="precio1" />
                        <input type="hidden" value="http://demo.pagadito.com/mitiendapagadito/index.php?item=1" name="url1" />
                        <img src="imagenes/monitor.jpg" title="Monitor" alt="Monitor" />
                        <h2>Monitor</h2>
                        <p>Monitor LCD 17 pulgadas, con resoluciones hasta 1280 x 800 pixeles.</p>
                        <strong>$65.00</strong>
                        <br />
                        <br />
                        <div class="cantidad">
                            Cantidad <input type="text" size="2" value="0" name="cantidad1" />
                        </div>
                    </div>
                    <div class="producto">
                        <!-- Se definen 2 campos ocultos con el precio y la descripción del producto 2 -->
                        <input type="hidden" value="Laptop Core 2 Duo, 4GB de RAM, 160GB HHDD." name="descripcion2" />
                        <input type="hidden" value="900.00" name="precio2" />
                        <input type="hidden" value="http://demo.pagadito.com/mitiendapagadito/index.php?item=2" name="url2" />
                        <img src="imagenes/laptop.jpg" title="Laptop" alt="Laptop" />
                        <h2>Laptop</h2>
                        <p>Laptop Core 2 Duo, 4GB de RAM, 160GB HHDD.</p>
                        <br />
                        <strong>$900.00</strong>
                        <br />
                        <br />
                        <div class="cantidad">
                            Cantidad <input type="text" size="2" value="0" name="cantidad2" />
                        </div>
                    </div>
                    <div class="producto">
                        <!-- Se definen 2 campos ocultos con el precio y la descripción del producto 3 -->
                        <input type="hidden" value="Teclado ergonomico codificacion latina y teclas de funciones especiales." name="descripcion3" />
                        <input type="hidden" value="20.00" name="precio3" />
                        <input type="hidden" value="http://demo.pagadito.com/mitiendapagadito/index.php?item=3" name="url3" />
                        <img src="imagenes/teclado.jpg" title="Teclado" alt="Teclado" />
                        <h2>Teclado</h2>
                        <p>Teclado ergon&oacute;mico codificaci&oacute;n latina y teclas de funciones especiales.</p>
                        <strong>$20.00</strong>
                        <br />
                        <br />
                        <div class="cantidad">
                            Cantidad <input type="text" size="2" value="0" name="cantidad3" />
                        </div>
                    </div>
                    <input type="submit" value="Comprar" id="btnComprar" />
                </form>
            </div>
        </div>
    </body>
</html>
