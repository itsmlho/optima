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
        $appConfig = config('App');
        $defaultLocale = $appConfig->defaultLocale ?? 'en';
        $supportedLocales = $appConfig->supportedLocales ?? ['en', 'id'];

        // Get language preference from session
        $userLanguage = session()->get('user_language');

        // If no session preference, use app default (English) and persist to session
        if (!$userLanguage) {
            $userLanguage = $defaultLocale;
            session()->set('user_language', $userLanguage);
        }

        // Validate language is supported; fallback to default locale
        if (!in_array($userLanguage, $supportedLocales)) {
            $userLanguage = $defaultLocale;
            session()->set('user_language', $userLanguage);
        }

        // Set locale for current request and for lang() helper
        service('request')->setLocale($userLanguage);
        \Config\Services::language()->setLocale($userLanguage);
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
