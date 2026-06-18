<?php
    require("../../includes/config.php");

    $consult = mysqli_query($link,"SELECT * FROM bloqueo_pais
                                    INNER JOIN paises
                                    ON bloqueo_pais.id_pais = paises.id_pais");
    $j = 0;
    $total = 0;
    //optener el total de ataques
    while($rows = mysqli_fetch_array($consult))
    {
        $totalBloqueos = $rows['total_bloqueo'];

        if ($totalBloqueos > 50) {
            $total += $rows['total_bloqueo'];
        }
    }

    $consulta = mysqli_query($link,"SELECT * FROM bloqueo_pais
                                    INNER JOIN paises
                                    ON bloqueo_pais.id_pais = paises.id_pais");
    while($rows = mysqli_fetch_array($consulta))
    {
        $nombrePais = $rows['nombre'];
        $totalBloqueos = $rows['total_bloqueo'];
        $codigoPais = $rows['iso'];
                            
        if ($totalBloqueos > 50) {
            //calcular % de total
            $porcentaje = ((float)$totalBloqueos * 100) / $total; // Regla de tres
            $porcentaje = round($porcentaje, 0);  // Quitar los decimales
            if ($porcentaje != 0) {
                $arr_waf[$j] = array('pais'=>$nombrePais,
                                'codigo_pais'=>$codigoPais,
                                'total_bloqueo'=>$porcentaje);
                $j++;
            }
        }
    }
    die(json_encode($arr_waf)); 
?>