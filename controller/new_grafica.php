<?php
	require("../includes/config.php");
    require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
?>

<script>
            $(document).ready(function(){
                Highcharts.chart('container5', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: 'Grafico de tipos de ataques'
                      },
                      tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
                        name: '',
                        colorByPoint: true,
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT r.nombre_rule, COUNT(b.id_bloqueo) AS total_bloqueos
                                                            FROM rules r
                                                            LEFT JOIN detalle_rule dr
                                                                ON dr.id_rule = r.id_rule
                                                               AND dr.activo_r = 1
                                                            LEFT JOIN bloqueo b
                                                                ON b.idN = dr.numero_rule_detalle
                                                               AND b.activo_bloqueo = 1
                                                               AND b.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'
                                                            WHERE r.activo_rule = 1
                                                            GROUP BY r.id_rule, r.nombre_rule
                                                            ORDER BY r.id_rule ASC");
                            while($rows = mysqli_fetch_array($consult))
                            {
                                echo '{ name: "'.$rows['nombre_rule'].'", y:'.$rows['total_bloqueos'].' },';
                            }
                            ?>
                        ]
                      }]
                    });
            });
        </script>
