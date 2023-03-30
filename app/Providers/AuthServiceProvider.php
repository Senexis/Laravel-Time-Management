<?php

namespace App\Providers;

use App\Project;
use App\TimeEntry;
use App\User;
use App\UserLocation;
use App\WorkType;

use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\TimeEntryPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserLocationPolicy;
use App\Policies\WorkTypePolicy;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Role::class => RolePolicy::class,
        TimeEntry::class => TimeEntryPolicy::class,
        UserLocation::class => UserLocationPolicy::class,
        User::class => UserPolicy::class,
        WorkType::class => WorkTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
