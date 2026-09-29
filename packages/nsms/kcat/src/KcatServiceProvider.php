<?php

namespace Nsms\Kcat;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class KcatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kcat.php', 'kcat');
    }

    public function boot(): void
    {
        $base = dirname(__DIR__);
        $this->loadMigrationsFrom($base.'/database/migrations');
        $this->loadViewsFrom($base.'/resources/views', 'kcat');
        Blade::anonymousComponentPath($base.'/resources/views/components', 'kcat');
        $this->app['router']->aliasMiddleware('kcat.permission', Http\Middleware\PermissionMiddleware::class);
        foreach (require $base.'/config/abilities.php' as $ability) {
            Gate::define('kcat.'.$ability, static function ($user) use ($ability): bool {
                if (config('kcat.self_service', false) && in_array($ability, ['attempt_kcat_test', 'attempt_adaptive_kcat'], true)) {
                    return true;
                }
                if (in_array($user->role, config('kcat.manager_roles', []), true)) {
                    return ! in_array($ability, ['attempt_kcat_test', 'attempt_adaptive_kcat'], true);
                }
                if (! Schema::hasTable('kcat_access')) {
                    return false;
                }
                $role = DB::table('kcat_access')->where('user_id', $user->getAuthIdentifier())->value('role');
                return match ($role) {
                    'counselor' => ! in_array($ability, ['attempt_kcat_test', 'attempt_adaptive_kcat'], true),
                    'principal' => in_array($ability, ['view_all_kcat_reports', 'view_kcat_reports', 'print_kcat_reports',
                        'view_kcat_stream_recommendations', 'view_kcat_question_analytics', 'view_kcat_question_quality'], true),
                    'student' => in_array($ability, ['attempt_kcat_test', 'attempt_adaptive_kcat'], true),
                    default => false,
                };
            });
        }
        $this->loadRoutesFrom($base.'/routes/web.php');
        $this->publishes([$base.'/public' => public_path('vendor/kcat')], 'kcat-assets');
        $this->publishes([$base.'/config/kcat.php' => config_path('kcat.php')], 'kcat-config');
        if ($this->app->runningInConsole()) {
            $this->commands([Console\Commands\GenerateKcatQuestionBank::class,
                Console\Commands\GrantAccess::class, Console\Commands\LinkStudent::class]);
        }
    }
}
