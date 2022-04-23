<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\model;

use think\Model;
use think\tenancy\events;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $autoWriteTimestamp = true;

    public static function onAfterInsert(self $model): void
    {
        event(new events\TenantCreated($model));
    }

    public static function onAfterUpdate(self $model): void
    {
        event(new events\TenantUpdated($model));
    }

    public static function onAfterDelete(self $model): void
    {
        event(new events\TenantDeleted($model));
    }

    public static function onBeforeInsert(self $model): void
    {
        event(new events\CreatingTenant($model));
    }

    public static function onBeforeUpdate(self $model): void
    {
        event(new events\UpdatingTenant($model));
    }

    public static function onBeforeDelete(self $model): void
    {
        event(new events\DeletingTenant($model));
    }
}
