<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

/**
 * CsrfController
 *
 * Provides a CSRF token refresh endpoint for AJAX-heavy pages.
 * When session-based CSRF token becomes stale (e.g. session regeneration
 * race condition), the client calls GET /csrf-refresh to receive a fresh
 * token, updates its meta tag, then retries the original failed request.
 *
 * Route: GET /csrf-refresh  (protected by auth filter, no csrf filter)
 */
class CsrfController extends ResourceController
{
    /**
     * Return the current session's CSRF token name and value.
     * The auth filter ensures only authenticated users can call this.
     * Because this is a GET request it bypasses CI4's CSRF filter.
     */
    public function refresh()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('AJAX only', 400);
        }

        return $this->respond([
            'success'    => true,
            'tokenName'  => csrf_token(),
            'tokenValue' => csrf_hash(),
        ]);
    }
}
