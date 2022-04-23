<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\services;

use think\tenancy\model\Tenant;

class ResetService
{
    public static function reset(Tenant $tenant): void
    {
        // 重新配置数据库连接。
        self::resetDatabase($tenant);
        // 重写缓存配置
        if (config('tenancy.reset_cache')) {
            self::resetCache();
        }
        // 重写文件系统配置
        if (config('tenancy.reset_filesystem')) {
            self::resetFilesystem();
        }
        // 重写session配置
        if (config('tenancy.reset_session')) {
            self::resetSession();
        }
        // 重写cookie配置
        if (config('tenancy.reset_cookie')) {
            self::resetCookie();
        }
        // 重写日志配置
        if (config('tenancy.reset_log')) {
            self::resetLog();
        }
        // 重写视图配置
        if (config('tenancy.reset_view')) {
            self::resetView();
        }
    }

    /**
     * 重置数据库配置.
     *
     * @param $tenant
     */
    public static function resetDatabase($tenant): void
    {
        $connect = config('tenancy.database.tenant_connect');
        if ($connect == config('database.default')) {
            return;
        }
        $database = config('database');
        $database['default'] = $connect;
        $database['connections'][$connect]['database'] = $tenant[config('tenancy.database.db_name_field')];
        $database['connections'][$connect]['username'] = $tenant[config('tenancy.database.db_username_field')];
        $database['connections'][$connect]['password'] = $tenant[config('tenancy.database.db_password_field')];
        config(['database' => $database]);
    }

    /**
     * 重写缓存配置.
     */
    public static function resetCache(): void
    {
        self::overwriteConfig('cache', 'stores', config('tenancy.overwrite_cache_config'));
    }

    /**
     * 重写配置.
     *
     * @param $firstKey
     * @param $secondKey
     * @param $configArray
     */
    public static function overwriteConfig($firstKey, $secondKey = '', $configArray = []): void
    {
        $config = config($firstKey);
        foreach ($configArray as $key => &$item) {
            if (isset($config[$secondKey][$key])) {
                if ($secondKey) {
                    $config[$secondKey][$key] = array_merge($config[$secondKey][$key], $item);
                } else {
                    $config[$key] = array_merge($config[$key], $item);
                }
            }
        }
        config([$firstKey => $config]);
    }

    /**
     * 重置文件系统配置.
     */
    public static function resetFilesystem(): void
    {
        self::overwriteConfig('filesystem', 'disks', config('tenancy.overwrite_filesystem_config'));
    }

    /**
     * 重写session配置.
     */
    public static function resetSession(): void
    {
        self::overwriteConfig('session', '', config('tenancy.overwrite_session_config'));
    }

    /**
     * 重写cookie配置.
     */
    public static function resetCookie(): void
    {
        self::overwriteConfig('cookie', '', config('tenancy.overwrite_cookie_config'));
    }

    /**
     * 重写日志配置.
     */
    public static function resetLog(): void
    {
        self::overwriteConfig('log', '', config('tenancy.overwrite_log_config'));
    }

    /**
     * 重写view配置.
     */
    public static function resetView(): void
    {
        self::overwriteConfig('view', '', config('tenancy.overwrite_view_config'));
    }
}
