<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy;

use think\Exception;
use think\Model;
use think\tenancy\events\EndingTenancy;
use think\tenancy\events\InitializingTenancy;
use think\tenancy\events\TenancyEnded;
use think\tenancy\events\TenancyInitialized;
use think\tenancy\model\Tenant;

class Tenancy
{
    /** @var null|Model|Tenant */
    public $tenant;

    /** @var bool */
    public $initialized = false;

    private $subDomainField;

    public function __construct()
    {
        $this->subDomainField = config('tenancy.database.sub_domain_field') ?: 'sub_domain';
    }

    /**
     * Initializes the tenant.
     *
     * @param $tenant
     *
     * @throws Exception
     */
    public function initialize($tenant): void
    {
        if (!\is_object($tenant)) {
            $subdomain = $tenant;
            $tenant = $this->find($subdomain);
            if (!$tenant) {
                throw new Exception('未找到相关资源', 404);
            }
        }

        if ($this->initialized && $this->tenant[$this->subDomainField] === $tenant[$this->subDomainField]) {
            return;
        }
        if ($this->initialized) {
            $this->end();
        }

        $this->tenant = $tenant;

        event(new InitializingTenancy($this));

        $this->initialized = true;

        event(new TenancyInitialized($this));
    }

    public function find($subdomain = ''): ?Tenant
    {
        return $this->model()->where($this->subDomainField, $subdomain ?? request()->subDomain())->find();
    }

    /** @return Model|Tenant */
    public function model()
    {
        $class = config('tenancy.tenant_model');

        return new $class();
    }

    public function end(): void
    {
        event(new EndingTenancy($this));

        if (!$this->initialized) {
            return;
        }

        event(new TenancyEnded($this));

        $this->initialized = false;

        $this->tenant = null;
    }
}
