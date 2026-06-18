<?php
	  require("../includes/config.php");
      require("../includes/security.php");

	$server_name = isset($_POST['server_name']) ? clean(trim($_POST['server_name'])) : '';
    $server_label = isset($_POST['server_label']) ? trim($_POST['server_label']) : '';
    $fecha1 = isset($_POST['fecha1']) ? clean(trim($_POST['fecha1'])) : '';
	$fecha2 = isset($_POST['fecha2']) ? clean(trim($_POST['fecha2'])) : '';

    $server_key = strtolower($server_name);
    if (strpos($server_key, 'www.') === 0) {
        $server_key = substr($server_key, 4);
    }
    $server_key = trim($server_key);

    if ($server_label === '') {
        if ($server_key !== '') {
            $server_label = 'www.' . $server_key;
        } else {
            $server_label = 'Todos los host';
        }
    }

    if($fecha1 != '' && $fecha2 != ''){
        $fecha_condition = "AND bloqueo.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'";
    } else {
        $fecha_condition = "";
    }

    $server_condition = "";
    if ($server_key !== '' && preg_match('/^[a-z0-9.-]+$/', $server_key)) {
        $server_key_sql = mysqli_real_escape_string($link, $server_key);
        $server_condition = "AND (LOWER(TRIM(bloqueo.server)) = '".$server_key_sql."' OR LOWER(TRIM(bloqueo.server)) = 'www.".$server_key_sql."')";
    }
?>
        <!-- grafica nueva -->
        <script>
            $(document).ready(function () {
                Highcharts.chart('containerPastelTipoAtaqueDomino', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: 'Tipos de Ataques por Dominio: <?php echo htmlspecialchars($server_label, ENT_QUOTES, 'UTF-8'); ?>'
                      },
                      tooltip: {
                        pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
                      },
                      plotOptions: {
                        pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                          }
                        }
                      },
                      series: [{
                        name: 'Otros tipos de ataques',
                        colorByPoint: true,
                        data: [
                        <?php
                            $total_bloqueos_other = 0;
                            $consult = mysqli_query($link,"SELECT r.id_rule, r.nombre_rule, r.inicio_rule, COUNT(bloqueo.id_bloqueo) AS total_bloqueos
                                                            FROM rules r
                                                            LEFT JOIN detalle_rule
                                                                ON detalle_rule.id_rule = r.id_rule
                                                               AND detalle_rule.activo_r = 1
                                                            LEFT JOIN bloqueo
                                                                ON bloqueo.idN = detalle_rule.numero_rule_detalle
                                                               AND bloqueo.activo_bloqueo = 1
                                                               $server_condition
                                                               $fecha_condition
                                                            WHERE r.activo_rule = 1
                                                            GROUP BY r.id_rule, r.nombre_rule, r.inicio_rule
                                                            ORDER BY r.id_rule ASC");

                            while($rows = mysqli_fetch_array($consult))
                            {
                                $total_bloqueos = (int) $rows['total_bloqueos'];

                                if($rows['inicio_rule'] == 0){
                                  //optener el total
                                  $total_bloqueos_other += $total_bloqueos;
                                } else {
                                    echo '["'.$rows['nombre_rule'].'", '.$total_bloqueos.' ],';
                                }
                            }
                            //para los otros tipos de ataques
                            echo '["Otras Reglas", '.$total_bloqueos_other.' ]';
                        ?>
                        ]
                      }]
                    });
            });
        </script>

        <!-- grafica anterior -->
        <!-- <script type="text/javascript">
                    var line = new Morris.Line({
                        element          : 'line-chart2',
                        resize           : true,
                        data             : [
                         <?php
                            // $consultBloqueo = mysqli_query($link,"SELECT DISTINCT CAST(bloqueo.fecha_bloqueo AS DATE) AS fecha FROM bloqueo WHERE bloqueo.server LIKE '%$server_name'");
                                        
                            // while($rowsBloqueo = mysqli_fetch_array($consultBloqueo))
                            // {
                            //     $fecha_bloqueo = $rowsBloqueo['fecha'];
                            //     $consultTotalBloqueo = mysqli_query($link,"SELECT * FROM bloqueo
                            //                             WHERE CAST(bloqueo.fecha_bloqueo AS DATE) = '$fecha_bloqueo'");
                            //     $totalPorFechaBloqueo = mysqli_num_rows($consultTotalBloqueo);

                            //     echo '{ y: "'.$fecha_bloqueo.'", item1: '.$totalPorFechaBloqueo.'},';
                            // }
                        ?>
                        ],
                        xkey             : 'y',
                        ykeys            : ['item1'],
                        labels           : ['Total Ataques'],
                        lineColors       : ['#0a63a4'],
                        lineWidth        : 2,
                        hideHover        : 'auto',
                        gridTextColor    : "#888888",
                        gridStrokeWidth  : 0.4,
                        pointSize        : 4,
                        pointStrokeColors: ['#0a63a4'],
                        gridLineColor    : "#888888",
                        gridTextFamily   : 'Open Sans',
                        gridTextSize     : 10
                    });
        </script> -->
