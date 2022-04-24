<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use think\App;
use think\tenancy\Service;

class TestCase extends BaseTestCase
{
    protected $app;

    protected function setUp(): void
    {
        $this->app = new App();
        $configFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'tenancy.php';
        $this->app->config->load($configFile);
        $this->app->config->set([
            'central_domains' => [
                '127.0.0.1',
                'localhost',
            ],
        ]);
        $this->app->bind('tenancy', Service::class);
        parent::setUp();
    }
}
