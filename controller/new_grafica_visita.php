<?php
	require("../includes/config.php");
    require("../includes/security.php");
    include_once '../geoIp/geoiploc.php';

	$fecha1 = isset($_POST['fecha1']) ? clean(trim($_POST['fecha1'])) : '';
	$fecha2 = isset($_POST['fecha2']) ? clean(trim($_POST['fecha2'])) : '';
    $dominio = isset($_POST['nombredominio']) ? clean(trim($_POST['nombredominio'])) : '';
    $dominio_key = strtolower($dominio);
    if (strpos($dominio_key, 'www.') === 0) {
        $dominio_key = substr($dominio_key, 4);
    }
    $dominio_key = trim($dominio_key);

    $dominio_condition = '';
    if ($dominio_key !== '' && preg_match('/^[a-z0-9.-]+$/', $dominio_key)) {
        $dominio_key_sql = mysqli_real_escape_string($link, $dominio_key);
        $dominio_condition = "AND (LOWER(TRIM(dominio)) = '".$dominio_key_sql."' OR LOWER(TRIM(dominio)) = 'www.".$dominio_key_sql."')";
    }

    if(!empty($fecha1) && !empty($fecha2) && empty($dominio_key))
    {
?>
                                        <!-- solid sales graph -->
                                        <div class="box box-solid bg-oscuro-gradient">
                                            <div class="box-header">
                                            <i class="fa fa-line-chart"></i>
                                            <h3 class="box-title">Grafica timeline visitas</h3>
                                            <div class="box-tools pull-right">
                                            </div>
                                            </div>
                                            <div class="box-body border-radius-none">
                                                <!-- Date range -->
                                                <form id="FormRangoVisitas" class="FormRangoVisitas" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaVisita">
                                                            <input type="hidden" id="nombredominio" value="<?php echo htmlspecialchars($dominio_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </form>
                                                <!-- /.form group -->
                                            <div class="chart" id="line-chart4" style="height: 250px;"></div>
                                            </div><!-- /.box-body -->

                                            <div class="overlay" id="overlayGraficaVisita" style="display: none;">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>

                                            <div class="box-footer no-border">
                                            <div class="row">
                                                <?php
                                                    // //optener rango de fechas por dia
                                                    // $actual = date("Y-m-d H:i:s");
                                                    // $pasadoAux = date("Y-m-d");
                                                    // $pasado = $pasadoAux.' 00:00:00';
                                                    
                                                    // //total de visitas dia
                                                    // $consultDia = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$pasado' AND '$actual'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalDia = mysqli_num_rows($consultDia);

                                                    // //optener rango de fechas por semana
                                                    // $hoy = date("Y-m-d H:i:s");
                                                    // $semana = date("Y-m-d H:i:s",strtotime($hoy."- 1 week"));
                                                    
                                                    // //total visitas semana
                                                    // $consultSemana = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio 
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$semana' AND '$hoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalSemana = mysqli_num_rows($consultSemana);

                                                    // //optener rango de fechas por mes
                                                    // $meshoy = date("Y-m-d H:i:s");
                                                    // $mes = date("Y-m-d H:i:s",strtotime($hoy."- 1 month"));
                                                    
                                                    // //total visita por mes
                                                    // $consultMes = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$mes' AND '$meshoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalMes = mysqli_num_rows($consultMes);
                                                ?>
                                                <!-- <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Dia</div>
                                                </div>
                                                
                                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Semana</div>
                                                </div>

                                                <div class="col-xs-4 text-center">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Mes</div>
                                                </div> -->
                                            </div><!-- /.row -->
                                            </div><!-- /.box-footer -->
                                        </div><!-- /.box -->
        <script>
            $(document).ready(function(){
                $(document).ready(function(){
                    //grafica de linea de tiempo
                    var line = new Morris.Line({
                        element          : 'line-chart4',
                        resize           : true,
                        data: [
                        <?php
                        $consult = mysqli_query($link,"SELECT CAST(fecha_visita AS DATE) AS fecha, total_visita FROM grafica_visitas_dominio
                                                WHERE fecha_visita BETWEEN '$fecha1' AND '$fecha2'");
                            
                        while($rows = mysqli_fetch_array($consult))
                        {
                            $fecha_visita = $rows['fecha'];
                            $totalPorFecha = $rows['total_visita'];

                            echo '{ y: "'.$fecha_visita.'", item1: '.$totalPorFecha.'},';
                        }
                        ?>
                        ],
                        xkey: 'y',
                        ykeys: ['item1'],
                        labels: ['Total visitas'],
                        lineColors: ['#0a63a4'],
                        lineWidth: 2,
                        hideHover: 'auto',
                        gridTextColor: "#888888",
                        gridStrokeWidth: 0.4,
                        pointSize: 4,
                        pointStrokeColors: ["#0a63a4"],
                        gridLineColor: "#888888",
                        gridTextFamily: "Open Sans",
                        gridTextSize: 10
                    });
                });
            });
        </script>
<?php
    } 
    else if(!empty($fecha1) && !empty($fecha2) && !empty($dominio_key)) 
    {
?>
                                        <!-- solid sales graph -->
                                        <div class="box box-solid bg-oscuro-gradient">
                                            <div class="box-header">
                                            <i class="fa fa-line-chart"></i>
                                            <h3 class="box-title">Grafica timeline visitas</h3>
                                            <div class="box-tools pull-right">
                                            </div>
                                            </div>
                                            <div class="box-body border-radius-none">
                                                <!-- Date range -->
                                                <form id="FormRangoVisitas" class="FormRangoVisitas" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaVisita">
                                                            <input type="hidden" id="nombredominio" value="<?php echo htmlspecialchars($dominio_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </form>
                                                <!-- /.form group -->
                                            <div class="chart" id="line-chart4" style="height: 250px;"></div>
                                            </div><!-- /.box-body -->

                                            <div class="overlay" id="overlayGraficaVisita" style="display: none;">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>

                                            <div class="box-footer no-border">
                                            <div class="row">
                                                <?php
                                                    // //optener rango de fechas por dia
                                                    // $actual = date("Y-m-d H:i:s");
                                                    // $pasadoAux = date("Y-m-d");
                                                    // $pasado = $pasadoAux.' 00:00:00';
                                                    
                                                    // //total de visitas dia
                                                    // $consultDia = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$pasado' AND '$actual' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalDia = mysqli_num_rows($consultDia);

                                                    // //optener rango de fechas por semana
                                                    // $hoy = date("Y-m-d H:i:s");
                                                    // $semana = date("Y-m-d H:i:s",strtotime($hoy."- 1 week"));
                                                    
                                                    // //total visitas semana
                                                    // $consultSemana = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio 
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$semana' AND '$hoy' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalSemana = mysqli_num_rows($consultSemana);

                                                    // //optener rango de fechas por mes
                                                    // $meshoy = date("Y-m-d H:i:s");
                                                    // $mes = date("Y-m-d H:i:s",strtotime($hoy."- 1 month"));
                                                    
                                                    // //total visita por mes
                                                    // $consultMes = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$mes' AND '$meshoy' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalMes = mysqli_num_rows($consultMes);
                                                ?>
                                                <!-- <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Dia</div>
                                                </div>
                                                
                                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Semana</div>
                                                </div>

                                                <div class="col-xs-4 text-center">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Mes</div>
                                                </div> -->
                                            </div><!-- /.row -->
                                            </div><!-- /.box-footer -->
                                        </div><!-- /.box -->
        <script>
            $(document).ready(function(){
                $(document).ready(function(){
                    //grafica de linea de tiempo
                    var line = new Morris.Line({
                        element          : 'line-chart4',
                        resize           : true,
                        data: [
                        <?php
                        $consult = mysqli_query($link,"SELECT CAST(fecha_visita AS DATE) AS fecha, SUM(total_visita) AS total_visita
                                                FROM grafica_visitas
                                                WHERE fecha_visita BETWEEN '$fecha1' AND '$fecha2'
                                                $dominio_condition
                                                GROUP BY CAST(fecha_visita AS DATE)
                                                ORDER BY fecha");
                            
                        while($rows = mysqli_fetch_array($consult))
                        {
                            $fecha_visita = $rows['fecha'];
                            $totalPorFecha = $rows['total_visita'];

                            echo '{ y: "'.$fecha_visita.'", item1: '.$totalPorFecha.'},';
                        }
                        ?>
                        ],
                        xkey: 'y',
                        ykeys: ['item1'],
                        labels: ['Total visitas'],
                        lineColors: ['#0a63a4'],
                        lineWidth: 2,
                        hideHover: 'auto',
                        gridTextColor: "#888888",
                        gridStrokeWidth: 0.4,
                        pointSize: 4,
                        pointStrokeColors: ["#0a63a4"],
                        gridLineColor: "#888888",
                        gridTextFamily: "Open Sans",
                        gridTextSize: 10
                    });
                });
            });
        </script>

<?php
    }
    else if(empty($fecha1) && empty($fecha2) && !empty($dominio_key)) {
?>

                                        <!-- solid sales graph -->
                                        <div class="box box-solid bg-oscuro-gradient">
                                            <div class="box-header">
                                            <i class="fa fa-line-chart"></i>
                                            <h3 class="box-title">Grafica timeline visitas</h3>
                                            <div class="box-tools pull-right">
                                            </div>
                                            </div>
                                            <div class="box-body border-radius-none">
                                                <!-- Date range -->
                                                <form id="FormRangoVisitas" class="FormRangoVisitas" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaVisita">
                                                            <input type="hidden" id="nombredominio" value="<?php echo htmlspecialchars($dominio_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </form>
                                                <!-- /.form group -->
                                            <div class="chart" id="line-chart4" style="height: 250px;"></div>
                                            </div><!-- /.box-body -->

                                            <div class="overlay" id="overlayGraficaVisita" style="display: none;">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>

                                            <div class="box-footer no-border">
                                            <div class="row">
                                                <?php
                                                    // //optener rango de fechas por dia
                                                    // $actual = date("Y-m-d H:i:s");
                                                    // $pasadoAux = date("Y-m-d");
                                                    // $pasado = $pasadoAux.' 00:00:00';
                                                    
                                                    // //total de visitas dia
                                                    // $consultDia = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$pasado' AND '$actual' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalDia = mysqli_num_rows($consultDia);

                                                    // //optener rango de fechas por semana
                                                    // $hoy = date("Y-m-d H:i:s");
                                                    // $semana = date("Y-m-d H:i:s",strtotime($hoy."- 1 week"));
                                                    
                                                    // //total visitas semana
                                                    // $consultSemana = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio 
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$semana' AND '$hoy' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalSemana = mysqli_num_rows($consultSemana);

                                                    // //optener rango de fechas por mes
                                                    // $meshoy = date("Y-m-d H:i:s");
                                                    // $mes = date("Y-m-d H:i:s",strtotime($hoy."- 1 month"));
                                                    
                                                    // //total visita por mes
                                                    // $consultMes = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$mes' AND '$meshoy' AND visita_dominio.dominio = '$dominio'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalMes = mysqli_num_rows($consultMes);
                                                ?>
                                                <!-- <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Dia</div>
                                                </div>
                                                
                                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Semana</div>
                                                </div>

                                                <div class="col-xs-4 text-center">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Mes</div>
                                                </div> -->
                                            </div><!-- /.row -->
                                            </div><!-- /.box-footer -->
                                        </div><!-- /.box -->
        <script>
            $(document).ready(function(){
                $(document).ready(function(){
                    //grafica de linea de tiempo
                    var line = new Morris.Line({
                        element          : 'line-chart4',
                        resize           : true,
                        data: [
                        <?php
                        $consult = mysqli_query($link,"SELECT CAST(fecha_visita AS DATE) AS fecha, SUM(total_visita) AS total_visita
                                                FROM grafica_visitas
                                                WHERE 1 = 1
                                                $dominio_condition
                                                GROUP BY CAST(fecha_visita AS DATE)
                                                ORDER BY fecha");
                            
                        while($rows = mysqli_fetch_array($consult))
                        {
                            $fecha_visita = $rows['fecha'];
                            $totalPorFecha = $rows['total_visita'];

                            echo '{ y: "'.$fecha_visita.'", item1: '.$totalPorFecha.'},';
                        }
                        ?>
                        ],
                        xkey: 'y',
                        ykeys: ['item1'],
                        labels: ['Total visitas'],
                        lineColors: ['#0a63a4'],
                        lineWidth: 2,
                        hideHover: 'auto',
                        gridTextColor: "#888888",
                        gridStrokeWidth: 0.4,
                        pointSize: 4,
                        pointStrokeColors: ["#0a63a4"],
                        gridLineColor: "#888888",
                        gridTextFamily: "Open Sans",
                        gridTextSize: 10
                    });
                });
            });
        </script>

<?php
    }
    else if(empty($fecha1) && empty($fecha2) && empty($dominio)) {
?>
                                        <!-- solid sales graph -->
                                        <div class="box box-solid bg-oscuro-gradient">
                                            <div class="box-header">
                                            <i class="fa fa-line-chart"></i>
                                            <h3 class="box-title">Grafica timeline visitas</h3>
                                            <div class="box-tools pull-right">
                                            </div>
                                            </div>
                                            <div class="box-body border-radius-none">
                                                <!-- Date range -->
                                                <form id="FormRangoVisitas" class="FormRangoVisitas" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaVisita">
                                                            <input type="hidden" id="nombredominio" value="<?php echo htmlspecialchars($dominio_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </form>
                                                <!-- /.form group -->
                                            <div class="chart" id="line-chart4" style="height: 250px;"></div>
                                            </div><!-- /.box-body -->

                                            <div class="overlay" id="overlayGraficaVisita" style="display: none;">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>
                                            
                                            <div class="box-footer no-border">
                                            <div class="row">
                                                <?php
                                                    // //optener rango de fechas por dia
                                                    // $actual = date("Y-m-d H:i:s");
                                                    // $pasadoAux = date("Y-m-d");
                                                    // $pasado = $pasadoAux.' 00:00:00';
                                                    
                                                    // //total de visitas dia
                                                    // $consultDia = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$pasado' AND '$actual'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalDia = mysqli_num_rows($consultDia);

                                                    // //optener rango de fechas por semana
                                                    // $hoy = date("Y-m-d H:i:s");
                                                    // $semana = date("Y-m-d H:i:s",strtotime($hoy."- 1 week"));
                                                    
                                                    // //total visitas semana
                                                    // $consultSemana = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio 
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$semana' AND '$hoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalSemana = mysqli_num_rows($consultSemana);

                                                    // //optener rango de fechas por mes
                                                    // $meshoy = date("Y-m-d H:i:s");
                                                    // $mes = date("Y-m-d H:i:s",strtotime($hoy."- 1 month"));
                                                    
                                                    // //total visita por mes
                                                    // $consultMes = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$mes' AND '$meshoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalMes = mysqli_num_rows($consultMes);
                                                ?>
                                                <!-- <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Dia</div>
                                                </div>
                                                
                                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Semana</div>
                                                </div>

                                                <div class="col-xs-4 text-center">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Mes</div>
                                                </div> -->
                                            </div><!-- /.row -->
                                            </div><!-- /.box-footer -->
                                        </div><!-- /.box -->

        <script>
            $(document).ready(function(){
                $(document).ready(function(){                    
                    //grafica de linea de tiempo
                    var line = new Morris.Line({
                        element          : 'line-chart4',
                        resize           : true,
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM grafica_visitas_dominio");
                                
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $fecha_visita = $rows['fecha_visita'];
                                $totalPorFechaVisita = $rows['total_visita'];

                                echo '{ y: "'.$fecha_visita.'", item1: '.$totalPorFechaVisita.'},';
                            }
                        ?>
                        ],
                        xkey: 'y',
                        ykeys: ['item1'],
                        labels: ['Total visitas'],
                        lineColors: ['#0a63a4'],
                        lineWidth: 2,
                        hideHover: 'auto',
                        gridTextColor: "#888888",
                        gridStrokeWidth: 0.4,
                        pointSize: 4,
                        pointStrokeColors: ["#0a63a4"],
                        gridLineColor: "#888888",
                        gridTextFamily: "Open Sans",
                        gridTextSize: 10
                    });
                });
            });
        </script>
<?php
    }
?>
        <!-- rango de fechas Grafica visitas-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaVisita').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');

                        var fechaUno = start.format('YYYY-MM-DD');
                        var fechaDos = end.format('YYYY-MM-DD');

                        var nombredominio = $('#nombredominio').val();
                        //alert(nombredominio);
                        $("#overlayGraficaVisita").css("display", "block");
                        $('#line-chart4').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2,
                            "nombredominio": nombredominio,
                        }
                        $.ajax({
                            url:  'controller/new_grafica_visita.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $("#overlayGraficaVisita").css("display", "none");
                            $('#line-chart4').html('');    
                            $('#line-chart4').html(data); // mostrar la data
                            GraficaDominio(fechaUno, fechaDos);
                        })
                        .fail(function(){
                            $('#line-chart4').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica de visita por paises-->
        <script>
            function GraficaDominio(fecha1, fecha2) {
                // alert(fecha1+' - '+fecha2);
                $('#containerBarDominio').html('');
                var parametro = {
                    "fecha1" : fecha1,
                    "fecha2" : fecha2
                }
                $.ajax({
                    url:  'controller/new_grafica_visita_dominio.php', 
                    type: 'POST',
                    data: parametro,
                    dataType: 'html'
                })
                .done(function(data){  
                    $('#containerBarDominio').html('');    
                    $('#containerBarDominio').html(data); // mostrar la data
                })
                .fail(function(){
                    $('#containerBarDominio').html('');
                });
            }
        </script>

        <script type="text/javascript">
            /* jQueryKnob */
            $(".knob").knob();
        </script>
