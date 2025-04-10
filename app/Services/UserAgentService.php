<?php

namespace App\Services;

use Detection\MobileDetect;

class UserAgentService
{
    protected $detect;
    protected $userAgent;

    public function __construct()
    {
        $this->detect = new MobileDetect();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public function parse($userAgent = null)
    {
        if ($userAgent) {
            $this->userAgent = $userAgent;
            $this->detect->setUserAgent($userAgent);
        }
        return $this;
    }

    public function platform()
    {
        if ($this->detect->isTablet()) {
            return 'tablet';
        }

        if ($this->detect->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    }

    public function browser()
    {
        $userAgent = $this->userAgent;
        
        $browser = 'Unknown';
        
        if (preg_match('/MSIE/i', $userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            if (preg_match('/Edge/i', $userAgent)) {
                $browser = 'Edge';
            } else {
                $browser = 'Chrome';
            }
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }
        
        return $browser;
    }

    public function device()
    {
        return $this->platform();
    }

    public function isDesktop()
    {
        return !$this->detect->isMobile() && !$this->detect->isTablet();
    }

    public function isMobile()
    {
        return $this->detect->isMobile();
    }

    public function isTablet()
    {
        return $this->detect->isTablet();
    }

    public function isRobot()
    {
        $userAgent = $this->userAgent;
        return preg_match('/bot|crawl|slurp|spider|mediapartners/i', $userAgent);
    }
}
