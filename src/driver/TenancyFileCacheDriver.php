<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\driver;

use think\App;
use think\cache\driver\File;
use think\contract\CacheHandlerInterface;

class TenancyFileCacheDriver extends File implements CacheHandlerInterface
{
    public function __construct(App $app)
    {
        $subdomain = request()->subDomain();
        $path = $subdomain
            ? $app->getRuntimePath().'cache'.\DIRECTORY_SEPARATOR.'tenants'.\DIRECTORY_SEPARATOR.$subdomain.\DIRECTORY_SEPARATOR
            : '';
        $options = [
            'path' => $path,
            'prefix' => $subdomain,
            'tag_prefix' => 'tag:'.$subdomain.':',
        ];

        parent::__construct($app, $options);
    }
}
