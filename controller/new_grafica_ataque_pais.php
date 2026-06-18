<?php
	  require("../includes/config.php");
      require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
?>
        <ul class="chart-legend clearfix f16 dosColumnas">
            <?php
                //
                $consult = mysqli_query($link,"SELECT COUNT(*) AS total, id_pais, iso, nombre_pais, fecha_bloqueo_pais
                                                FROM bloqueo_pais_rango
                                                WHERE fecha_bloqueo_pais BETWEEN '$fecha1' AND '$fecha2'
                                                GROUP BY id_pais
                                                ORDER BY total DESC");
                                
                while($rows = mysqli_fetch_array($consult))
                {
                    $nombre_pais = $rows['nombre_pais'];
                    $total_bloqueos = $rows['total'];
                    $codigo_p = $rows['iso'];
                    //
                    if ($total_bloqueos > 0) {
                        echo '
                            <li class="estiloFila">
                                <i class="flag '.strtolower($codigo_p).'""></i>
                                <span>
                                    <a href="bloqueo_pais_rango.php?pais!='.$nombre_pais.'&fecha1!='.$fecha1.'&fecha2!='.$fecha2.'">'
                                    .$nombre_pais.' ('.$total_bloqueos.')
                                    </a>
                                </span>
                            </li>
                        '; 
                    }
                }
            ?>
        </ul>
        
        <script type="text/javascript">
                    var line = new Morris.Line({
                        element          : 'line-chart3',
                        resize           : true,
                        data             : [
                         <?php
                            $consultBloqueo = mysqli_query($link,"SELECT DATE(bloqueo.fecha_bloqueo) AS fecha, COUNT(*) AS total
                                                                    FROM bloqueo
                                                                    WHERE bloqueo.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'
                                                                    GROUP BY DATE(bloqueo.fecha_bloqueo)
                                                                    ORDER BY fecha ASC");
                                        
                            while($rowsBloqueo = mysqli_fetch_array($consultBloqueo))
                            {
                                $fecha_bloqueo = $rowsBloqueo['fecha'];
                                $totalPorFechaBloqueo = $rowsBloqueo['total'];

                                echo '{ y: "'.$fecha_bloqueo.'", item1: '.$totalPorFechaBloqueo.'},';
                            }
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
        </script>

        <script>
            Highcharts.chart('container3', {
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
                        pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
                      },
                      plotOptions: {
                        pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y:.0f}',
                            style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                          }
                        }
                      },
                      series: [{
                        name: '',
                        colorByPoint: true,
                        point:{
                            events:{
                                click: function (event) {
                                    //alert(this.name);
                                    //setTimeout("location.href = 'bloqueo_pais.php?pais!="+this.name+"'",100);
                                    setTimeout("location.href = 'tipo_bloqueo.php?tipo!="+this.name+"&fecha1!=<?php echo $fecha1; ?>&fecha2!=<?php echo $fecha2; ?>'",100);
                                }
                            }
                        },
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
        </script>
