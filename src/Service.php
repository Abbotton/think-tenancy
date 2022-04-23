<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy;

use think\Exception;
use think\Service as BaseService;
use think\tenancy\services\ResetService;

class Service extends BaseService
{
    public function register(): void
    {
        $this->app->make(Tenancy::class);
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            if (\in_array($this->app->request->host(true), config('tenancy.central_domains'), true)) {
                return;
            }
            $subdomain = $this->app->request->subDomain();
            if ($subdomain) {
                $tenant = tenancy()->find($subdomain);
                $this->validateTenancy($tenant);
                ResetService::reset($tenant);
            }
        } else {
            $this->commands([
                commands\MigrateCreate::class,
                commands\MigrateRun::class,
                commands\MigrateRollback::class,
                commands\MigrateStatus::class,
                commands\SeedRun::class,
                commands\SeedCreate::class,
                commands\Publish::class,
            ]);
        }
    }

    private function validateTenancy($tenant): void
    {
        if (!$tenant) {
            throw new Exception('未找到相关资源', 404);
        }

        if (!$tenant[config('tenancy.database.status_field')]) {
            throw new Exception('当前租户已暂停使用', 403);
        }

        if ($tenant[config('tenancy.database.expired_field')] <= time()) {
            throw new Exception('当前租户已过期', 403);
        }
    }
}
