<?php

function lisense_cleanup_all_data($link)
{
    $queries = array(
        "TRUNCATE ataques",
        "TRUNCATE bloqueo",
        "TRUNCATE bloqueo_ddos",
        "UPDATE bloqueo_ddos_pais SET total_bloqueo_ddos_pais = 0, iso3 = '' WHERE 1",
        "TRUNCATE bloqueo_ddos_pais_rango",
        "TRUNCATE bloqueo_ip",
        "UPDATE bloqueo_ip_pais SET total_bloqueo_ip_pais = 0, iso3 = '' WHERE 1",
        "TRUNCATE bloqueo_master",
        "UPDATE bloqueo_pais SET total_bloqueo = 0, iso3 = '' WHERE 1",
        "TRUNCATE bloqueo_pais_rango",
        "TRUNCATE grafica_bloqueo",
        "TRUNCATE grafica_bloqueo_ip",
        "TRUNCATE grafica_bloqueo_rango",
        "TRUNCATE grafica_bloqueo_rango_ip",
        "TRUNCATE grafica_consulta",
        "TRUNCATE grafica_ddos",
        "TRUNCATE grafica_principal",
        "TRUNCATE grafica_visitas",
        "TRUNCATE grafica_visitas_dominio",
        "TRUNCATE host",
        "TRUNCATE host_borrados",
        "TRUNCATE host_visita_borrados",
        "TRUNCATE notificacion_regla",
        "TRUNCATE reporte",
        "TRUNCATE respaldos",
        "UPDATE resumen_datos SET paquetes = 0, virus = 0, bloqueo_ip = 0, bloqueo_waf = 0 WHERE 1",
        "TRUNCATE sitio",
        "TRUNCATE usuario_host",
        "TRUNCATE visita_dominio",
        "TRUNCATE visita_dominio_group",
        "UPDATE visita_pais SET total_visita = 0, iso3 = '' WHERE 1",
        "TRUNCATE whitelist",
        "TRUNCATE whitelist_ddos",
        "TRUNCATE usuario"
    );

    foreach ($queries as $sql) {
        if (!mysqli_query($link, $sql)) {
            return array(
                'success' => false,
                'query' => $sql,
                'error' => mysqli_error($link)
            );
        }
    }

    return array(
        'success' => true
    );
}

