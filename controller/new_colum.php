<?php
    include_once '../includes/config.php';
    
    $sql2 = mysqli_query ($link, "ALTER TABLE `bloqueo_ip_pais` ADD `iso3` VARCHAR(50) NOT NULL AFTER `total_bloqueo_ip_pais`");
?>