<?php
	  require("../includes/config.php");
      require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
    $dominios = array();
    $consultDominios = mysqli_query($link,"SELECT server, COUNT(*) AS total
                                            FROM bloqueo
                                            WHERE bloqueo.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'
                                              AND server <> ''
                                            GROUP BY server
                                            ORDER BY total DESC");
    while($rowDominio = mysqli_fetch_array($consultDominios)){
        $caracter = substr(trim($rowDominio['server']), -1);
        if ($caracter != '"') {
            $dominios[] = array(
                'server' => $rowDominio['server'],
                'total' => (int) $rowDominio['total'],
            );
        }
    }
?>

        <!-- grafica de barra de dominios -->
        <script type="text/javascript">
            //$(document).on('click','#graficoDominio',function() {
                Highcharts.chart('containerBar', {

                chart: {
                    type: 'column'
                },

                title: {
                    text: 'Ataques por dominios'
                },

                subtitle: {
                    text: 'Dominios'
                },

                xAxis: {
                    categories: [
                    <?php
                        foreach($dominios as $dominioData){
                            echo '"'.addslashes($dominioData['server']).'",';
                        }
                    ?>
                    ],
                    title: {
                        text: 'Dominios'
                    }
                },

                yAxis: {
                    title: {
                        text: 'Total ataques'
                    },
                },

                tooltip: {
                    pointFormat: 'Total ataques: <b>{point.y:.0f}</b>'
                },

                series: [
                  <?php
                    $total_dominios = count($dominios);
                    $position = 1;
                    foreach($dominios as $dominioData){
                        $total_bloqueo_dominio = $dominioData['total'];
                        $nombre_dominio = addslashes($dominioData['server']);

                        echo '{ name: "'.$nombre_dominio.'",';
                        echo 'data: [';

                        for($i = 1; $i <= $total_dominios; $i++){
                            if ($i != $position) {
                                echo 'null,';
                            } else {
                                echo $total_bloqueo_dominio.',';
                            }
                        }
                        echo '] },';

                        $position++;
                    }
                  ?>
                ]

                });
            //});
        </script>
