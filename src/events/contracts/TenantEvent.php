<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\events\contracts;

use think\tenancy\model\Tenant;

abstract class TenantEvent
{
    /** @var Tenant */
    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
}
