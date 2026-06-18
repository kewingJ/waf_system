<?php
	  require("../includes/config.php");
    require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
?>
        <script type="text/javascript">
            $(document).ready(function(){
                var line = new Morris.Line({
                    element          : 'line-chart',
                    resize           : true,
                    data             : [
                    <?php
                        $consult = mysqli_query($link,"SELECT fecha_bloqueo_ip2 AS fecha, COUNT(*) AS total
                                                FROM bloqueo_ip
                                                WHERE bloqueo_ip.fecha_bloqueo_ip BETWEEN '$fecha1' AND '$fecha2'
                                                GROUP BY fecha_bloqueo_ip2
                                                ORDER BY fecha_bloqueo_ip2 ASC");
                            
                        while($rows = mysqli_fetch_array($consult))
                        {
                            $fecha_bloqueo_ip2 = $rows['fecha'];
                            $totalPorFecha = $rows['total'];

                            echo '{ y: "'.$fecha_bloqueo_ip2.'", item1: '.$totalPorFecha.'},';
                        }
                    ?>
                    ],
                    xkey             : 'y',
                    ykeys            : ['item1'],
                    labels           : ['Total ip bloquedas'],
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
            });
        </script>
