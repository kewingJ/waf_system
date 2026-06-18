<?php
	include_once '../includes/config.php';
    include_once '../geoIp/geoiploc.php';
 
    // DB table to use
    $table = 'threat_ip_list';
    
    // Table's primary key
    $primaryKey = 'id_threat';
    
    // indexes
    $columns = array(
        array( 'db' => 'id_threat',     'dt' => 0 ),
        array( 'db' => 'ip',            'dt' => 1,
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
        array( 'db' => 'hits',          'dt' => 2 ),
        array( 'db' => 'source_file',   'dt' => 3,
        'formatter' => function( $d, $row ) {
            $resultado = "";
            $cadena = $d;
            $partes = explode('.', $cadena);
            $resultado = $partes[0];
            $resultado = $resultado == 'apache' ? 'Web' : $resultado;
            return $resultado;
        }
     ),
    );
    
    require( 'ssp.class.php' );
    
    echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
    );