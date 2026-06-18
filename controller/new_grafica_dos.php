<?php
	  require("../includes/config.php");
      require("../includes/security.php");

	$fecha1 = mysqli_real_escape_string($link, $_POST['fecha1']);
	$fecha2 = mysqli_real_escape_string($link, $_POST['fecha2']);
?>
        <script type="text/javascript">
            // $(document).ready(function(){
                // $(document).on('click', '#ataquesGrafica', function(e){
                    // e.preventDefault();
                    var line = new Morris.Line({
                        element          : 'line-chart2',
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
                // });
            // });
        </script>
