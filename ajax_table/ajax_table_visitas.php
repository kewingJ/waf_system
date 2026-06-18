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
$table = 'visita_dominio_group';
 
// Table's primary key
$primaryKey = 'id_visita';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id_visita',     'dt' => 0 ),
    array( 'db' => 'fecha_visita',  'dt' => 1 ),
    array( 'db' => 'ip_visita',    'dt' => 2,
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
    array( 'db' => 'dominio',       'dt' => 3 ),
    array( 'db' => 'total',         'dt' => 4 )
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );

$domainKey = isset($_GET['domain_key']) ? strtolower(trim($_GET['domain_key'])) : '';
if (strpos($domainKey, 'www.') === 0) {
    $domainKey = substr($domainKey, 4);
}

$whereResult = null;
if ($domainKey !== '' && preg_match('/^[a-z0-9.-]+$/', $domainKey)) {
    $domainKeySql = addslashes($domainKey);
    $whereResult = "(LOWER(TRIM(dominio)) = '".$domainKeySql."' OR LOWER(TRIM(dominio)) = 'www.".$domainKeySql."')";
}

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $whereResult )
);
