<?php

namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;

class HealthController extends Controller
{
    /**
     * @return mixed
     */
    private static function getServerCpuUsage()
    {
        $serverLoad = [];

        // DIRECTORY_SEPARATOR checks if running windows
        if (DIRECTORY_SEPARATOR != '\\') {
            if (function_exists("sys_getloadavg")) {
                // sys_getloadavg() will return an array with [0] being load within the last minute.
                $serverLoad    = sys_getloadavg();
                $serverLoad[0] = round($serverLoad[0], 4);
            } elseif (@file_exists("/proc/loadavg") && $load = @file_get_contents("/proc/loadavg")) {
                $serverLoad    = explode(" ", $load);
                $serverLoad[0] = round($serverLoad[0], 4);
            }
            if (!is_numeric($serverLoad[0])) {
                if (@ini_get('safe_mode') == 'On') {
                    return "Unknown";
                }

                // Suhosin likes to throw a warning if exec is disabled then die - weird
                if ($func_blacklist = @ini_get('suhosin.executor.func.blacklist')) {
                    if (strpos(",".$func_blacklist.",", 'exec') !== false) {
                        return "Unknown";
                    }
                }
                // PHP disabled functions?
                if ($func_blacklist = @ini_get('disable_functions')) {
                    if (strpos(",".$func_blacklist.",", 'exec') !== false) {
                        return "Unknown";
                    }
                }

                $load       = @exec("uptime");
                $load       = explode("load average: ", $load);
                $serverLoad = explode(",", $load[1]);
                if (!is_array($serverLoad)) {
                    return "Unknown";
                }
            }
        } else {
            return "Unknown";
        }

        return trim($serverLoad[0]);
    }

    /**
     * @return float|int
     */
    protected static function getServerMemoryUsage()
    {
        $free         = shell_exec('free');
        $memory_usage = 0;

        if ($free) {
            $free         = (string)trim($free);
            $free_arr     = explode("\n", $free);
            $mem          = explode(" ", $free_arr[1]);
            $mem          = array_filter($mem);
            $mem          = array_merge($mem);
            $memory_usage = $mem[2] / $mem[1] * 100;
        }
        return $memory_usage;
    }

    /**
     * @param $bytes
     * @return string
     */
    protected static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function health()
    {
        $response = [
            'status'  => 0,
            'health'  => 'Ok',
            'app'     => [],
            'request' => [],
            'cpu'     => [],
            'memory'  => [],
        ];

        // run some check here
        if (1) {
            $response['status'] = 200;
        }

        // App
        $response['app']['name']    = env('APP_NAME');
        $response['app']['version'] = env('APP_VERSION');
        $response['app']['debug']   = config('app.debug');

        // Request
        $response['request']['ip'] = \Request::ip();

        // CPU
        $response['cpu']['usage'] = self::getServerCpuUsage();

        // Memory
        $response['memory']['free']  = self::formatSizeUnits(self::getServerMemoryUsage());
        $response['memory']['total'] = self::formatSizeUnits(memory_get_usage(true));

        # Disk
        $response['disk']['free']  = self::formatSizeUnits(disk_free_space('/'));
        $response['disk']['total'] = self::formatSizeUnits(disk_total_space('/'));

        return $response;
    }
}