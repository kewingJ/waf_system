<?php
    include_once '../includes/config.php';
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
$nombreRaw = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
// Solo se permiten caracteres válidos de hostname/dominio para prevenir SQL injection
$nombre = mysqli_real_escape_string($link, preg_replace('/[^a-zA-Z0-9.\-_]/', '', $nombreRaw));
$table = <<<EOT
 (
    SELECT * FROM bloqueo
    WHERE server LIKE '%$nombre'
 ) temp
EOT;
 
// Table's primary key
$primaryKey = 'id_bloqueo';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'log_bloqueo', 'dt' => 0 ),
    array( 'db' => 'id_bloqueo', 'dt' => 1 ),
    array( 'db' => 'fecha_bloqueo', 'dt' => 2 ),
    array( 'db' => 'ip',  'dt' => 3 ),
    array( 'db' => 'server',   'dt' => 4 ),
    array( 'db' => 'url', 'dt' => 5 ),
    array( 'db' => 'metodo', 'dt' => 6 ),
    array( 'db' => 'tipo_ataque', 'dt' => 7 ),
    array( 'db' => 'ip', 'dt' => 8 )

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