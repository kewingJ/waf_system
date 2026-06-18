<?php
	require("../includes/config.php");
    require("../includes/security.php");
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('new_grafica_principal');

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);

    $query = mysqli_query($link,"TRUNCATE grafica_consulta") or die(mysqli_error($link));

    mysqli_query(
        $link,
        "INSERT INTO grafica_consulta (fecha_bloqueo, total_waf, total_fuerza)
         SELECT fechas.fecha_bloqueo,
                COALESCE(waf.total_waf, 0),
                COALESCE(fuerza.total_fuerza, 0)
         FROM (
             SELECT DATE(fecha_bloqueo) AS fecha_bloqueo
             FROM bloqueo
             WHERE fecha_bloqueo >= '$fecha1 00:00:00' AND fecha_bloqueo <= '$fecha2 23:59:59'
             UNION
             SELECT fecha_bloqueo_ip2 AS fecha_bloqueo
             FROM bloqueo_ip
             WHERE fecha_bloqueo_ip2 BETWEEN '$fecha1' AND '$fecha2'
         ) fechas
         LEFT JOIN (
             SELECT DATE(fecha_bloqueo) AS fecha_bloqueo, COUNT(*) AS total_waf
             FROM bloqueo
             WHERE fecha_bloqueo >= '$fecha1 00:00:00' AND fecha_bloqueo <= '$fecha2 23:59:59'
             GROUP BY DATE(fecha_bloqueo)
         ) waf ON waf.fecha_bloqueo = fechas.fecha_bloqueo
         LEFT JOIN (
             SELECT fecha_bloqueo_ip2 AS fecha_bloqueo, COUNT(*) AS total_fuerza
             FROM bloqueo_ip
             WHERE fecha_bloqueo_ip2 BETWEEN '$fecha1' AND '$fecha2'
             GROUP BY fecha_bloqueo_ip2
         ) fuerza ON fuerza.fecha_bloqueo = fechas.fecha_bloqueo
         ORDER BY fechas.fecha_bloqueo ASC"
    ) or die(mysqli_error($link));
?>
        <script type="text/javascript">
            $(document).ready(function(){
                var area = new Morris.Area({
                        element: 'revenue-chart2',
                        resize: true,
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM grafica_consulta ORDER BY fecha_bloqueo ASC");
                            
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $fecha_bloqueo_waf = $rows['fecha_bloqueo'];
                                $total_bloqueo_waf = $rows['total_waf'];
                                $total_bloqueo_fue = $rows['total_fuerza'];

                                echo '{ y: "'.$fecha_bloqueo_waf.'", item1: '.$total_bloqueo_fue.', item2: '.$total_bloqueo_waf.'},';
                            }
                        ?>
                        ],
                        xkey: 'y',
                        ykeys: ['item1', 'item2'],
                        labels: ['Total Fuerza Bruta', 'Total WAF'],
                        lineColors: ['#a0d0e0', '#3c8dbc'],
                        hideHover: 'auto'
                    });
            });
        </script>
