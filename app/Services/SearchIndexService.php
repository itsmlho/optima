<?php

namespace App\Services;

/**
 * Advanced Search Service untuk Shared Hosting
 * File-based full-text search tanpa Elasticsearch
 */
class SearchIndexService
{
    private $indexPath;
    private $searchableFields = [
        'kontrak' => ['no_kontrak', 'customer_name', 'location_name'],
        'inventory_unit' => ['serial_number', 'no_unit', 'lokasi_unit'],
        'customers' => ['customer_name', 'customer_code'],
        'customer_locations' => ['location_name', 'address']
    ];
    
    public function __construct()
    {
        $this->indexPath = WRITEPATH . 'search_index/';
        if (!is_dir($this->indexPath)) {
            mkdir($this->indexPath, 0755, true);
        }
    }
    
    /**
     * Build search index untuk table tertentu
     */
    public function buildIndex($table, $batchSize = 1000)
    {
        if (!isset($this->searchableFields[$table])) {
            throw new \Exception("Table {$table} not configured for search indexing");
        }
        
        $db = \Config\Database::connect();
        $fields = $this->searchableFields[$table];
        
        // Clear existing index
        $this->clearIndex($table);
        
        $offset = 0;
        $indexed = 0;
        
        do {
            // Get batch of records
            $builder = $db->table($table)->select(array_merge(['id'], $fields));
            
            // Add joins for kontrak table
            if ($table === 'kontrak') {
                $builder->select([
                    'k.id', 'k.no_kontrak', 'k.nilai_total', 'k.status',
                    'c.customer_name', 'cl.location_name'
                ])
                ->from('kontrak k')
                ->join('customer_locations cl', 'k.customer_location_id = cl.id', 'left')
                ->join('customers c', 'cl.customer_id = c.id', 'left');
                
                $fields = ['no_kontrak', 'customer_name', 'location_name'];
            }
            
            $records = $builder->limit($batchSize, $offset)->get()->getResultArray();
            
            foreach ($records as $record) {
                $this->indexRecord($table, $record, $fields);
                $indexed++;
            }
            
            $offset += $batchSize;
            
        } while (count($records) === $batchSize);
        
        // Save index metadata
        $this->saveIndexMetadata($table, $indexed);
        
        return $indexed;
    }
    
    /**
     * Search across indexed data
     */
    public function search($query, $tables = ['kontrak'], $limit = 50)
    {
        $results = [];
        $query = strtolower(trim($query));
        
        if (strlen($query) < 2) {
            return $results;
        }
        
        foreach ($tables as $table) {
            $tableResults = $this->searchInTable($table, $query, $limit);
            $results[$table] = $tableResults;
        }
        
        return $results;
    }
    
    /**
     * Search dalam table specific
     */
    private function searchInTable($table, $query, $limit)
    {
        $indexFile = $this->indexPath . $table . '_index.json';
        
        if (!file_exists($indexFile)) {
            return [];
        }
        
        $index = json_decode(file_get_contents($indexFile), true);
        $results = [];
        $queryTerms = explode(' ', $query);
        
        foreach ($index as $recordId => $searchableText) {
            $score = $this->calculateScore($queryTerms, $searchableText);
            
            if ($score > 0) {
                $results[] = [
                    'id' => $recordId,
                    'score' => $score,
                    'table' => $table,
                    'preview' => $this->generatePreview($searchableText, $queryTerms)
                ];
            }
        }
        
        // Sort by score descending
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($results, 0, $limit);
    }
    
    /**
     * Calculate search score
     */
    private function calculateScore($queryTerms, $searchableText)
    {
        $score = 0;
        $text = strtolower($searchableText);
        
        foreach ($queryTerms as $term) {
            $term = trim($term);
            if (strlen($term) < 2) continue;
            
            // Exact match gets highest score
            if (strpos($text, $term) !== false) {
                $score += 10;
                
                // Bonus for word boundaries
                if (preg_match('/\b' . preg_quote($term) . '\b/', $text)) {
                    $score += 5;
                }
                
                // Bonus for beginning of text
                if (strpos($text, $term) === 0) {
                    $score += 3;
                }
            }
            
            // Partial matches
            if (strlen($term) >= 3) {
                $partialMatches = substr_count($text, substr($term, 0, 3));
                $score += $partialMatches * 2;
            }
        }
        
        return $score;
    }
    
    /**
     * Generate search result preview
     */
    private function generatePreview($text, $queryTerms, $maxLength = 150)
    {
        $text = strip_tags($text);
        
        // Find first occurrence of any query term
        $firstPos = strlen($text);
        foreach ($queryTerms as $term) {
            $pos = stripos($text, $term);
            if ($pos !== false && $pos < $firstPos) {
                $firstPos = $pos;
            }
        }
        
        if ($firstPos === strlen($text)) {
            $firstPos = 0;
        }
        
        // Calculate start position for preview
        $start = max(0, $firstPos - 50);
        
        // Extract preview
        $preview = substr($text, $start, $maxLength);
        
        if ($start > 0) {
            $preview = '...' . $preview;
        }
        
        if (strlen($text) > $start + $maxLength) {
            $preview .= '...';
        }
        
        // Highlight query terms
        foreach ($queryTerms as $term) {
            $preview = preg_replace('/(' . preg_quote($term) . ')/i', '<mark>$1</mark>', $preview);
        }
        
        return $preview;
    }
    
