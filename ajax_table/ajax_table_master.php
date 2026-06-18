<?php
	include_once '../includes/config.php';
    include_once '../geoIp/geoiploc.php';
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'bloqueo_master';
 
// Table's primary key
$primaryKey = 'id_bloqueo_master';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id_bloqueo_master', 'dt' => 0 ),
    array( 'db' => 'fecha_bloqueo', 'dt' => 1 ),
    array( 'db' => 'ip_bloqueda',    'dt' => 2,
        'formatter' => function( $d, $row ) {
            $resultado = "";
            $cadena = $d;
            $ip_bandera = trim($cadena);
            if (!empty($ip_bandera)) {
                $codigo_ip = getCountryFromIP($ip_bandera, "code");
                $resultado = '<span class="f16"><i class="flag '.strtolower($codigo_ip).' icono-bandera"></i></span> '.$ip_bandera;
            } else{
                $resultado = '<span class="f16"><i class="flag ac icono-bandera"></i></span> '.$ip_bandera;
            }
            return $resultado;
        } 
    ),
    array( 'db' => 'tipo_ataque',   'dt' => 3 )
);
 
// SQL server connection information
// $sql_details = array(
//     'user' => 'root',
//     'pass' => '',
//     'db'   => 'waf',
//     'host' => 'localhost'
// );
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);