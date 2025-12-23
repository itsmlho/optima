<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Language Filter
 * 
 * Sets the locale based on user session preference
 * Runs before every request to ensure correct language is used
 */
class LanguageFilter implements FilterInterface
{
    /**
     * Before filter - Set locale from session
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get language preference from session
        $userLanguage = session()->get('user_language');
        
        // If user has language preference in session, use it
        if ($userLanguage) {
            // Validate language is supported
            $supportedLanguages = ['id', 'en'];
            if (in_array($userLanguage, $supportedLanguages)) {
                // Set locale using the proper CI4 method
                service('request')->setLocale($userLanguage);
                
                // Set locale in language service for lang() helper
                \Config\Services::language()->setLocale($userLanguage);
            }
        }
        
        // No need to return modified request
    }

    /**
     * After filter - No action needed
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
