<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\events\contracts;

use think\tenancy\Tenancy;

abstract class TenancyEvent
{
    /** @var Tenancy */
    public $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }
}
