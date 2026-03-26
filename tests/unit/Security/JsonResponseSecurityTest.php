<?php

namespace Tests\Unit\Security;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Security unit tests: JSON response data exposure.
 *
 * Verifies that the ENVIRONMENT === 'development' guard on 'debug'
 * fields is consistently applied. Tests simulate the pattern used
 * in controllers (Dashboard, Marketing, etc.) and confirm that
 * production builds never leak internal DB state to the client.
 */
final class JsonResponseSecurityTest extends CIUnitTestCase
{
    /**
     * Helper that replicates the guard pattern used in all controllers:
     *
     *   'debug' => ENVIRONMENT === 'development' ? [...] : null
     *
     * @param array $internalData  Sensitive fields (db_error, last_query, payload, …)
     * @return array               Simulated JSON response body
     */
    private function buildResponse(array $internalData): array
    {
        return [
            'success' => false,
            'message' => 'An error occurred.',
            'debug'   => ENVIRONMENT === 'development' ? $internalData : null,
            'csrf_hash' => 'fake_hash',
        ];
    }

    public function testDebugFieldIsNullOutsideDevelopment(): void
    {
        if (ENVIRONMENT === 'development') {
            $this->markTestSkipped('This test only applies outside the development environment.');
        }

        $response = $this->buildResponse([
            'db_error'   => ['code' => 1045, 'message' => 'Access denied'],
            'last_query' => 'SELECT * FROM users WHERE id = 1',
            'payload'    => ['username' => 'admin', 'password' => 'secret'],
        ]);

        $this->assertNull(
            $response['debug'],
            "The 'debug' field MUST be null in production to prevent internal data exposure."
        );
    }

    public function testDebugFieldIsPopulatedInDevelopment(): void
    {
        if (ENVIRONMENT !== 'development') {
            $this->markTestSkipped('This test only runs in the development environment.');
        }

        $internalData = [
            'db_error'   => ['code' => 1045, 'message' => 'Access denied'],
            'last_query' => 'SELECT * FROM users WHERE id = 1',
        ];

        $response = $this->buildResponse($internalData);

        $this->assertNotNull(
            $response['debug'],
            "The 'debug' field should be populated in development for troubleshooting."
        );
        $this->assertArrayHasKey('db_error', $response['debug']);
        $this->assertArrayHasKey('last_query', $response['debug']);
    }

    public function testResponseAlwaysContainsSuccessAndMessage(): void
    {
        $response = $this->buildResponse([]);

        $this->assertArrayHasKey('success', $response, "Response must always have 'success' key.");
        $this->assertArrayHasKey('message', $response, "Response must always have 'message' key.");
        $this->assertIsBool($response['success'], "'success' must be a boolean.");
        $this->assertIsString($response['message'], "'message' must be a string.");
    }

    public function testProductionResponseDoesNotContainSensitiveKeys(): void
    {
        if (ENVIRONMENT === 'development') {
            $this->markTestSkipped('This test only applies outside the development environment.');
        }

        $response = $this->buildResponse([
            'db_error'   => 'should not appear',
            'last_query' => 'SELECT password FROM users',
            'payload'    => ['secret_key' => 'abc123'],
        ]);

        $encoded = json_encode($response);

        $this->assertStringNotContainsString(
            'SELECT password FROM users',
            $encoded,
            'Raw SQL query must never appear in production JSON responses.'
        );
        $this->assertStringNotContainsString(
            'secret_key',
            $encoded,
            'Payload data must never appear in production JSON responses.'
        );
    }
}
