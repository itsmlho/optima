<?php

if (!function_exists('safe_get_row')) {
    /**
     * Safely execute query and get single row
     * Returns array or null if query fails
     *
     * @param mixed $query Query Builder object or ResultInterface
     * @return array|null
     */
    function safe_get_row($query): ?array
    {
        try {
            $result = (is_object($query) && method_exists($query, 'get')) ? $query->get() : $query;
            
            if (!$result || $result === false) {
                return null;
            }
            
            $row = $result->getRowArray();
            return $row ?: null;
        } catch (\Exception $e) {
            log_message('error', 'safe_get_row failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('safe_get_result')) {
    /**
     * Safely execute query and get result array
     * Returns array (empty if query fails)
     *
     * @param mixed $query Query Builder object or ResultInterface
     * @return array
     */
    function safe_get_result($query): array
    {
        try {
            $result = (is_object($query) && method_exists($query, 'get')) ? $query->get() : $query;
            
            if (!$result || $result === false) {
                return [];
            }
            
            return $result->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'safe_get_result failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('safe_count_results')) {
    /**
     * Safely count query results
     * Returns integer count (0 if query fails)
     *
     * @param mixed $query Query Builder object
     * @return int
     */
    function safe_count_results($query): int
    {
        try {
            if (!$query || !is_object($query)) {
                return 0;
            }
            
            return (int) $query->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'safe_count_results failed: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('safe_get_result_object')) {
    /**
     * Safely execute query and get result as objects
     * Returns array of objects (empty if query fails)
     *
     * @param mixed $query Query Builder object or ResultInterface
     * @return array
     */
    function safe_get_result_object($query): array
    {
        try {
            $result = (is_object($query) && method_exists($query, 'get')) ? $query->get() : $query;
            
            if (!$result || $result === false) {
                return [];
            }
            
            return $result->getResult();
        } catch (\Exception $e) {
            log_message('error', 'safe_get_result_object failed: ' . $e->getMessage());
            return [];
        }
    }
}
