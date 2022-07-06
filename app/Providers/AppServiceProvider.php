<?php

namespace App\Providers;

use App\Models\Settings\Language;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View()->composer('settings.languages.switcher', function ($view) {

            $available_locales = [];

            $backend_languages = Language::getBackendLangs();
            foreach ($backend_languages as $backend_language) {
                $available_locales[$backend_language->name] = $backend_language->code;
            }

            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', $available_locales);
        });

        View::composer('layouts.app', function ($view) {

            $language = Language::getLangByCode(app()->getLocale());
            $view->with('language', $language);
            $view->with('taxonomies', \App\Models\Definitions\Taxonomy::allLang());
        });
    }
}
