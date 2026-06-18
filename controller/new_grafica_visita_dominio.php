<?php
	require("../includes/config.php");
    require("../includes/security.php");

	$fecha1 = $_POST['fecha1'];
	$fecha2 = $_POST['fecha2'];
?>

        <script>
            $(document).ready(function(){
                Highcharts.chart('containerBarDominio', {

                    chart: {
                        type: 'column'
                    },

                    title: {
                        text: 'Visitas por dominio'
                    },

                    subtitle: {
                        text: 'Dominios'
                    },

                    xAxis: {
                        categories: [
                        <?php
                            $consult = mysqli_query($link,"SELECT
                                                            LOWER(
                                                                TRIM(
                                                                    IF(
                                                                        LOWER(TRIM(dominio)) LIKE 'www.%',
                                                                        SUBSTRING(TRIM(dominio), 5),
                                                                        TRIM(dominio)
                                                                    )
                                                                )
                                                            ) AS dominio_key,
                                                            MAX(CASE WHEN LOWER(TRIM(dominio)) LIKE 'www.%' THEN TRIM(dominio) ELSE '' END) AS dominio_www,
                                                            MAX(CASE WHEN LOWER(TRIM(dominio)) NOT LIKE 'www.%' THEN TRIM(dominio) ELSE '' END) AS dominio_plain,
                                                            SUM(total_visita) as total
                                                        FROM grafica_visitas
                                                        WHERE fecha_visita BETWEEN '$fecha1' AND '$fecha2'
                                                        GROUP BY dominio_key
                                                        ORDER BY dominio_key");
                            while($row = mysqli_fetch_array($consult)){
                                $nombre_dominio = addslashes(!empty($row['dominio_www']) ? $row['dominio_www'] : $row['dominio_plain']);
                                echo '"'.$nombre_dominio.'",';
                            }
                        ?>
                        ],
                        title: {
                            text: 'Dominios'
                        }
                    },

                    yAxis: {
                        title: {
                            text: 'Total visitas'
                        },
                    },

                    tooltip: {
                        pointFormat: 'Total visitas: <b>{point.y:.0f}</b>'
                    },

                    series: [
                        <?php
                            $consult = mysqli_query($link,"SELECT
                                                            LOWER(
                                                                TRIM(
                                                                    IF(
                                                                        LOWER(TRIM(dominio)) LIKE 'www.%',
                                                                        SUBSTRING(TRIM(dominio), 5),
                                                                        TRIM(dominio)
                                                                    )
                                                                )
                                                            ) AS dominio_key,
                                                            MAX(CASE WHEN LOWER(TRIM(dominio)) LIKE 'www.%' THEN TRIM(dominio) ELSE '' END) AS dominio_www,
                                                            MAX(CASE WHEN LOWER(TRIM(dominio)) NOT LIKE 'www.%' THEN TRIM(dominio) ELSE '' END) AS dominio_plain,
                                                            SUM(total_visita) as total
                                                        FROM grafica_visitas
                                                        WHERE fecha_visita BETWEEN '$fecha1' AND '$fecha2'
                                                        GROUP BY dominio_key
                                                        ORDER BY dominio_key");
                            $total_visita_dominio = mysqli_num_rows($consult);
                            $position = 1;
                            while($row = mysqli_fetch_array($consult))
                            {
                                $nombre_dominio = addslashes(!empty($row['dominio_www']) ? $row['dominio_www'] : $row['dominio_plain']);
                                $total_visita = $row['total'];

                                echo '{ name: "'.$nombre_dominio.'",';
                                echo 'data: [';
                                for($i = 1; $i <= $total_visita_dominio; $i++){
                                    if ($i != $position) {
                                        echo 'null,';
                                    } else {
                                        echo $total_visita.',';
                                    }
                                }
                                echo '] },';

                                $position++;
                            }
                    ?>
                    ]

                });
            });
        </script>
