<?php

namespace App\Controllers;

/**
 * Language Debug Controller
 * 
 * For debugging multilanguage issues
 */
class LanguageDebug extends BaseController
{
    public function index()
    {
        // Force clear session first
        if ($this->request->getGet('clear')) {
            session()->destroy();
            return redirect()->to('/language-debug');
        }
        
        // Get all locale settings
        $requestLocale = $this->request->getLocale();
        $languageServiceLocale = service('language')->getLocale();
        $sessionLanguage = session()->get('user_language');
        $appConfig = config('App');
        $configDefault = $appConfig->defaultLocale ?? 'id';
        $configSupported = $appConfig->supportedLocales ?? ['id', 'en'];
        
        // Test direct translations
        $translations = [
            'id' => [
                'Dashboard.total_units' => 'Total Unit',
                'Dashboard.active_contracts' => 'Kontrak Aktif',
                'Dashboard.from_last_month' => 'dari bulan lalu',
            ],
            'en' => [
                'Dashboard.total_units' => 'Total Units',
                'Dashboard.active_contracts' => 'Active Contracts',
                'Dashboard.from_last_month' => 'from last month',
            ]
        ];
        
        // Test lang() helper
        $currentTranslations = [
            'Dashboard.total_units' => lang('Dashboard.total_units'),
            'Dashboard.active_contracts' => lang('Dashboard.active_contracts'),
            'Dashboard.from_last_month' => lang('Dashboard.from_last_month'),
            'Auth.login' => lang('Auth.login'),
            'App.welcome' => lang('App.welcome'),
        ];
        
        $data = [
            'title' => 'Language Debug',
            'request_locale' => $requestLocale,
            'language_service_locale' => $languageServiceLocale,
            'session_language' => $sessionLanguage,
            'config_default' => $configDefault,
            'config_supported' => $configSupported,
            'expected_translations' => $translations,
            'current_translations' => $currentTranslations,
        ];
        
        return view('language_debug', $data);
    }
    
    public function setManual($locale = 'id')
    {
        if (!in_array($locale, ['id', 'en'])) {
            return redirect()->back()->with('error', 'Invalid locale');
        }
        
        // Clear session first
        session()->remove('user_language');
        
        // Set new language
        session()->set('user_language', $locale);
        
        // Set both locales
        $this->request->setLocale($locale);
        service('language')->setLocale($locale);
        
        return redirect()->to('/language-debug')->with('success', 'Language set to: ' . $locale);
    }
}
