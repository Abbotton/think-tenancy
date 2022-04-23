<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy;

use InvalidArgumentException;
use think\App;
use think\migration\Creator as BaseCreator;

class Creator extends BaseCreator
{
    private $path = '';

    public function __construct(App $app, $path)
    {
        $this->path = $path;
        parent::__construct($app);
    }

    protected function ensureDirectory()
    {
        $path = $this->path ?: config('tenancy.migration_path');

        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            throw new InvalidArgumentException(sprintf('directory "%s" does not exist', $path));
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(sprintf('directory "%s" is not writable', $path));
        }

        return $path;
    }
}
