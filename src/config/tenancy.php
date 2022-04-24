<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

use think\tenancy\model\Tenant;

return [
    // 主域名配置，所有主域名都不会走多租户逻辑。
    'central_domains' => [
        '127.0.0.1',
        'localhost',
        str_replace(['http://', 'https://'], '', config('app.app_host')),
    ],
    // 数据库相关配置
    'database' => [
        // 租户数据库连接
        'tenant_connect' => 'tenant',
        // 租户子域名字段
        'sub_domain_field' => 'sub_domain',
        // 租户数据库名称字段
        'db_name_field' => 'db_name',
        // 租户数据库用户名字段
        'db_username_field' => 'db_user',
        // 租户数据库密码字段
        'db_password_field' => 'db_password',
        // 租户状态字段
        'status_field' => 'status',
        // 租户过期时间字段
        'expired_field' => 'expired_at',
    ],
    // 数据迁移目录配置
    'migration_path' => root_path().'database'.DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.'tenants',
    // 数据填充目录
    'seed_path' => root_path().'database'.DIRECTORY_SEPARATOR.'seeds'.DIRECTORY_SEPARATOR.'tenants',
    // 租户模型
    'tenant_model' => Tenant::class,
    // 是否重写文件系统配置
    'reset_filesystem' => true,
    // 新的文件系统配置
    'overwrite_filesystem_config' => [
        'public' => [
            'root' => public_path().'storage'.DIRECTORY_SEPARATOR.request()->subDomain(),
            'url' => '/storage/'.request()->subDomain(),
        ],
    ],
    // 是否重写缓存配置
    'reset_cache' => true,
    /*
     * 新的缓存配置
     * TODO 此处实现的不完美，如果缓存驱动为`file`，那么请先修改下方的配置，
     * 然后手动修改`config/cache.php`中的`stores['file']['type']`的值为`\think\tenancy\driver\TenancyFileCacheDriver::class`。
     * 其他缓存驱动还没有测试，您可以参考`\think\tenancy\driver\TenancyFileCacheDriver::class`自行处理。
     */
    'overwrite_cache_config' => [
        'file' => [
            'path' => runtime_path().'cache'.DIRECTORY_SEPARATOR.'tenants'.request()->subDomain(),
            'prefix' => request()->subDomain(),
            'tag_prefix' => 'tag:'.request()->subDomain().':',
        ],
    ],
    // 是否重写session配置
    'reset_session' => true,
    // 新的session配置
    'overwrite_session_config' => [
        'prefix' => '',
    ],
    // 是否重写cookie配置
    'reset_cookie' => true,
    // 新的cookie配置
    'overwrite_cookie_config' => [
        'domain' => '',
        'path' => '',
    ],
    // 是否重写日志配置
    'reset_log' => true,
    // 新的日志配置
    'overwrite_log_config' => [
        'file' => [
            'path' => '',
        ],
    ],
    // 是否重写视图配置
    'reset_view' => true,
    // 新的视图配置
    'overwrite_view_config' => [
        'view_dir_name' => 'view',
    ],
];
