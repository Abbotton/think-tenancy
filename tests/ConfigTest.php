<?php

namespace think\tenancy\tests;

class ConfigTest extends TestCase
{
    public function test_load_config()
    {
        $configFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'tenancy.php';
        $this->assertFileExists($configFile);
        $this->assertIsArray($this->app->config->get('central_domains'));
        $this->assertTrue($this->app->config->has('database'));
    }
}