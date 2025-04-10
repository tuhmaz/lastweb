<?php

namespace App\Helpers;

class SecureUrlHelper
{
    /**
     * تحويل جميع الروابط HTTP إلى HTTPS في المحتوى
     *
     * @param string $content المحتوى الذي يحتوي على روابط
     * @return string المحتوى بعد تحويل الروابط
     */
    public static function secureUrls($content)
    {
        if (empty($content)) {
            return $content;
        }

        // تحويل روابط HTTP إلى HTTPS
        $content = preg_replace('/(http:\/\/)([^"\'\s]+)/i', 'https://$2', $content);

        return $content;
    }

    /**
     * تحويل رابط HTTP إلى HTTPS
     *
     * @param string $url الرابط المراد تحويله
     * @return string الرابط بعد التحويل
     */
    public static function secureUrl($url)
    {
        if (empty($url)) {
            return $url;
        }

        // تحويل رابط HTTP إلى HTTPS
        return preg_replace('/^http:/i', 'https:', $url);
    }

    /**
     * فحص المحتوى للبحث عن روابط مختلطة (HTTP في صفحات HTTPS)
     *
     * @param string $content المحتوى المراد فحصه
     * @return array قائمة بالروابط المختلطة
     */
    public static function findMixedContent($content)
    {
        if (empty($content)) {
            return [];
        }

        $mixedContent = [];
        
        // البحث عن روابط HTTP في المحتوى
        preg_match_all('/(http:\/\/)([^"\'\s]+)/i', $content, $matches);
        
        if (!empty($matches[0])) {
            $mixedContent = $matches[0];
        }
        
        return $mixedContent;
    }

    /**
     * فحص ما إذا كان الرابط آمنًا (HTTPS)
     *
     * @param string $url الرابط المراد فحصه
     * @return bool هل الرابط آمن
     */
    public static function isSecureUrl($url)
    {
        if (empty($url)) {
            return true;
        }

        return strpos(strtolower($url), 'https://') === 0;
    }
}
