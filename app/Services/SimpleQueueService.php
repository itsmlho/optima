<?php

namespace App\Services;

/**
 * ============================================================================
 * SIMPLE FILE-BASED QUEUE SYSTEM - 100% GRATIS
 * ============================================================================
 * Perfect untuk shared hosting tanpa Redis/external dependencies
 * ============================================================================
 */
class SimpleQueueService
{
    protected $queuePath;
    protected $maxJobsPerExecution = 5; // Limit untuk shared hosting
    protected $jobTimeout = 30; // 30 seconds max per job
    
    public function __construct()
    {
        $this->queuePath = WRITEPATH . 'queue/';
        
        // Create queue directory if not exists
        if (!is_dir($this->queuePath)) {
            mkdir($this->queuePath, 0755, true);
        }
    }
    
    /**
     * Add job to queue
     */
    public function push(string $jobClass, array $data = [], int $delay = 0): string
    {
        $jobId = uniqid('job_', true);
        $executeAt = time() + $delay;
        
        $jobData = [
            'id' => $jobId,
            'job' => $jobClass,
            'data' => $data,
            'created_at' => time(),
            'execute_at' => $executeAt,
            'attempts' => 0,
            'max_attempts' => 3,
            'status' => 'pending'
        ];
        
        $filename = $this->queuePath . $executeAt . '_' . $jobId . '.json';
        file_put_contents($filename, json_encode($jobData, JSON_PRETTY_PRINT));
        
        log_message('info', "Job queued: {$jobClass} with ID {$jobId}");
        
        return $jobId;
    }
    
    /**
     * Process pending jobs (call this from cron or manual trigger)
     */
    public function work(): array
    {
        $processed = [];
        $files = glob($this->queuePath . '*.json');
        
        if (empty($files)) {
            return ['message' => 'No jobs in queue'];
        }
        
        // Sort by execute time (oldest first)
        sort($files);
        $files = array_slice($files, 0, $this->maxJobsPerExecution);
        
        foreach ($files as $file) {
            $jobData = json_decode(file_get_contents($file), true);
            
            if (!$jobData) {
                unlink($file);
                continue;
            }
            
            // Check if job is ready to execute
            if ($jobData['execute_at'] > time()) {
                continue; // Not yet time to execute
            }
            
            $result = $this->executeJob($jobData, $file);
            $processed[] = $result;
        }
        
        return $processed;
    }
    
    /**
     * Execute a single job
     */
    protected function executeJob(array $jobData, string $filePath): array
    {
        $startTime = time();
        
        try {
            log_message('info', "Executing job: {$jobData['job']} (ID: {$jobData['id']})");
            
            // Load job class
            if (!class_exists($jobData['job'])) {
                throw new \Exception("Job class {$jobData['job']} not found");
            }
            
            $jobInstance = new $jobData['job']();
            
            // Check if job has handle method
            if (!method_exists($jobInstance, 'handle')) {
                throw new \Exception("Job class must have handle() method");
            }
            
            // Execute job with timeout protection
            set_time_limit($this->jobTimeout);
            $result = $jobInstance->handle($jobData['data']);
            
            // Job completed successfully
            unlink($filePath);
            
            log_message('info', "Job completed: {$jobData['job']} in " . (time() - $startTime) . " seconds");
            
            return [
                'job_id' => $jobData['id'],
                'job_class' => $jobData['job'],
                'status' => 'completed',
                'execution_time' => time() - $startTime,
                'result' => $result
            ];
            
        } catch (\Exception $e) {
            return $this->handleJobFailure($jobData, $filePath, $e);
        }
    }
    
    /**
     * Handle job failure
     */
    protected function handleJobFailure(array $jobData, string $filePath, \Exception $e): array
    {
        $jobData['attempts']++;
        $jobData['last_error'] = $e->getMessage();
        
        log_message('error', "Job failed: {$jobData['job']} - {$e->getMessage()}");
        
        if ($jobData['attempts'] >= $jobData['max_attempts']) {
            // Max attempts reached - move to failed jobs
            $failedPath = WRITEPATH . 'queue/failed/';
            if (!is_dir($failedPath)) {
                mkdir($failedPath, 0755, true);
            }
            
            $failedFile = $failedPath . 'failed_' . $jobData['id'] . '.json';
            $jobData['status'] = 'failed';
            $jobData['failed_at'] = time();
            
            file_put_contents($failedFile, json_encode($jobData, JSON_PRETTY_PRINT));
            unlink($filePath);
            
            return [
                'job_id' => $jobData['id'],
                'job_class' => $jobData['job'],
                'status' => 'failed',
                'error' => $e->getMessage(),
                'attempts' => $jobData['attempts']
            ];
        } else {
            // Retry with exponential backoff
            $delay = pow(2, $jobData['attempts']) * 60; // 2^attempts minutes
            $jobData['execute_at'] = time() + $delay;
            $jobData['status'] = 'retrying';
            
            // Create new file with updated execute time
            $newFile = $this->queuePath . $jobData['execute_at'] . '_retry_' . $jobData['id'] . '.json';
            file_put_contents($newFile, json_encode($jobData, JSON_PRETTY_PRINT));
            unlink($filePath);
            
            return [
                'job_id' => $jobData['id'],
                'job_class' => $jobData['job'],
                'status' => 'retrying',
                'attempt' => $jobData['attempts'],
                'retry_at' => date('Y-m-d H:i:s', $jobData['execute_at'])
            ];
        }
    }
    
    /**
     * Get queue statistics
     */
    public function getStats(): array
    {
        $pending = count(glob($this->queuePath . '*.json'));
        $failed = count(glob($this->queuePath . 'failed/*.json'));
        
        return [
            'pending_jobs' => $pending,
            'failed_jobs' => $failed,
            'queue_size' => $this->formatBytes($this->getDirectorySize($this->queuePath))
        ];
    }
    
    /**
     * Clean old failed jobs (older than 7 days)
     */
    public function cleanFailedJobs(): int
    {
        $failedPath = $this->queuePath . 'failed/';
        $files = glob($failedPath . '*.json');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < (time() - (7 * 24 * 60 * 60))) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    private function getDirectorySize(string $path): int
    {
        $size = 0;
        $files = glob($path . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
            } elseif (is_dir($file)) {
                $size += $this->getDirectorySize($file . '/');
            }
        }
        
        return $size;
    }
    
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}