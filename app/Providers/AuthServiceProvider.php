<?php

namespace App\Providers;

use App\Policies\ConversationPolicy;
use App\Policies\WebsitePolicy;
use App\Tenant\Conversation;
use App\Website;
use Auth;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Website::class => WebsitePolicy::class,
        Conversation::class => ConversationPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    { 
        $this->registerPolicies($gate);

        $gate->define('edit-user', function ($user, $model) {
            if ($user->hasAnyRole(['super','admin']) 
                || ($user->is_country_manager && $user->country_id = $model->country_id ) 
                || $user->id == $model->id) {
                return true;
            }
        });
    }
}
