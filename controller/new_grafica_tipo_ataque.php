<?php
	require("../includes/config.php");
    require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
?>
        <script>
            $(document).ready(function () {
                Highcharts.chart('containerPastelTipoAtaque', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: ''
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
                            $consult = mysqli_query($link,"SELECT r.id_rule, r.nombre_rule, r.inicio_rule, COUNT(b.id_bloqueo) AS total_bloqueos
                                                            FROM rules r
                                                            LEFT JOIN detalle_rule dr
                                                                ON dr.id_rule = r.id_rule
                                                               AND dr.activo_r = 1
                                                            LEFT JOIN bloqueo b
                                                                ON b.idN = dr.numero_rule_detalle
                                                               AND b.activo_bloqueo = 1
                                                               AND b.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'
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
                                // if($total_bloqueos > 0){
                                //     echo '{ name: "'.$rows['nombre_rule'].'", y:'.$total_bloqueos.'},';
                                // }
                            }
                            //para los otros tipos de ataques
                            echo '["Otras Reglas", '.$total_bloqueos_other.' ]';
                        ?>
                        ]
                      }]
                    });
            });
        </script>
