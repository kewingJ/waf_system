<?php

if (!function_exists('waf_cron_state_dir')) {
    function waf_cron_state_dir()
    {
        static $resolvedDir = null;
        if ($resolvedDir !== null) {
            return $resolvedDir;
        }

        $baseDir = dirname(__DIR__);
        $dirs = array(
            $baseDir . '/.cron_state',
            rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . '/waf_cron_state_' . substr(md5($baseDir), 0, 12),
        );

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            if (is_dir($dir) && is_writable($dir)) {
                $resolvedDir = $dir;
                return $resolvedDir;
            }
        }

        $resolvedDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
        return $resolvedDir;
    }
}

if (!function_exists('waf_acquire_cron_lock')) {
    function waf_acquire_cron_lock($name)
    {
        $lockFile = waf_cron_state_dir() . '/' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name) . '.lock';
        $handle = @fopen($lockFile, 'c');
        if ($handle === false || !flock($handle, LOCK_EX | LOCK_NB)) {
            exit;
        }
        if (!isset($GLOBALS['waf_cron_lock_handles']) || !is_array($GLOBALS['waf_cron_lock_handles'])) {
            $GLOBALS['waf_cron_lock_handles'] = array();
        }
        $GLOBALS['waf_cron_lock_handles'][] = $handle;
        return $handle;
    }
}

if (!function_exists('waf_should_process_full_log')) {
    function waf_should_process_full_log()
    {
        global $argv;
        if (PHP_SAPI === 'cli' && is_array($argv) && in_array('--full', $argv, true)) {
            return true;
        }
        return isset($_GET['full']) && $_GET['full'] === '1';
    }
}

if (!function_exists('waf_open_incremental_log')) {
    function waf_open_incremental_log($logPath, $stateName)
    {
        $fp = @fopen($logPath, 'r');
        if ($fp === false) {
            return false;
        }

        $stateFile = waf_cron_state_dir() . '/' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $stateName) . '.json';
        $size = filesize($logPath);
        $offset = $size;

        if (waf_should_process_full_log()) {
            $offset = 0;
        } elseif (is_file($stateFile)) {
            $state = json_decode((string) file_get_contents($stateFile), true);
            if (is_array($state) && isset($state['offset'])) {
                $offset = (int) $state['offset'];
            }
            if ($offset > $size) {
                $offset = 0;
            }
        }

        fseek($fp, $offset);

        return array($fp, $stateFile, $size);
    }
}

if (!function_exists('waf_save_incremental_log_state')) {
    function waf_save_incremental_log_state($fp, $stateFile)
    {
        $offset = ftell($fp);
        @file_put_contents($stateFile, json_encode(array(
            'offset' => $offset,
            'updated_at' => date('Y-m-d H:i:s'),
        )));
    }
}

?>
