<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Base DataTable Controller
 * Reusable functionality untuk DataTable operations
 */
abstract class BaseDataTableController extends BaseController
{
    use ResponseTrait;

    protected $cache;
    protected $cachePrefix = 'datatable_';
    protected $cacheTTL = 300; // 5 minutes

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->cache = \Config\Services::cache();
    }

    /**
     * Process standard DataTable request with optimization support
     */
    protected function processDataTableRequest($model, $columns, $searchFields = [], $joins = [], $conditions = [], $useOptimized = true)
    {
        try {
            $request = service('request');
            
            // DataTable parameters
            $draw = intval($request->getPost('draw') ?? $request->getGet('draw') ?? 1);
            $start = intval($request->getPost('start') ?? $request->getGet('start') ?? 0);
            $length = intval($request->getPost('length') ?? $request->getGet('length') ?? 25);
            $searchValue = trim($request->getPost('search')['value'] ?? $request->getGet('search') ?? '');
            
            // Order parameters
            $orderColumnIndex = intval($request->getPost('order')[0]['column'] ?? 0);
            $orderDir = $request->getPost('order')[0]['dir'] ?? 'asc';
            $orderColumn = $columns[$orderColumnIndex] ?? $columns[0];

            // Check if using optimized model methods
            if ($useOptimized && method_exists($model, 'getDataTableOptimized')) {
                // Use optimized model method
                $result = $model->getDataTableOptimized([
                    'search' => $searchValue,
                    'start' => $start,
                    'length' => $length,
                    'orderColumn' => $orderColumn,
                    'orderDir' => $orderDir,
                    'conditions' => $conditions
                ]);
                
                return $this->respond([
                    'draw' => $draw,
                    'recordsTotal' => $result['total'],
                    'recordsFiltered' => $result['filtered'],
                    'data' => $result['data']
                ]);
            }

            // Fallback to traditional method
            // Build query
            $builder = $model->builder();
            
            // Apply joins
            foreach ($joins as $join) {
                $builder->join($join['table'], $join['condition'], $join['type'] ?? 'left');
            }

            // Apply conditions
            foreach ($conditions as $condition) {
                if (isset($condition['whereIn'])) {
                    $builder->whereIn($condition['field'], $condition['whereIn']);
                } else {
                    $builder->where($condition['field'], $condition['value']);
                }
            }

            // Count total records
            $totalRecords = $builder->countAllResults(false);

            // Apply search
            if (!empty($searchValue) && !empty($searchFields)) {
                $builder->groupStart();
                foreach ($searchFields as $field) {
                    $builder->orLike($field, $searchValue);
                }
                $builder->groupEnd();
            }

            // Count filtered records
            $filteredRecords = $builder->countAllResults(false);

            // Apply ordering and pagination
            $builder->orderBy($orderColumn, $orderDir)
                   ->limit($length, $start);

            // Get data
            $data = $builder->get()->getResultArray();

            return $this->respond([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'DataTable Error: ' . $e->getMessage());
            
            return $this->respond([
                'draw' => $draw ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => ENVIRONMENT === 'production' ? 'Internal server error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cache query results untuk data yang jarang berubah
     */
    protected function getCachedData($cacheKey, $callback, $ttl = null)
    {
        $ttl = $ttl ?? $this->cacheTTL;
        $fullKey = $this->cachePrefix . $cacheKey;
        
        $data = $this->cache->get($fullKey);
        
        if ($data === null) {
            $data = $callback();
            $this->cache->save($fullKey, $data, $ttl);
        }
        
        return $data;
    }

    /**
     * Invalidate related cache keys
     */
    protected function invalidateCache($pattern)
    {
        // Note: File cache handler tidak support pattern deletion
        // Untuk production, pertimbangkan Redis/Memcached
        $this->cache->clean();
    }

    /**
     * Validate and sanitize DataTable input
     */
    protected function validateDataTableInput($request)
    {
        $draw = filter_var($request->getPost('draw'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'default' => 1]
        ]);

        $start = filter_var($request->getPost('start'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'default' => 0]
        ]);

        $length = filter_var($request->getPost('length'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 100, 'default' => 25]
        ]);

        return compact('draw', 'start', 'length');
    }

    /**
     * Common error response
     */
    protected function errorResponse($message, $code = 500)
    {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'csrf_hash' => csrf_hash()
        ], $code);
    }

    /**
     * Common success response
     */
    protected function successResponse($data, $message = 'Success')
    {
        return $this->respond([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'csrf_hash' => csrf_hash()
        ]);
    }
}