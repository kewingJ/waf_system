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
 
$nombre = $_GET['busqueda!'];
// DB table to use
$table = <<<EOT
 (
    SELECT * FROM visita_dominio_group
    WHERE activo_visita = 1 AND dominio = '$nombre'
 ) temp
EOT;
 
// Table's primary key
$primaryKey = 'id_visita';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id_visita',     'dt' => 0 ),
    array( 'db' => 'fecha_visita',  'dt' => 1 ),
    array( 'db' => 'ip_visita',     'dt' => 2 ),
    array( 'db' => 'dominio',       'dt' => 3 ),
    array( 'db' => 'total',         'dt' => 4 )
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);