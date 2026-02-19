<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Jobs\InvoiceAutomationJob;

/**
 * Invoice Automation CLI Command
 * 
 * This command runs the invoice automation job to generate invoices
 * for delivery instructions that are 30 days past completion.
 * 
 * Usage: php spark jobs:invoice-automation
 * 
 * This command should be run daily via cron job
 */
class InvoiceAutomation extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Jobs';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'jobs:invoice-automation';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Auto-generate invoices for DIs that are 30 days past completion';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'jobs:invoice-automation [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--dry-run' => 'Show what would be generated without actually creating invoices',
        '--verbose' => 'Show detailed output'
    ];

    /**
     * Run the command
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('═══════════════════════════════════════════════════', 'blue');
        CLI::write('        INVOICE AUTOMATION JOB', 'blue');
        CLI::write('═══════════════════════════════════════════════════', 'blue');
        CLI::newLine();
        
        CLI::write('Start Time: ' . date('Y-m-d H:i:s'), 'yellow');
        CLI::newLine();
        
        $dryRun = CLI::getOption('dry-run');
        $verbose = CLI::getOption('verbose');
        
        if ($dryRun) {
            CLI::write('⚠️  DRY RUN MODE - No invoices will be generated', 'yellow');
            CLI::newLine();
        }
        
        try {
            // Initialize job
            $job = new InvoiceAutomationJob();
            
            if ($dryRun) {
                // In dry run, just show what would be generated
                $this->dryRunPreview($job);
            } else {
                // Run actual job
                $result = $job->run();
                
                CLI::newLine();
                CLI::write('═══════════════════════════════════════════════════', 'blue');
                CLI::write('        JOB COMPLETED', 'blue');
                CLI::write('═══════════════════════════════════════════════════', 'blue');
                CLI::newLine();
                
                if ($result['success']) {
                    CLI::write("✅ Job completed successfully!", 'green');
                    CLI::write("   Invoices generated: {$result['generated']}", 'green');
                    
                    if ($result['errors'] > 0) {
                        CLI::write("   ⚠️  Errors encountered: {$result['errors']}", 'yellow');
                        CLI::write("   Check logs for details: writable/logs/", 'yellow');
                    }
                } else {
                    CLI::write("❌ Job failed!", 'red');
                }
            }
            
        } catch (\Exception $e) {
            CLI::newLine();
            CLI::error('❌ ERROR: ' . $e->getMessage());
            CLI::error('   Trace: ' . $e->getTraceAsString());
            return EXIT_ERROR;
        }
        
        CLI::newLine();
        CLI::write('End Time: ' . date('Y-m-d H:i:s'), 'yellow');
        CLI::newLine();
        
        return EXIT_SUCCESS;
    }
    
    /**
     * Preview what would be generated in dry run mode
     */
    protected function dryRunPreview($job)
    {
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($job);
        $method = $reflection->getMethod('getEligibleDeliveryInstructions');
        $method->setAccessible(true);
        
        $eligibleDIs = $method->invoke($job);
        
        CLI::write("Found " . count($eligibleDIs) . " DIs eligible for invoice generation:", 'cyan');
        CLI::newLine();
        
        if (count($eligibleDIs) > 0) {
            // Display each DI in a simple format
            foreach ($eligibleDIs as $index => $di) {
                $completedDate = $di['completed_at'] ?? $di['sampai_tanggal_approve'] ?? 'N/A';
                if ($completedDate !== 'N/A') {
                    $completedTimestamp = strtotime($completedDate);
                    $daysPassed = floor((time() - $completedTimestamp) / (60 * 60 * 24));
                    $completedFormatted = date('Y-m-d', $completedTimestamp);
                } else {
                    $daysPassed = 'N/A';
                    $completedFormatted = 'N/A';
                }
                
                CLI::write(($index + 1) . ". DI #" . ($di['id'] ?? 'N/A'), 'white');
                CLI::write("   Customer: " . ($di['customer_name'] ?? 'N/A'), 'light_gray');
                CLI::write("   Contract: " . ($di['contract_number'] ?? 'N/A'), 'light_gray');
                CLI::write("   Completed: " . $completedFormatted . " (" . $daysPassed . " days ago)", 'light_gray');
                CLI::newLine();
            }
        } else {
            CLI::write('   No DIs found that need invoice generation.', 'yellow');
        }
        
        CLI::newLine();
        CLI::write('💡 Run without --dry-run to generate invoices', 'cyan');
    }
}
