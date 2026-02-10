<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Batch Update Expired Contracts Command
 * 
 * Usage:
 *   php spark contracts:update-expired
 * 
 * For automated execution, add to crontab:
 *   0 2 * * * cd /path/to/optima && php spark contracts:update-expired
 */
class UpdateExpiredContracts extends BaseCommand
{
    protected $group       = 'Marketing';
    protected $name        = 'contracts:update-expired';
    protected $description = 'Update expired contracts to EXPIRED status and return units';
    protected $usage       = 'contracts:update-expired';

    public function run(array $params)
    {
        CLI::write('=================================', 'cyan');
        CLI::write('Batch Update Expired Contracts', 'cyan');
        CLI::write('=================================', 'cyan');
        CLI::newLine();

        CLI::write("Checking for expired contracts...", 'yellow');
        CLI::newLine();

        try {
            // Call the BatchContractOperations controller method
            $controller = new \App\Controllers\BatchContractOperations();
            $response = $controller->updateExpiredContracts();
            
            // Parse response
            $result = json_decode($response->getBody(), true);

            if ($result['success']) {
                CLI::write('✓ Batch update completed successfully', 'green');
                CLI::newLine();
                
                CLI::write('Contracts checked: ' . ($result['contracts_checked'] ?? 0), 'white');
                CLI::write('Contracts updated: ' . ($result['contracts_updated'] ?? 0), 'white');
                CLI::write('Units returned: ' . ($result['units_updated'] ?? 0), 'white');
                
                if (!empty($result['errors'])) {
                    CLI::newLine();
                    CLI::write('Errors encountered: ' . count($result['errors']), 'yellow');
                    foreach ($result['errors'] as $error) {
                        CLI::write('  - Contract ' . $error['contract_number'] . ': ' . $error['error'], 'red');
                    }
                }

                CLI::newLine();
                CLI::write('=================================', 'cyan');
                return EXIT_SUCCESS;
                
            } else {
                CLI::error('✗ Batch update failed: ' . ($result['message'] ?? 'Unknown error'));
                return EXIT_ERROR;
            }

        } catch (\Exception $e) {
            CLI::error('✗ Error: ' . $e->getMessage());
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString());
            return EXIT_ERROR;
        }
    }
}
