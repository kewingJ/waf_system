<?php
    require("../../includes/config.php");

    $consult = mysqli_query($link,"SELECT * FROM rules WHERE rules.activo_rule = 1");
    $j = 0;
    $total = 0;
    //optener el total
    while($rows = mysqli_fetch_array($consult))
    {
        $id_rule = $rows['id_rule'];
        //optener totales de bloqueos por reglas
        $consult2 = mysqli_query($link,"SELECT * FROM bloqueo 
                                        INNER JOIN detalle_rule 
                                        ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                        INNER JOIN rules 
                                        ON detalle_rule.id_rule = rules.id_rule 
                                        WHERE bloqueo.activo_bloqueo = 1 AND rules.id_rule = '$id_rule'");
        $total += mysqli_num_rows($consult2);
    }
    //echo $total;

    //
    $consultPrimaria = mysqli_query($link,"SELECT * FROM rules WHERE rules.activo_rule = 1");
    while($rows = mysqli_fetch_array($consultPrimaria))
    {
        $total_bloqueos = 0;
        $id_rule = $rows['id_rule'];
        //optener totales de bloqueos por reglas
        $consult3 = mysqli_query($link,"SELECT * FROM bloqueo 
                                        INNER JOIN detalle_rule 
                                        ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                        INNER JOIN rules 
                                        ON detalle_rule.id_rule = rules.id_rule 
                                        WHERE bloqueo.activo_bloqueo = 1 AND rules.id_rule = '$id_rule'");
        $total_bloqueos = mysqli_num_rows($consult3);
        $nombreAtaque = $rows['nombre_rule'];
        if ($nombreAtaque == "INTERNAL RULES") {
            $nombreAtaque = "IR";
        }

        if ($nombreAtaque == "Directory traversal") {
            $nombreAtaque = "DR";
        }

        if ($nombreAtaque == "Evading tricks") {
            $nombreAtaque = "ET";
        }

        if ($nombreAtaque == "File uploads") {
            $nombreAtaque = "File";
        }
                            
        if ($total_bloqueos > 0) {
            //calcular % de total
            $porcentaje = ((float)$total_bloqueos * 100) / $total; // Regla de tres
            $porcentaje = number_format(round($porcentaje, 2), 1);  // Quitar los decimales
            //$porcentaje2 = round($porcentaje, 0);
            $arr_waf[$j] = array('tipo_ataque'=>$nombreAtaque,
                                'total_bloqueos'=>$total_bloqueos,
                                'porcentaje'=>$porcentaje);
            $j++;
        }
    }
    die(json_encode($arr_waf)); 
?>