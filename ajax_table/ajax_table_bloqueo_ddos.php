<?php
	include_once '../includes/config.php';
    include_once '../geoIp/geoiploc.php';
 
// DB table to use
$table = 'bloqueo_ddos';
 
// Table's primary key
$primaryKey = 'id_bloqueo_ddos';
 
// indexes
$columns = array(
    array( 'db' => 'id_bloqueo_ddos',       'dt' => 0 ),
    array( 'db' => 'fecha_ddos',            'dt' => 1 ),
    array( 'db' => 'ip_ddos',               'dt' => 2 ),
    array( 'db' => 'codigo_pais',           'dt' => 3,
        'formatter' => function( $d, $row ) {
            $resultado = "";
            $codigo_ip = $d;
            $resultado = '<span class="f16"><i class="flag '.strtolower($codigo_ip).' icono-bandera"></i></span>';
            return $resultado;
        } 
    ),
    array( 'db' => 'total_coneccion',       'dt' => 4 ),
    array( 'db' => 'id_bloqueo_ddos',       'dt' => 5 ),
    array( 'db' => 'lista_blanca',          'dt' => 6 )
);
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);