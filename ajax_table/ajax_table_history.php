<?php
	include_once '../includes/config.php';
    include_once '../geoIp/geoiploc.php';
 
    // DB table to use
    $table = 'history_vulnerability';
    
    // Table's primary key
    $primaryKey = 'id_history';
    
    // indexes
    $columns = array(
        array( 'db' => 'id_history',    'dt' => 0 ),
        array( 'db' => 'host',          'dt' => 1 ),
        array( 'db' => 'resultado',     'dt' => 2 ),
        array( 'db' => 'fecha_escaneo', 'dt' => 3 ),
    );
    
    require( 'ssp.class.php' );
    
    echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
    );