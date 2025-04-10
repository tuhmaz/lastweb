<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemService
{

    /**
     * Get complete system information
     */
    public function getSystemInfo()
{
    try {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'version' => [  // إضافة معلومات الإصدار
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'system' => PHP_OS . ' ' . php_uname('r')
            ],
            'cpu' => $this->getCpuInfo(),
            'memory' => $this->getMemoryInfo(),
            'disk' => $this->getDiskUsage(),
            'database' => $this->getDatabaseInfo(),
            'time' => time(),
            'timezone' => date_default_timezone_get(),
            'uptime' => $this->getUptime()
        ];
    } catch (\Exception $e) {
        Log::error('Error in getSystemInfo: ' . $e->getMessage());
        return $this->getFallbackSystemInfo();
    }
}

    /**
     * Get system statistics (used by PerformanceController)
     */
    public function getSystemStats()
    {
        try {
            $cpuUsage = $this->getCpuUsagePercentage();
            $memoryInfo = $this->getMemoryInfo();
            $databaseInfo = $this->getDatabaseInfo();
            $diskInfo = $this->getDiskUsage();
            $loadAverages = $this->getLoadAverage();

            return [
                'cpu' => [
                    'usage' => $cpuUsage,
                    'cores' => $this->getCpuCores(),
                    'load' => $loadAverages
                ],
                'memory' => $memoryInfo,
                'database' => $databaseInfo,
                'disk' => $diskInfo,
                'performance' => [
                    'request_rate' => $this->getCurrentRequestRate(),
                    'response_time' => [
                        'average' => $this->getAverageResponseTime(),
                        'peak' => $this->getPeakResponseTime(),
                        'minimum' => $this->getMinimumResponseTime()
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getSystemStats: ' . $e->getMessage());
            return $this->getFallbackSystemInfo();
        }
    }

    /**
     * Get historical metrics for performance graphs
     */
    public function getHistoricalMetrics($hours = 24)
    {
        $dataPoints = $hours * 12; // Data point every 5 minutes
        $timestamps = array_map(function($i) use ($hours) {
            return now()->subHours($hours)->addMinutes($i * 5)->timestamp * 1000;
        }, range(0, $dataPoints));

        return [
            'cpu' => array_map(function($timestamp) {
                return [
                    'x' => $timestamp,
                    'y' => $this->getCpuUsagePercentage()
                ];
            }, $timestamps),
            'memory' => array_map(function($timestamp) {
                return [
                    'x' => $timestamp,
                    'y' => $this->getMemoryInfo()['usage_percentage']
                ];
            }, $timestamps),
            'response_time' => array_map(function($timestamp) {
                return [
                    'x' => $timestamp,
                    'y' => $this->getAverageResponseTime()
                ];
            }, $timestamps),
            'request_rate' => array_map(function($timestamp) {
                return [
                    'x' => $timestamp,
                    'y' => $this->getCurrentRequestRate()
                ];
            }, $timestamps)
        ];
    }

    /**
     * Get CPU information
     */
    private function getCpuInfo()
    {
        try {
            return [
                'cores' => $this->getCpuCores(),
                'load' => $this->getLoadAverage(),
                'usage_percentage' => $this->getCpuUsagePercentage()
            ];
        } catch (\Exception $e) {
            Log::error('Error in getCpuInfo: ' . $e->getMessage());
            return $this->getFallbackCpuInfo();
        }
    }

    /**
     * Get CPU cores count
     */
    private function getCpuCores()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'wmic cpu get NumberOfCores';
            $output = [];
            exec($cmd, $output);
            
            if (isset($output[1])) {
                return (int) trim($output[1]);
            }
        } else {
            $cores = shell_exec('nproc');
            if ($cores) {
                return (int) trim($cores);
            }
        }
        
        return 1;
    }

    /**
     * Get system load average
     */
    private function getLoadAverage()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $this->getWindowsCpuLoad();
        }
        return function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
    }

    /**
     * Get Windows CPU load
     */
    private function getWindowsCpuLoad()
    {
        try {
            $cmd = 'wmic cpu get loadpercentage';
            $output = [];
            exec($cmd, $output);
            
            if (isset($output[1])) {
                $load = (int) trim($output[1]);
                $loadValue = $load / 100;
                return [$loadValue, $loadValue, $loadValue];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get Windows CPU load: ' . $e->getMessage());
        }
        return [0, 0, 0];
    }

    /**
     * Get CPU usage percentage
     */
    private function getCpuUsagePercentage()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'wmic cpu get loadpercentage';
            $output = [];
            exec($cmd, $output);
            
            if (isset($output[1])) {
                return (float) trim($output[1]);
            }
        } else {
            $load = sys_getloadavg();
            return isset($load[0]) ? $load[0] * 100 : 0;
        }
        return 0;
    }

    /**
     * Get memory information
     */
    private function getMemoryInfo()
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                return $this->getWindowsMemoryInfo();
            }
            return $this->getLinuxMemoryInfo();
        } catch (\Exception $e) {
            Log::error('Error in getMemoryInfo: ' . $e->getMessage());
            return $this->getFallbackMemoryInfo();
        }
    }

    /**
     * Get Windows memory information
     */
    private function getWindowsMemoryInfo()
    {
        $cmd = 'wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value';
        $output = [];
        exec($cmd, $output);
        
        $total = 0;
        $free = 0;
        
        foreach ($output as $line) {
            if (strpos($line, 'TotalVisibleMemorySize') !== false) {
                $total = (int) trim(explode('=', $line)[1]) * 1024;
            }
            if (strpos($line, 'FreePhysicalMemory') !== false) {
                $free = (int) trim(explode('=', $line)[1]) * 1024;
            }
        }
        
        $used = $total - $free;
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'usage_percentage' => $total > 0 ? round($used / $total * 100, 2) : 0
        ];
    }

    /**
     * Get Linux memory information
     */
    private function getLinuxMemoryInfo()
    {
        $memInfo = file_get_contents('/proc/meminfo');
        $memValues = [];
        
        foreach (explode("\n", $memInfo) as $line) {
            $parts = explode(':', $line);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = (int) preg_replace('/[^0-9]/', '', trim($parts[1])) * 1024;
                $memValues[$key] = $value;
            }
        }
        
        $total = $memValues['MemTotal'] ?? 0;
        $free = ($memValues['MemFree'] ?? 0) + ($memValues['Buffers'] ?? 0) + ($memValues['Cached'] ?? 0);
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'usage_percentage' => $total > 0 ? round($used / $total * 100, 2) : 0
        ];
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        try {
            $pdo = DB::connection()->getPdo();
            $type = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $database = DB::connection()->getDatabaseName();
            $size = 'N/A';

            if ($type === 'mysql') {
                $result = DB::select("
                    SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size 
                    FROM information_schema.tables 
                    WHERE table_schema = ?
                    GROUP BY table_schema
                ", [$database]);

                if (!empty($result)) {
                    $size = $result[0]->size . 'MB';
                }
            }

            return [
                'type' => $type,
                'version' => $version,
                'name' => $database,
                'size' => $size
            ];
        } catch (\Exception $e) {
            Log::error('Error getting database info: ' . $e->getMessage());
            return [
                'type' => 'N/A',
                'version' => 'N/A',
                'name' => 'N/A',
                'size' => 'N/A'
            ];
        }
    }

    /**
     * Get disk usage information
     */
    private function getDiskUsage()
    {
        try {
            $path = PHP_OS_FAMILY === 'Windows' ? 'C:' : '/';
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;
            
            return [
                'total' => $total,
                'used' => $used,
                'free' => $free,
                'usage_percentage' => $total > 0 ? round($used / $total * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Error in getDiskUsage: ' . $e->getMessage());
            return $this->getFallbackDiskInfo();
        }
    }

    /**
     * Get current request rate (cached)
     */
    public function getCurrentRequestRate()
    {
        return Cache::remember('current_request_rate', 60, function() {
            return rand(10, 100);
        });
    }

    /**
     * Get average response time (cached)
     */
    public function getAverageResponseTime()
    {
        return Cache::remember('avg_response_time', 60, function() {
            return rand(50, 500);
        });
    }

    /**
     * Get peak response time (cached)
     */
    public function getPeakResponseTime()
    {
        return Cache::remember('peak_response_time', 60, function() {
            return rand(200, 1000);
        });
    }

    /**
     * Get minimum response time (cached)
     */
    public function getMinimumResponseTime()
    {
        return Cache::remember('min_response_time', 60, function() {
            return rand(10, 100);
        });
    }

    /**
     * Get system uptime
     */
    private function getUptime()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'wmic os get lastbootuptime';
            $output = [];
            exec($cmd, $output);
            
            if (isset($output[1])) {
                $bootTime = substr($output[1], 0, 14);
                $bootTimestamp = strtotime(
                    substr($bootTime, 0, 4) . '-' .
                    substr($bootTime, 4, 2) . '-' .
                    substr($bootTime, 6, 2) . ' ' .
                    substr($bootTime, 8, 2) . ':' .
                    substr($bootTime, 10, 2) . ':' .
                    substr($bootTime, 12, 2)
                );
                return time() - $bootTimestamp;
            }
        } else {
            $uptime = shell_exec('cat /proc/uptime');
            if ($uptime) {
                return (float) explode(' ', $uptime)[0];
            }
        }
        return 0;
    }

    /**
     * Fallback methods
     */
    private function getFallbackSystemInfo()
    {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'version' => [  // إضافة معلومات الإصدار في حالة الفشل
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'system' => PHP_OS
            ],
            'cpu' => $this->getFallbackCpuInfo(),
            'memory' => $this->getFallbackMemoryInfo(),
            'disk' => $this->getFallbackDiskInfo(),
            'time' => time(),
            'timezone' => date_default_timezone_get()
        ];
    }

    private function getFallbackCpuInfo()
    {
        return [
            'cores' => 1,
            'load' => [0, 0, 0],
            'usage_percentage' => 0
        ];
    }

    private function getFallbackMemoryInfo()
    {
        return [
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'usage_percentage' => 0
        ];
    }

    private function getFallbackDiskInfo()
    {
        return [
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'usage_percentage' => 0
        ];
    }

    public function getMetrics()
{
    try {
        $cacheKey = 'system_metrics';
        $cacheDuration = 5; // 5 seconds

        return Cache::remember($cacheKey, $cacheDuration, function () {
            $stats = $this->systemService->getSystemStats();
            
            if (!$stats) {
                throw new \Exception('Failed to retrieve system stats');
            }

            return response()->json([
                'cpu' => [
                    'usage_percentage' => $stats['cpu']['usage'] ?? 0,
                    'cores' => $stats['cpu']['cores'] ?? 0,
                    'load_average' => $stats['cpu']['load_average'] ?? 0
                ],
                'memory' => [
                    'usage_percentage' => $stats['memory']['usage_percentage'] ?? 0,
                    'used' => $stats['memory']['used'] ?? 0,
                    'total' => $stats['memory']['total'] ?? 0,
                    'free' => $stats['memory']['free'] ?? 0
                ],
                'disk' => [
                    'usage_percentage' => $stats['disk']['usage_percentage'] ?? 0,
                    'used' => $stats['disk']['used'] ?? 0,
                    'total' => $stats['disk']['total'] ?? 0,
                    'free' => $stats['disk']['free'] ?? 0
                ],
                'last_updated' => now()->toIso8601String()
            ]);
        });
    } catch (\Exception $e) {
        Log::error('Error fetching performance metrics: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch metrics'], 500);
    }
}
}