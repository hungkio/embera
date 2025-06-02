<?php

namespace App\Providers;

use App\Domain\Admin\Models\Admin;
use App\Support\ValuesStore\Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Flash\Flash;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'admins' => Admin::class,
        ]);

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);

        Blade::component('admin.components.formButton', 'form-button');
        Blade::component('admin.components.textField', 'text-field');
        Blade::component('admin.components.selectField', 'select-field');
        Blade::component('admin.components.textareaField', 'textarea-field');
        Blade::component('admin.components.pageHeader', 'page-header');
        Blade::component('admin.components.checkField', 'check-field');
        Blade::component('admin.components.card', 'card');
        Blade::component('admin.components.collapse-card', 'collapse-card');

        $this->app->singleton(Setting::class, function () {
            return Setting::make(storage_path('app/settings.json'));
        });

//        URL::forceScheme('https');

        $this->setupMailConfig();
    }

    public function setupMailConfig()
    {
        $this->app['config']->set('mail.driver', 'smtp');
        $this->app['config']->set('mail.from.address', setting('mail_from_address'));
        $this->app['config']->set('mail.from.name', setting('mail_from_name'));
        $this->app['config']->set('mail.host', setting('mail_host'));
        $this->app['config']->set('mail.port', setting('mail_port'));
        $this->app['config']->set('mail.username', setting('mail_username'));
        $this->app['config']->set('mail.password', setting('mail_password'));
        $this->app['config']->set('mail.encryption', setting('mail_encryption'));
    }
}
