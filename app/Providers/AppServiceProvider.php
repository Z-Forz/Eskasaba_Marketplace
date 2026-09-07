<?php

namespace App\Providers;

use App\Models\WebsiteSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->shareWebsiteSettings();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Share website settings with all views.
     */
    protected function shareWebsiteSettings(): void
    {
        View::composer('*', function () {
            $defaults = [
                'favicon' => null,
                'website_name' => null,
                'logo' => null,
                'hero_image' => null,
                'about' => null,
                'vision' => null,
                'mission' => null,
                'address' => null,
                'email' => null,
                'phone' => null,
                'instagram' => null,
                'facebook' => null,
                'tiktok' => null,
                'copyright' => null,
            ];

            $settingsArray = array_merge($defaults, WebsiteSetting::allSettings());
            $settings = (object) $settingsArray;

            View::share('settings', $settings);
        });
    }
}
