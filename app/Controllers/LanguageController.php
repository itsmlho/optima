<?php

namespace App\Controllers;

/**
 * Language Controller
 * 
 * Handles language switching functionality for OPTIMA system
 * Supports Indonesian (id) and English (en)
 * 
 * @package App\Controllers
 * @author OPTIMA Development Team
 * @version 1.0.0
 */
class LanguageController extends BaseController
{
    /**
     * Supported languages
     * 
     * @var array
     */
    private $supportedLanguages = ['id', 'en'];

    /**
     * Switch Language
     * 
     * Changes the active language and stores preference in session
     * 
     * @param string|null $locale Language code (id or en)
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function switch($locale = null)
    {
        // Validate locale parameter
        if (!$locale || !in_array($locale, $this->supportedLanguages)) {
            return redirect()->back()->with('error', 'Invalid language selection');
        }

        // Store language preference in session
        session()->set('user_language', $locale);
        
        // Set locale for current request
        service('request')->setLocale($locale);
        
        // Also set the global locale for lang() helper
        service('language')->setLocale($locale);
        
        // Log language change for audit
        log_message('info', 'User changed language to: ' . $locale . ' (IP: ' . $this->request->getIPAddress() . ')');
        
        // Success message based on selected language
        $successMessage = $locale === 'id' 
            ? 'Bahasa berhasil diubah ke Bahasa Indonesia' 
            : 'Language successfully changed to English';
        
        // Redirect back to previous page with success message
        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Get Current Language
     * 
     * Returns current active language code
     * Useful for AJAX requests
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getCurrent()
    {
        $appConfig = config('App');
        $currentLang = session()->get('user_language') ?? $appConfig->defaultLocale ?? 'en';
        
        return $this->response->setJSON([
            'status' => 'success',
            'language' => $currentLang,
            'supported_languages' => $this->supportedLanguages
        ]);
    }

    /**
     * Get Available Languages
     * 
     * Returns list of all supported languages with labels
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getAvailable()
    {
        $languages = [
            [
                'code' => 'id',
                'name' => 'Bahasa Indonesia',
                'flag' => '🇮🇩',
                'short' => 'ID'
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'flag' => '🇬🇧',
                'short' => 'EN'
            ]
        ];
        
        return $this->response->setJSON([
            'status' => 'success',
            'languages' => $languages
        ]);
    }
}
