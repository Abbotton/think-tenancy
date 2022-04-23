<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\commands;

use InvalidArgumentException;
use RuntimeException;
use think\console\Input;
use think\console\input\Argument as InputArgument;
use think\console\Output;
use think\migration\command\migrate\Create as BaseMigrate;
use think\tenancy\Creator;

class MigrateCreate extends BaseMigrate
{
    private $path = '';

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

    protected function configure(): void
    {
        $this->setName('tenants:migrate:create')
            ->setDescription('多租户创建新的迁移文件')
            ->addArgument('name', InputArgument::REQUIRED, 'What is the name of the migration?')
            ->addArgument('path', InputArgument::OPTIONAL, '指定迁移文件存放目录')
            ->setHelp(sprintf('%sCreates a new database migration%s', PHP_EOL, PHP_EOL));
    }

    /**
     * Create the new migration.
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function execute(Input $input, Output $output): void
    {
        $className = $input->getArgument('name');
        $this->path = $input->getArgument('path');

        $path = (new Creator($this->app, $this->path))->create($className);

        $output->writeln('<info>created</info> .'.str_replace(getcwd(), '', realpath($path)));
    }
}
