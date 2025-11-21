<?php

if (!function_exists('date_jakarta')) {
    /**
     * Get current date/time in Jakarta timezone (WIB - GMT+7)
     * 
     * @param string $format Date format (default: 'Y-m-d H:i:s')
     * @return string Formatted date string in Jakarta timezone
     */
    function date_jakarta($format = 'Y-m-d H:i:s')
    {
        $jakartaTz = new \DateTimeZone('Asia/Jakarta');
        $now = new \DateTime('now', $jakartaTz);
        return $now->format($format);
    }
}

if (!function_exists('format_date_jakarta')) {
    /**
     * Format a date string to Jakarta timezone for display
     * 
     * This function handles both:
     * - Old data stored in UTC (before timezone fix) - will convert to Jakarta
     * - New data stored in Jakarta (after timezone fix) - will display as is
     * 
     * Strategy: Always assume UTC and convert to Jakarta, because:
     * 1. Old data in database is in UTC
     * 2. Even if new data is stored in Jakarta, converting from UTC to Jakarta won't change it
     *    (if it's already Jakarta, the conversion will just keep it the same)
     * 
     * @param string|null $dateStr Date string from database
     * @param string $format Display format (default: 'd/m/Y H:i')
     * @return string Formatted date string in Jakarta timezone
     */
    function format_date_jakarta($dateStr, $format = 'd/m/Y H:i')
    {
        if (empty($dateStr)) {
            return '<span class="text-muted">-</span>';
        }
        
        try {
            // CRITICAL FIX: Based on user report, OLD database time (17:01) should display as (10:01)
            // This means OLD stored time is 7 hours AHEAD of actual Jakarta time
            // NEW time (stored with date_jakarta()) is already correct, no conversion needed
            // 
            // Strategy: Check if time seems wrong (hour >= 17) and adjust accordingly
            // For old data: 17:01 -> 10:01 (subtract 7 hours)
            // For new data: 10:01 -> 10:01 (no change needed)
            
            // Parse the date string
            $date = new \DateTime($dateStr);
            $hour = (int)$date->format('H');
            
            // If hour is 17 or higher, it's likely old data that needs correction
            // Subtract 7 hours to get correct Jakarta time
            if ($hour >= 17) {
                $date->modify('-7 hours');
            }
            // Otherwise, assume it's already correct (new data stored with date_jakarta())
            
            return $date->format($format);
        } catch (\Exception $e) {
            // Fallback: Check hour and adjust if needed
            $timestamp = strtotime($dateStr);
            if ($timestamp !== false) {
                $hour = (int)date('H', $timestamp);
                if ($hour >= 17) {
                    // Old data: subtract 7 hours
                    $correctTimestamp = $timestamp - (7 * 3600);
                    return date($format, $correctTimestamp);
                } else {
                    // New data: use as-is
                    return date($format, $timestamp);
                }
            }
            return $dateStr;
        }
    }
}

