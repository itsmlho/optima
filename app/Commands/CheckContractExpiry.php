<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Contract Expiry Notification Command
 * 
 * Usage:
 *   php spark contracts:check-expiry
 *   php spark contracts:check-expiry 30
 *   php spark contracts:check-expiry --days=60
 * 
 * For automated execution, add to crontab:
 *   0 9 * * * cd /path/to/optima && php spark contracts:check-expiry
 */
class CheckContractExpiry extends BaseCommand
{
    protected $group       = 'Marketing';
    protected $name        = 'contracts:check-expiry';
    protected $description = 'Check for expiring contracts and send notifications';
    protected $usage       = 'contracts:check-expiry [days]';
    protected $arguments   = [
        'days' => 'Number of days before expiry to check (default: 30)'
    ];
    protected $options     = [
        '--days' => 'Number of days before expiry to check (alternative to argument)'
    ];

    public function run(array $params)
    {
        CLI::write('=================================', 'cyan');
        CLI::write('Contract Expiry Notification Check', 'cyan');
        CLI::write('=================================', 'cyan');
        CLI::newLine();

        // Get days parameter
        $days = $params[0] ?? $this->getOption('days') ?? 30;
        $days = (int) $days;

        if ($days < 1 || $days > 365) {
            CLI::error('Invalid days value. Must be between 1 and 365.');
            return EXIT_ERROR;
        }

        CLI::write("Checking for contracts expiring in {$days} days...", 'yellow');
        CLI::newLine();

        try {
            // Call the ContractNotifications controller method
            $controller = new \App\Controllers\ContractNotifications();
            $response = $controller->checkExpiringContracts($days);
            
            // Parse response
            $result = json_decode($response->getBody(), true);

            if ($result['success']) {
                CLI::write('✓ Check completed successfully', 'green');
                CLI::newLine();
                
                CLI::write('Contracts checked: ' . ($result['contracts_checked'] ?? 0), 'white');
                CLI::write('Notifications sent: ' . ($result['notifications_sent'] ?? 0), 'white');
                
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
                CLI::error('✗ Check failed: ' . ($result['message'] ?? 'Unknown error'));
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
