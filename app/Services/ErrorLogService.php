<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ErrorLogService
{
   public function getRecentErrors()
{
    try {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return [
                'count' => 0,
                'trend' => 0,
                'recent' => []
            ];
        }

        // قراءة آخر 1000 سطر من ملف السجل
        $logs = array_slice(file($logFile), -1000);
        $errors = [];
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(ERROR|WARNING|NOTICE|DEPRECATED|ALERT|CRITICAL|EMERGENCY).*?({.*}|\[(.*?)\]|: (.*?)(?=\s+in|\s+\[|\s*$))/i';

        foreach ($logs as $log) {
            if (preg_match($pattern, $log, $matches)) {
                $timestamp = $matches[1];
                // تجاهل الأخطاء القديمة (أكثر من 24 ساعة)
                if (strtotime($timestamp) < strtotime('-24 hours')) {
                    continue;
                }

                // استخراج الرسالة من النتائج
                $message = '';
                if (!empty($matches[3])) {
                    $message = $matches[3]; // JSON or array format
                } elseif (!empty($matches[4])) {
                    $message = $matches[4]; // Square bracket format
                } elseif (!empty($matches[5])) {
                    $message = $matches[5]; // Regular message
                }

                // تنظيف الرسالة
                $message = trim($message);
                if (str_starts_with($message, ': ')) {
                    $message = substr($message, 2);
                }

                // محاولة استخراج معلومات الملف والسطر
                $file = '';
                $line = '';
                if (preg_match('/in\s+(.*?):(\d+)/', $log, $fileMatches)) {
                    $file = $fileMatches[1];
                    $line = $fileMatches[2];
                }

                $errors[] = [
                    'id' => md5($timestamp . $message . $file . $line),
                    'timestamp' => $timestamp,
                    'type' => ucfirst(strtolower($matches[2])),
                    'message' => $message,
                    'file' => $file,
                    'line' => $line
                ];
            }
        }

        // ترتيب الأخطاء حسب الوقت (الأحدث أولاً)
        usort($errors, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // حساب الاتجاه
        $now = time();
        $lastHour = array_filter($errors, function($error) use ($now) {
            return strtotime($error['timestamp']) > ($now - 3600);
        });
        $previousHour = array_filter($errors, function($error) use ($now) {
            return strtotime($error['timestamp']) <= ($now - 3600) && 
                   strtotime($error['timestamp']) > ($now - 7200);
        });

        $trend = count($lastHour) - count($previousHour);

        return [
            'count' => count($errors),
            'trend' => $trend,
            'recent' => array_slice($errors, 0, 10) // Return only the 10 most recent errors
        ];

    } catch (\Exception $e) {
        Log::error('Error in ErrorLogService::getRecentErrors: ' . $e->getMessage());
        return [
            'count' => 0,
            'trend' => 0,
            'recent' => []
        ];
    }
}

  public function deleteError($errorId)
{
    try {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return false;
        }

        $logs = file($logFile);
        $newLogs = [];
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(ERROR|WARNING|NOTICE|DEPRECATED).*?: (.*?) in (.*?):(\d+)/i';

        foreach ($logs as $log) {
            if (preg_match($pattern, $log, $matches)) {
                $currentId = md5($matches[1] . $matches[3] . $matches[4] . $matches[5]);
                if ($currentId !== $errorId) {
                    $newLogs[] = $log;
                }
            } else {
                $newLogs[] = $log;
            }
        }

        File::put($logFile, implode('', $newLogs));
        return true;

    } catch (\Exception $e) {
        Log::error('Error deleting error log: ' . $e->getMessage());
        return false;
    }
}
}
