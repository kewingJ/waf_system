<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

	$query = mysqli_query($link,"TRUNCATE whitelist") or die(mysqli_error($link));
    echo "bien";
?>