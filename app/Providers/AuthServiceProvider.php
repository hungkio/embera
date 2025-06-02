<?php

namespace App\Providers;

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\Models\Admin;
use App\Domain\Admin\Policies\AdminPolicy;
use App\Domain\Acl\Policies\RolePolicy;
use App\Domain\Banner\Models\Banner;
use App\Domain\Banner\Policies\BannerPolicy;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Policies\ContactPolicy;
use App\Domain\LogSearch\Models\LogSearch;
use App\Domain\LogSearch\Policies\LogSearchPolicy;
use App\Domain\MailSetting\Models\MailSetting;
use App\Domain\MailSetting\Policies\MailSettingPolicy;
use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Policies\LogActivityPolicy;
use App\Domain\Menu\Policies\MenuPolicy;
use App\Domain\Page\Models\Page;
use App\Domain\Page\Policies\PagePolicy;
use App\Domain\Post\Models\Post;
use App\Domain\Post\Policies\PostPolicy;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Domain\SubscribeEmail\Policies\SubscribeEmailPolicy;
use App\Domain\Taxonomy\Policies\TaxonPolicy;
use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;
use App\Domain\Taxonomy\Policies\TaxonomyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Admin::class => AdminPolicy::class,
        Taxon::class => TaxonPolicy::class,
        Taxonomy::class => TaxonomyPolicy::class,
        Banner::class => BannerPolicy::class,
        Page::class => PagePolicy::class,
        Post::class => PostPolicy::class,
        Menu::class => MenuPolicy::class,
        Activity::class => LogActivityPolicy::class,
        Contact::class => ContactPolicy::class,
        LogSearch::class => LogSearchPolicy::class,
        MailSetting::class => MailSettingPolicy::class,
        SubscribeEmail::class => SubscribeEmailPolicy::class,
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

//        Gate::define('settings', function ($user) {
//            return $user->isAdmin;
//        });

        Gate::before(function (Admin $admin, $ability) {
            return $admin->email == config('ecc.admin_email') || $admin->hasRole('superadmin') ? true : null;
        });
    }
}