    /**
     * Index single record
     */
    private function indexRecord($table, $record, $fields)
    {
        $searchableText = [];
        
        foreach ($fields as $field) {
            if (isset($record[$field]) && !empty($record[$field])) {
                $searchableText[] = strip_tags((string)$record[$field]);
            }
        }
        
        $text = implode(' ', $searchableText);
        
        // Load existing index
        $indexFile = $this->indexPath . $table . '_index.json';
        $index = [];
        
        if (file_exists($indexFile)) {
            $index = json_decode(file_get_contents($indexFile), true) ?: [];
        }
        
        // Add/update record
        $index[$record['id']] = $text;
        
        // Save index
        file_put_contents($indexFile, json_encode($index, JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Clear table index
     */
    private function clearIndex($table)
    {
        $indexFile = $this->indexPath . $table . '_index.json';
        if (file_exists($indexFile)) {
            unlink($indexFile);
        }
    }
    
    /**
     * Save index metadata
     */
    private function saveIndexMetadata($table, $recordCount)
    {
        $metadata = [
            'table' => $table,
            'record_count' => $recordCount,
            'built_at' => date('Y-m-d H:i:s'),
            'fields' => $this->searchableFields[$table]
        ];
        
        $metadataFile = $this->indexPath . $table . '_metadata.json';
        file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get index statistics
     */
    public function getIndexStats()
    {
        $stats = [];
        $totalSize = 0;
        
        foreach (array_keys($this->searchableFields) as $table) {
            $indexFile = $this->indexPath . $table . '_index.json';
            $metadataFile = $this->indexPath . $table . '_metadata.json';
            
            $tableStats = [
                'indexed' => file_exists($indexFile),
                'size' => 0,
                'records' => 0,
                'last_built' => null
            ];
            
            if (file_exists($indexFile)) {
                $tableStats['size'] = filesize($indexFile);
                $totalSize += $tableStats['size'];
                
                $index = json_decode(file_get_contents($indexFile), true);
                $tableStats['records'] = count($index);
            }
            
            if (file_exists($metadataFile)) {
                $metadata = json_decode(file_get_contents($metadataFile), true);
                $tableStats['last_built'] = $metadata['built_at'];
            }
            
            $stats[$table] = $tableStats;
        }
        
        $stats['total_size'] = $totalSize;
        $stats['total_size_mb'] = round($totalSize / 1024 / 1024, 2);
        
        return $stats;
    }
    
    /**
     * Rebuild all indexes
     */
    public function rebuildAllIndexes()
    {
        $results = [];
        
        foreach (array_keys($this->searchableFields) as $table) {
            try {
                $indexed = $this->buildIndex($table);
                $results[$table] = ['status' => 'success', 'records' => $indexed];
            } catch (\Exception $e) {
                $results[$table] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }
        
        return $results;
    }
    
    /**
     * Auto-complete suggestions
     */
    public function getAutocompleteSuggestions($query, $table = 'kontrak', $limit = 10)
    {
        $indexFile = $this->indexPath . $table . '_index.json';
        
        if (!file_exists($indexFile)) {
            return [];
        }
        
        $index = json_decode(file_get_contents($indexFile), true);
        $suggestions = [];
        $query = strtolower(trim($query));
        
        foreach ($index as $recordId => $text) {
            $words = explode(' ', strtolower($text));
            
            foreach ($words as $word) {
                if (strlen($word) >= 3 && strpos($word, $query) === 0) {
                    if (!in_array($word, $suggestions)) {
                        $suggestions[] = $word;
                    }
                }
            }
        }
        
        sort($suggestions);
        return array_slice($suggestions, 0, $limit);
    }
    
    /**
     * Update single record in index
     */
    public function updateRecord($table, $recordId, $data)
    {
        if (!isset($this->searchableFields[$table])) {
            return false;
        }
        
        $fields = $this->searchableFields[$table];
        
        // Load existing index
        $indexFile = $this->indexPath . $table . '_index.json';
        $index = [];
        
        if (file_exists($indexFile)) {
            $index = json_decode(file_get_contents($indexFile), true) ?: [];
        }
        
        // Build searchable text
        $searchableText = [];
        foreach ($fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $searchableText[] = strip_tags((string)$data[$field]);
            }
        }
        
        $index[$recordId] = implode(' ', $searchableText);
        
        // Save updated index
        file_put_contents($indexFile, json_encode($index, JSON_UNESCAPED_UNICODE));
        
        return true;
    }
    
    /**
     * Remove record from index
     */
    public function removeRecord($table, $recordId)
    {
        $indexFile = $this->indexPath . $table . '_index.json';
        
        if (!file_exists($indexFile)) {
            return false;
        }
        
        $index = json_decode(file_get_contents($indexFile), true) ?: [];
        
        if (isset($index[$recordId])) {
            unset($index[$recordId]);
            file_put_contents($indexFile, json_encode($index, JSON_UNESCAPED_UNICODE));
            return true;
        }
        
        return false;
    }
}