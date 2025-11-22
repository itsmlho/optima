<?php

if (!function_exists('getBrowserInfo')) {
    /**
     * Get browser information from user agent
     */
    function getBrowserInfo(?string $userAgent = null): array
    {
        if (empty($userAgent)) {
            $request = \Config\Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        $browser = 'Unknown';
        $version = '';

        // Detect browser
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
            if (preg_match('/MSIE\s([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            } elseif (preg_match('/rv:([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
            if (preg_match('/Edge\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge (Chromium)';
            if (preg_match('/Edg\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
            if (preg_match('/Chrome\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
            if (preg_match('/Firefox\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
            if (preg_match('/Version\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
            if (preg_match('/Version\/([0-9]+)/i', $userAgent, $matches) || preg_match('/OPR\/([0-9]+)/i', $userAgent, $matches)) {
                $version = $matches[1] ?? '';
            }
        }

        return [
            'name' => $browser,
            'version' => $version,
            'full' => $browser . ($version ? ' ' . $version : '')
        ];
    }
}

if (!function_exists('getOSInfo')) {
    /**
     * Get operating system information from user agent
     */
    function getOSInfo(?string $userAgent = null): array
    {
        if (empty($userAgent)) {
            $request = \Config\Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        $os = 'Unknown';
        $version = '';

        // Detect OS
        if (preg_match('/Windows NT 10.0/i', $userAgent)) {
            $os = 'Windows';
            $version = '10/11';
        } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
            $os = 'Windows';
            $version = '8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $userAgent)) {
            $os = 'Windows';
            $version = '8';
        } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
            $os = 'Windows';
            $version = '7';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
            if (preg_match('/Mac OS X ([0-9_]+)/i', $userAgent, $matches)) {
                $version = str_replace('_', '.', $matches[1]);
            }
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
            if (preg_match('/Android ([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $os = preg_match('/iPad/i', $userAgent) ? 'iPadOS' : 'iOS';
            if (preg_match('/OS ([0-9_]+)/i', $userAgent, $matches)) {
                $version = str_replace('_', '.', $matches[1]);
            }
        }

        return [
            'name' => $os,
            'version' => $version,
            'full' => $os . ($version ? ' ' . $version : '')
        ];
    }
}

if (!function_exists('getDeviceType')) {
    /**
     * Get device type from user agent
     */
    function getDeviceType(?string $userAgent = null): string
    {
        if (empty($userAgent)) {
            $request = \Config\Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $userAgent)) {
            if (preg_match('/iPad/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }

        return 'desktop';
    }
}

if (!function_exists('generateDeviceId')) {
    /**
     * Generate unique device ID from user agent and IP
     */
    function generateDeviceId(?string $userAgent = null, ?string $ipAddress = null): string
    {
        if (empty($userAgent)) {
            $request = \Config\Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        if (empty($ipAddress)) {
            $request = \Config\Services::request();
            $ipAddress = $request->getIPAddress();
        }

        // Create a hash from user agent and IP (partial IP for privacy)
        $ipParts = explode('.', $ipAddress);
        $partialIP = isset($ipParts[0], $ipParts[1]) ? $ipParts[0] . '.' . $ipParts[1] . '.x.x' : $ipAddress;
        
        $deviceString = $userAgent . '|' . $partialIP;
        
        return hash('sha256', $deviceString);
    }
}

if (!function_exists('getDeviceName')) {
    /**
     * Get user-friendly device name
     */
    function getDeviceName(?string $userAgent = null): string
    {
        if (empty($userAgent)) {
            $request = \Config\Services::request();
            $userAgent = $request->getUserAgent()->getAgentString();
        }

        $browser = getBrowserInfo($userAgent);
        $os = getOSInfo($userAgent);
        $deviceType = getDeviceType($userAgent);

        $parts = [];
        
        // Add device type
        if ($deviceType === 'mobile') {
            $parts[] = 'Mobile';
        } elseif ($deviceType === 'tablet') {
            $parts[] = 'Tablet';
        } else {
            $parts[] = 'Desktop';
        }

        // Add OS
        $parts[] = $os['name'];

        // Add browser
        $parts[] = $browser['name'];

        return implode(' - ', $parts);
    }
}

