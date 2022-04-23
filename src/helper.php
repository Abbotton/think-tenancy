<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

if (!function_exists('tenancy')) {
    /**
     * @return object|\think\App
     */
    function tenancy()
    {
        return app(\think\tenancy\Tenancy::class);
    }
}
