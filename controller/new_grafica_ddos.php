<?php
	require("../includes/config.php");
    require("../includes/security.php");

	$fecha1 = $_POST['fecha1'];
	$fecha2 = $_POST['fecha2'];
?>
        <script>
            $(document).ready(function () {
                Highcharts.chart('containerPastelDdos', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: 'Grafico de ataques ddos'
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
                                }
                            }
                        },
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM grafica_ddos WHERE fecha_ddos BETWEEN '$fecha1' AND '$fecha2'");
                            //optener el total de reglas
                            $total = mysqli_num_rows($consult);
                            $i = 1;
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $total_bloqueos = $rows['total_ddos'];
                                $ip_ddos = $rows['ip_ddos'];
                            
                                echo '{ name: "'.$rows['ip_ddos'].'", y:'.$total_bloqueos.' },';
                            }
                            ?>
                        ]
                      }]
                    });
            
                    Highcharts.chart('containerPastelDdos', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: 'Grafico de ddos por pais'
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
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT COUNT(*) AS total_bloqueo_ddos_pais, id_pais, iso, nombre_pais, fecha_bloqueo_pais FROM bloqueo_ddos_pais_rango 
                                                                    WHERE fecha_bloqueo_pais BETWEEN '$fecha1' AND '$fecha2'
                                                                    GROUP BY id_pais");
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $nombre_pais = $rows['nombre_pais'];
                                $total_bloqueos = $rows['total_bloqueo_ddos_pais'];
                                $codigo_p = $rows['iso'];
                            
                                if ($total_bloqueos > 0) {
                                    echo '{ name: "'.$nombre_pais.'", y:'.$total_bloqueos.'},';
                                }
                            }
                        ?>
                        ]
                      }]
                });
            });
        </script>