<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('ajax_update_master');

    mysqli_query(
        $link,
        "INSERT INTO bloqueo_master
            (id_bloqueo_referencia, tabla, ip_bloqueda, fecha_bloqueo, tipo_ataque)
         SELECT b.id_bloqueo, 'bloqueo', b.ip, b.fecha_bloqueo, b.tipo_ataque
         FROM bloqueo b
         LEFT JOIN bloqueo_master bm
            ON bm.id_bloqueo_referencia = b.id_bloqueo
           AND bm.tabla = 'bloqueo'
         WHERE bm.id_bloqueo_master IS NULL"
    ) or die(mysqli_error($link));

    mysqli_query(
        $link,
        "INSERT INTO bloqueo_master
            (id_bloqueo_referencia, tabla, ip_bloqueda, fecha_bloqueo, tipo_ataque)
         SELECT bi.id_bloqueo_ip, 'bloqueo_ip', bi.ip_bloqueada, bi.fecha_bloqueo_ip, bi.tipo_ataque_ip
         FROM bloqueo_ip bi
         LEFT JOIN bloqueo_master bm
            ON bm.id_bloqueo_referencia = bi.id_bloqueo_ip
           AND bm.tabla = 'bloqueo_ip'
         WHERE bm.id_bloqueo_master IS NULL"
    ) or die(mysqli_error($link));
?>
