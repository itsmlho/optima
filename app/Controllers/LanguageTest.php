<?php

namespace App\Controllers;

/**
 * Language Test Controller
 * 
 * For testing multi-language implementation
 * 
 * @package App\Controllers
 */
class LanguageTest extends BaseController
{
    /**
     * Test page for language switching
     * 
     * @return string
     */
    public function index()
    {
        // Get current language settings
        $currentLocale = service('request')->getLocale();
        $sessionLanguage = session()->get('user_language');
        $languageServiceLocale = service('language')->getLocale();
        $appConfig = config('App');
        $configDefault = $appConfig->defaultLocale ?? 'id';
        
        // Test translations
        $testTranslations = [
            'app_name' => lang('App.app_name'),
            'welcome' => lang('App.welcome'),
            'total_units' => lang('Dashboard.total_units'),
            'active_contracts' => lang('Dashboard.active_contracts'),
            'from_last_month' => lang('Dashboard.from_last_month'),
            'login' => lang('Auth.login'),
            'logout' => lang('Auth.logout'),
        ];
        
        $data = [
            'title' => 'Language System Test',
            'current_locale' => $currentLocale,
            'session_language' => $sessionLanguage,
            'language_service_locale' => $languageServiceLocale,
            'config_default' => $configDefault,
            'test_translations' => $testTranslations,
            'supported_languages' => config('App')->supportedLocales ?? ['id', 'en'],
        ];
        
        return view('test_language', $data);
    }
}
